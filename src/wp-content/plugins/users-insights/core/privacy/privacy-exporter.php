<?php

class USIN_Privacy_Exporter{

	protected static $instance;

	protected $export_data = array();
	protected $user = null;

	protected function __construct(){
		add_filter( 'wp_privacy_personal_data_exporters', array($this, 'register_data_exporter') );
	}

	/**
	 * This is a singleton class, returns the instance of the class.
	 * @return USIN_Modules the instance
	 */
	public static function init(){
		if(! self::$instance ){
			self::$instance = new USIN_Privacy_Exporter();
		}
		return self::$instance;
	}


	/**
	 * Register a data exporter that exports the Users Insights data
	 *
	 * @param array $exporters an array with the registered exporters
	 * @return array the exporters containing the Users Insights exporter
	 */
	public function register_data_exporter($exporters){
		$exporters['users_insights_exporter'] = array(
			'exporter_friendly_name' => __( 'Users Insights Data', 'usin' ),
			'callback'               => array($this, 'export_data')
		);
		return $exporters;
	}


	/**
	 * Export the Users Insights data. Will export the data based on the
	 * elements selected to export in the Privacy module settings.USIN_Event_Ticket_Orders_Query
	 *
	 * @param string $email_address the email address of the user whose data will be exported
	 * @param [type] $page
	 * @return array containing the exported data
	 */
	public function export_data($email_address, $page){
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$export_elements = usin_get_module_setting('privacy', 'export_data');
		$data = array();
		
		if ( $user instanceof WP_User && is_array($export_elements)) {
			
			$this->export_data = array();
			$this->user = $user;

			foreach ($export_elements as $element) {
				switch ($element) {
					case 'geolocation':
						$this->add_geolocation_to_export();
						break;
					case 'device':
						$this->add_device_data_to_export();
						break;
					case 'last_seen_sessions':
						$this->add_activity_data_to_export();
						break;
					case 'groups':
						$this->add_user_groups_to_export();
						break;
					case 'custom_fields':
						$this->add_custom_fields_to_export();
						break;
					case 'hashed_ip':
						$this->add_hashed_ip_to_export();
						break;
				}
			}

			if(sizeof($this->export_data) > 0){
				$data[] = array(
					'group_id'    => 'users_insights',
					'group_label' => __( 'User Data', 'usin' ),
					'item_id'     => 'user',
					'data'        => $this->export_data
				);
			}

			//export notes
			if(in_array('notes', $export_elements)){
				$notes_data = $this->get_notes_data();
				if(!empty($notes_data)){
					$data[] = array(
						'group_id'    => 'users_insights_notes',
						'group_label' => __( 'Notes', 'usin' ),
						'item_id'     => 'user',
						'data'        => $notes_data
					);
				}
			}

			//export pages visited
			if(in_array('visits', $export_elements)){
				$visited_pages = $this->get_visits_data();
				if(!empty($visited_pages)){
					$data[] = array(
						'group_id'    => 'users_insights_visits',
						'group_label' => __( 'Pages Visited', 'usin' ),
						'item_id'     => 'user',
						'data'        => $visited_pages
					);
				}
			}

		}

		return array(
			'data' => $data,
			'done' => true
		);
	}


	/**
	 * Add the geolocation data to the export.
	 *
	 * @return void
	 */
	protected function add_geolocation_to_export(){
		
		$this->apply_usin_fields_to_export(array('country', 'region', 'city'));

		//coordinates is not a registered field, so we load it separately
		$usin_data = $this->get_usin_data();
		if(!empty($usin_data->coordinates)){
			$this->add_to_export_data(__('Coordinates (IP based)', 'usin'), $usin_data->coordinates);
		}
	}

	/**
	 * Add the device data to the export.
	 *
	 * @return void
	 */
	protected function add_device_data_to_export(){
		$this->apply_usin_fields_to_export(array('browser', 'browser_version', 'platform'));
	}


	/**
	 * Add the last seen & sessions data to the export.
	 *
	 * @return void
	 */
	protected function add_activity_data_to_export(){
		$this->apply_usin_fields_to_export(array('last_seen', 'sessions'));
	}


	/**
	 * Add the user groups data to the export.
	 *
	 * @return void
	 */
	protected function add_user_groups_to_export(){
		$group_ids = USIN_Groups::get_user_groups($this->user->ID);

		if(!empty($group_ids)){
			$group_names = array();

			foreach ($group_ids as $id ) {
				$group_name = USIN_Groups::get_group_name($id);
				if(!empty($group_name)){
					$group_names[]=$group_name;
				}
			}

			if(!empty($group_names)){
				$this->add_to_export_data( __('User Groups', 'usin'), implode(', ', $group_names));
			}

		}
	}


	/**
	 * Add the Users Insights custom fields to the export.
	 *
	 * @return void
	 */
	protected function add_custom_fields_to_export(){
		$custom_fields = USIN_Custom_Fields_Options::get_saved_fields();
		$exclude_custom_fields = apply_filters('usin_personal_data_export_exclude_custom_fields', array());
		
		foreach ($custom_fields as $field ) {
			if(in_array($field['key'], $exclude_custom_fields)){
				continue;
			}

			$value = get_user_meta($this->user->ID, $field['key'], true);
			if($value !== ''){
				$this->add_to_export_data( $field['name'], $value );
			}
		}
	}

	/**
	 * Add the hashed IP that is stored by the Geolocation module to the export.
	 *
	 * @return void
	 */
	protected function add_hashed_ip_to_export(){
		$ip = get_user_meta( $this->user->ID, 'usin_ip', true);
		if(!empty($ip) && $ip !== 'fail'){
			$this->add_to_export_data( __('IP Address (hashed)', 'usin'), $ip );
		}
	}

	/**
	 * Add an item to the exported data.
	 *
	 * @param string $name the name of the item
	 * @param string $value the value of the item
	 * @return void
	 */
	protected function add_to_export_data($name, $value){
		$this->export_data[]=array(
			'name' => $name,
			'value' => $value
		);
	}


	/**
	 * Adds the Users Insights fields (from the custom Users Insights table) to
	 * the export
	 *
	 * @param string $field_ids the IDs of the fields whose values to add
	 * @return void
	 */
	protected function apply_usin_fields_to_export($field_ids){
		$usin_data = $this->get_usin_data();

		foreach ($field_ids as $field_id ) {
			if(!empty($usin_data->$field_id) && $usin_data->$field_id != 'unknown'){
				$this->add_to_export_data( USIN_Privacy::get_field_name($field_id), $usin_data->$field_id);
			}
		}
	}

	/**
	 * Retrieves the notes for a user and if there are any created, 
	 * returns them in an export format.
	 *
	 * @return array the notes data organised in the export format
	 */
	protected function get_notes_data(){
		$notes = USIN_Note::get_all($this->user->ID);
		$notes_data = array();
		if(!empty($notes)){
			foreach ($notes as $note ) {
				$notes_data[]=array(
					'name' => $note->date,
					'value' => $note->content
				);
			}
		}
		return $notes_data;
	}

	/**
	 * Retrieves the tracked pages that a user has visited
	 *
	 * @return array the visits data organised in the export format
	 */
	protected function get_visits_data(){
		$pages = USIN_Visit_Tracking_Data::get($this->user->ID);
		$pages_data = array();
		if(!empty($pages)){
			foreach ($pages as $page ) {
				$pages_data[]=array(
					'name' => $page['post_type'],
					'value' => $page['title']
				);
			}
		}
		return $pages_data;
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
}