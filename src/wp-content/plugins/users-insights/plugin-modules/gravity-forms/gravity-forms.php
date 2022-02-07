<?php

if(!defined( 'ABSPATH' )){
	exit;
}

/**
 * Gravity Forms Module:
 * - loads the user data saved via the Gravity Forms User Registration Add-on
 * - loads data and provides filters about the completed by the users forms
 */
class USIN_Gravity_Forms extends USIN_Plugin_Module{
	
	protected $module_name = 'gravityforms';
	protected $plugin_path = 'gravityforms/gravityforms.php';
	protected $prefix = 'gf_';
	protected $gfur;
	protected $is_user_reg_active = false;
	public $gf_fields = array();


	/**
	 * Initialize the main module functionality.
	 */
	public function init(){
		$this->is_user_reg_active = USIN_Helper::is_plugin_activated('gravityformsuserregistration/userregistration.php');

		$this->gf_query = new USIN_Gravity_Forms_Query();
		$this->gf_query->init();
		
		$gf_user_activity = new USIN_Gravity_Forms_User_Activity();
		$gf_user_activity->init();
		
		if($this->is_user_reg_active){
			$this->gfur = new USIN_Gravity_Forms_User_Registration($this->prefix);
			$this->gf_fields = $this->gfur->get_form_fields();
			$this->gf_query->init_meta_query($this->gf_fields, $this->prefix);
			add_filter('usin_user_db_data', array($this , 'filter_user_data'));
		}

	}

	protected function init_reports(){
		new USIN_Gravity_Forms_Reports($this);
	}
	
	/**
	 * Registers the module.
	 */
	public function register_module(){
		return array(
			'id' => $this->module_name,
			'name' => 'Gravity Forms',
			'desc' => __('Provides Gravity Forms related filters and data. Detects and displays the custom user data saved with the Gravity Forms User Registration Add-on.', 'usin'),
			'allow_deactivate' => true,
			'buttons' => array(
				array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/gravity-forms-list-search-filter-user-data/', 'target'=>'_blank')
			),
			'active' => false
		);
	}
	
	/**
	 * Registers the Gravity Form user fields
	 * @param  array $fields the default Users Insights fields
	 * @return array         the default Users Insights fields including the 
	 * Gravity Form fields
	 */
	public function register_fields(){
		$fields = array();
			
		$form_options = $this->get_form_options();

		$fields[]=array(
			'name' => __('Has completed form', 'usin'),
			'id' => 'has_completed_form',
			'order' => 'ASC',
			'show' => false,
			'hideOnTable' => true,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'select_option',
				'options' => $form_options
			),
			'module' => $this->module_name
		);

		$fields[]=array(
			'name' => __('Has not completed form', 'usin'),
			'id' => 'has_not_completed_form',
			'order' => 'ASC',
			'show' => false,
			'hideOnTable' => true,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'select_option',
				'options' => $form_options
			),
			'module' => $this->module_name
		);
		
		if($this->is_user_reg_active){
			//Gravity form user registration meta fields
			
			foreach ($this->gf_fields as $key => $field) {
				$field['id'] = $this->prefix.$field['id'];
				
				//do not add fields with existing keys
				$fields[]=array_merge(array(
					'order' => 'ASC',
					'show' => false,
					'fieldType' => 'general',
					'filter' => array(
						'type' => $field['type'],
					),
					'module' => $this->module_name
				), $field);
			}
		}

		return $fields;
	}
	
	/**
	 * Filters the user data that is loaded from the database and applied to
	 * the user when creating a new user. Formats the JSON data to a string/
	 * @param  object $data the user DB data
	 * @return object       the DB data with unserialized values
	 */
	public function filter_user_data($data){
		$json_fields = $this->gfur->get_json_fields();
		
		if(!empty($json_fields)){
			$json_keys = array_unique($json_fields);
			foreach ($json_keys as $key ) {
				$key = $this->prefix.$key;
				if(!empty($data->$key)){
					$data->$key = $this->gfur->format_json_field_data($data->$key);
				}
			}
		}
		
		return $data;
	}
	
	protected function get_form_options(){
		$form_options = array();
		if(method_exists('GFAPI', 'get_forms')){
			$forms = GFAPI::get_forms();
			if(is_array($forms)){
				foreach ($forms as $form ) {
					$form_options[]=array('key'=>$form['id'], 'val'=>$form['title']);
				}
			}
		}
		
		return $form_options;
	}

	public static function is_db_migrated(){
		$db_version = get_option('gf_db_version');
		if(!$db_version && class_exists('GFForms') &&
			property_exists('GFForms', 'version') && !empty(GFForms::$version)){
				$db_version = GFForms::$version;
		}
		if(!empty($db_version)){
			return version_compare($db_version, '2.3', '>=');
		}
		return false;
	}

	public static function get_entries_db_table_name(){
		global $wpdb;
		if(self::is_db_migrated()){
			return $wpdb->prefix.'gf_entry';
		}else{
			return $wpdb->prefix.'rg_lead';
		}
	}
	
}

new USIN_Gravity_Forms();