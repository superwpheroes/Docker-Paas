<?php

class USIN_Privacy_Eraser{

	protected static $instance;

	protected $export_data = array();
	protected $user = null;

	protected function __construct(){
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_data_eraser' ) );
	}

	/**
	 * Register a data exporter that exports the Users Insights data
	 *
	 * @param array $exporters an array with the registered exporters
	 * @return array the exporters containing the Users Insights exporter
	 */
	public function register_data_eraser($erasers){
		$erasers['users_insights_eraser'] = array(
			'eraser_friendly_name' => __( 'Users Insights Data', 'usin' ),
			'callback'               => array($this, 'erase_data')
		);
		return $erasers;
	}

	/**
	 * This is a singleton class, returns the instance of the class.
	 * @return USIN_Modules the instance
	 */
	public static function init(){
		if(! self::$instance ){
			self::$instance = new USIN_Privacy_Eraser();
		}
		return self::$instance;
	}

	public function erase_data($email_address, $page){
		$this->response = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		$this->user = get_user_by( 'email', $email_address );

		if ( ! $this->user instanceof WP_User ) {
			return $this->response;
		}

		$this->handle_geolocation_data();
		$this->handle_device_data();
		$this->handle_activity_data();
		$this->handle_groups();
		$this->handle_notes();
		$this->handle_custom_fields();
		$this->handle_page_visits();

		return $this->response;
	}


	protected function handle_geolocation_data(){
		$field_ids = array('country', 'region', 'city', 'coordinates');
		$this->handle_usin_data('geolocation', $field_ids);

		// remove the geolocation hashed IP
		if($this->should_erase_element('geolocation')){
			$ip = get_user_meta($this->user->ID, 'usin_ip', true);
			if($ip !== ''){
				$success = delete_user_meta($this->user->ID, 'usin_ip');
				$this->set_remove_status(__('IP Address (hashed)', 'usin'), $success);
			}
		}
	}

	protected function handle_device_data(){
		$field_ids = array('browser', 'browser_version', 'platform');
		$this->handle_usin_data('device', $field_ids);
	}

	protected function handle_activity_data(){
		$field_ids = array('last_seen', 'sessions');
		$this->handle_usin_data('last_seen_sessions', $field_ids);
	}

	protected function handle_groups(){
		$group_ids = USIN_Groups::get_user_groups($this->user->ID);

		if(empty($group_ids)){
			return;
		}

		$item_name = __('User Groups', 'usin');

		if($this->should_erase_element('groups')){
			USIN_Groups::delete_all_user_groups($this->user->ID);
			$this->set_item_removed($item_name);
		}else{
			$this->set_item_retained($item_name);
		}

	}


	protected function handle_notes(){
		$notes = USIN_Note::get_all($this->user->ID);

		if(empty($notes)){
			return;
		}

		if($this->should_erase_element('notes')){
			foreach ($notes as $note) {
				$item_name = sprintf(__('Note #%s', 'usin'), $note->id);
				$success = $note->delete();
				$this->set_remove_status($item_name, $success);
			}
		}else{
			$item_name = sprintf('%d %s', sizeof($notes), _n( 'note', 'notes', sizeof($notes), 'usin' ));
			$this->set_item_retained($item_name);
		}
	}

	protected function handle_page_visits(){
		$pages_visited_count = USIN_Visit_Tracking_Data::count($this->user->ID);
		
		if(!$pages_visited_count){
			return;
		}

		$item_name = $pages_visited_count .' '. _n('tacked page visit', 'tracked page visits', $pages_visited_count, 'usin');

		if($this->should_erase_element('visits')){
			$success = USIN_Visit_Tracking_Data::delete($this->user->ID);
			$this->set_remove_status($item_name, $success);
		}else{
			$this->set_item_retained($item_name);
		}

	}

	protected function handle_custom_fields(){
		$custom_fields = USIN_Custom_Fields_Options::get_saved_fields();
		$exclude_custom_fields = apply_filters('usin_personal_data_erase_exclude_custom_fields', array());
		
		foreach ($custom_fields as $field ) {
			//this field should not be erased
			if(in_array($field['key'], $exclude_custom_fields)){
				continue;
			}

			//date fields are read only, which means that they are not controlled by Users Insights
			//the plugin/code that updates these fields is responsible for deleting their data
			if($field['type'] == 'date'){
				continue;
			}

			$value = get_user_meta($this->user->ID, $field['key'], true);
			if($value !== ''){
				$name = sprintf(__('Custom field %s', 'usin'), $field['name']);
				//there is a value for this field stored
				if($this->should_erase_element('custom_fields')){
					$success = delete_user_meta($this->user->ID, $field['key']);
					$this->set_remove_status($name, $success);
				}else{
					$this->set_item_retained($name);
				}
			}

		}
	}


	protected function handle_usin_data($element, $field_ids){
		$usin_data = $this->get_usin_data();
		$fields_to_remove = array();
		$fields_to_retain = array();

		foreach ($field_ids as $field_id) {
			if($usin_data->$field_id !== null){  //handle only the fields that have some data set
				if($this->should_erase_element($element)){
					$fields_to_remove[]=$field_id;
				}else{
					$fields_to_retain[]=$field_id;
				}
			}
		}

		//remove the fields to be erased
		$this->remove_usin_fields($fields_to_remove);

		//mark the fields to be retained as retained
		foreach ($fields_to_retain as $field_id ) {
			$name = USIN_Privacy::get_field_name($field_id);
			$this->set_item_retained($name);
		}
	}


	protected function remove_usin_fields($field_ids){
		// make the fields in the format field_id => null
		$values = array_fill(0, sizeof($field_ids), null);
		$update_fields = array_combine($field_ids, $values);

		$ud = new USIN_User_Data($this->user->ID);
		$success = $ud->save_array($update_fields);

		foreach ($field_ids as $field_id ) {
			$name = USIN_Privacy::get_field_name($field_id);
			$this->set_remove_status($name, $success);
		}
	}

	protected function set_remove_status($name, $success){
		$method_name = $success ? 'set_item_removed' : 'set_item_remove_error';
		$method = new ReflectionMethod('USIN_Privacy_Eraser', $method_name);
		$method->invoke($this, $name);
	}

	public function set_item_removed($name){
		$message = sprintf('Users Insights: %s %s', __('Removed', 'usin'), $name);
		$this->response['messages'][]=$message;
		$this->response['items_removed'] = true;
	}

	public function set_item_retained($name){
		$message = sprintf('Users Insights: <strong>%s</strong> %s', __('Retained', 'usin'), $name);
		$this->response['messages'][]=$message;
		$this->response['items_retained'] = true;
	}

	public function set_item_remove_error($name){
		$message = sprintf('<span style="color:#ff0000">Users Insights: %s %s</span>', __('Error removing', 'usin'), $name);
		$this->response['messages'][]=$message;
		$this->response['items_retained'] = true;
	}


	/**
	 * Retrieves the Users Insights data of the current user.
	 *
	 * @return object the Users Insights data
	 */
	protected function get_usin_data(){
		if(!isset($this->usin_data)){
			$ud = new USIN_User_Data($this->user->ID);
			$this->usin_data = $ud->get_all();
		}
		return $this->usin_data;
	}



	protected function should_erase_element($name){
		if(!isset($this->erase_elements)){
			$this->erase_elements = usin_get_module_setting('privacy', 'erase_data');
		}

		return in_array($name, $this->erase_elements);
	}

	
}