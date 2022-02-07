<?php

if(!defined( 'ABSPATH' )){
	exit;
}

/**
 * Includes the functionality to register the custom fields to the UsersInsights
 * default fields functionality.
 */
class USIN_Custom_Fields{
	
	protected $custom_fields;
	public $prefix = 'usin_meta_';
	
	public function __construct(){
		$this->init();
	}
	
	/**
	 * Inits the main functionality and registers the required hooks.
	 */
	public function init(){
		add_filter('usin_fields', array($this , 'register_fields'));
		add_filter('usin_users_raw_data', array($this, 'format_select_field_values'));
		$this->register_fields_query();
	}
	
	/**
	 * Registers the fields to the UsersInsights users table, so that they are 
	 * available in the table and filters.
	 * @param  array $fields the default UsersInsights fields
	 * @return array         the default UsersInsights fields including the 
	 * custom user meta fields.
	 */
	public function register_fields($fields){
		$custom_fields = $this->get_custom_fields();
		
		if(!empty($custom_fields) && !empty($fields) && is_array($fields)){
			foreach ($custom_fields as $custom_field) {
				$order = $custom_field['type'] == 'text' ? 'ASC' : 'DESC';
				
				$field = array(
					'name' => $custom_field['name'],
					'id' => $this->prefix.$custom_field['key'],
					'order' => $order,
					'show' => false,
					'fieldType' => 'general',
					'filter' => array(
						'type' => $custom_field['type']
					),
					'icon' => 'custom-field'
				);
				
				if($custom_field['type'] != 'date'){
					$field['editable'] = array(
						'id' =>  $custom_field['key'],
						'location' => 'meta'
					);
				}

				if($custom_field['type'] == 'select'){
					$field['filter']['options'] = self::field_options_to_select_options($custom_field['options']);
				}
				
				$fields[]= $field;
			}
		}
		return $fields;
	}

	/**
	 * Registers a meta query for the custom user meta fields. This meta query will
	 * be responsible of generating the required database statements.
	 */
	protected function register_fields_query(){
		$custom_fields = $this->get_custom_fields();
		foreach ($custom_fields as $custom_field) {
			$query = new USIN_Meta_Query($custom_field['key'], $custom_field['type'], $this->prefix);
			$query->init();
		}
	}
	
	/**
	 * Retrieves the registered custom fields.
	 * @return array array containing the refgistered custom fields
	 */
	protected function get_custom_fields(){
		if(empty($this->custom_fields)){
			$this->custom_fields = USIN_Custom_Fields_Options::get_saved_fields();
		}
		return $this->custom_fields;
	}

	/**
	 * Convert a text of options to an array of options that can be used in filters
	 * and editing elements.
	 * 
	 * The following text:
	 *   Red Color
	 *   Green Color
	 * will be converted to:
	 *  array(
	 * 	  array(key => Red Color, value => Red Color),
	 * 	  array(key => Green Color, value => Green Color)
	 * )
	 * 
	 * 	And the following text:
	 *   red : Red Color
	 *   green : Green Color
	 * will be converted to:
	 *  array(
	 * 	  array(key => red, value => Red Color),
	 * 	  array(key => green, value => Green Color)
	 * )
	 *
	 * @param [type] $options_text
	 * @return void
	 */
	public static function field_options_to_select_options($options_text){
		$items = explode(PHP_EOL, $options_text);
		$options = array();

		foreach ($items as $item) {
			$trimmed = trim($item);
			if(empty($trimmed)){
				continue;
			}
			if(strpos($item, ':') === false){
				$key = $item;
				$val = $item;
			}else{
				$pieces = explode(':', $item, 2);
				$key = $pieces[0];
				$val = $pieces[1];
			}

			$options[]=array('key' => trim($key), 'val' => trim($val));
		}

		return $options;
	}

	public function format_select_field_values($data){
		if(empty($data)){
			return $data;
		}
		$visible_select_fields = array_intersect( $this->get_select_fields_ids(), array_keys((array)$data[0]));
		
		if(empty($visible_select_fields)){
			return $data;
		}

		foreach ($data as $user_data) {
			foreach ($visible_select_fields as $field ) {
				if(!empty($user_data->$field)){
					$user_data->$field = $this->field_option_key_to_val($user_data->$field, $field);
				}
			}
		}
		
		return $data;
	}

	protected function field_option_key_to_val($key, $field_id){
		$field = usin_options()->get_field_by_id($field_id);
		if(empty($field)){
			return $key;
		}

		foreach ($field['filter']['options'] as $option ) {
			if($option['key'] == $key){
				return $option['val'];
			}
		}

		return $key;
	}

	protected function get_select_fields_ids(){
		$select_fields = usin_options()->get_field_ids_by_filter_type('select');
		$select_fields = array_filter($select_fields, array($this, 'is_custom_field_key'));
		return $select_fields;
	}

	public function is_custom_field_key($key){
		return strpos($key, $this->prefix) === 0;
	}
	
}