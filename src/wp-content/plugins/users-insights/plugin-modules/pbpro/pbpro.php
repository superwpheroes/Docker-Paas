<?php

if(!defined( 'ABSPATH' )){
	exit;
}

class USIN_Pbpro extends USIN_Plugin_Module{
	
	protected $module_name = 'pbpro';
	protected $plugin_path = array('profile-builder-pro/index.php', 'profile-builder-hobbyist/index.php');
	protected $query;
	protected $option_fields = null;

	protected static $form_fields = null;

	const PREFIX = 'pbpro_';
	

	public function init(){
		add_filter('usin_user_db_data', array($this , 'format_option_fields_data'));

		$this->query = new USIN_Pbpro_Query(self::get_form_fields());
		$this->query->init();
	}

	protected function init_reports(){
		new USIN_Pbpro_Reports();
	}

	public function register_module(){
		return array(
			'id' => $this->module_name,
			'name' => 'Profile Builder Pro',
			'desc' => __('Allows you to search and filter the Profile Builder Pro user data.', 'usin'),
			'allow_deactivate' => true,
			'in_beta' => true,
			'buttons' => array(
				array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/profile-builder-pro-search-data/', 'target'=>'_blank')
			),
			'active' => false
		);
	}

	public function register_fields(){
		$fields = array();

		$form_fields = self::get_form_fields();
		foreach ($form_fields as $form_field ) {
			$fields[]= $form_field->to_usin_field();
		}
		
		return $fields;
	}

	public function format_option_fields_data($user_data){
		$option_fields = $this->get_option_fields();
		foreach ($option_fields as $field) {
			$field_id = $field->id;
			if(isset($user_data->$field_id)){
				$user_data->$field_id = $field->format_value($user_data->$field_id);
			}
		}
		return $user_data;
	}

	public static function get_form_fields(){
		if(self::$form_fields !== null){
			return self::$form_fields;
		}

		self::$form_fields = array();
		$pb_fields = get_option('wppb_manage_fields', array());
		foreach ($pb_fields as $pb_field) {
			$field = new USIN_Pbpro_Field($pb_field, 'pbpro');
			if(!$field->should_be_ignored()){
				self::$form_fields[]= $field;
			}
		}

		return self::$form_fields;
	}

	public static function get_form_field_by_id($id){
		foreach (self::get_form_fields() as $field ) {
			if($field->id === $id){
				return $field;
			}
		}
	}

	protected function get_option_fields(){
		if($this->option_fields === null){
			$this->option_fields = array_filter(self::get_form_fields(), array($this, 'is_option_field'));
		}
		return $this->option_fields;
	}

	protected function is_option_field($field){
		return $field->is_option_field();
	}

}

new USIN_Pbpro();