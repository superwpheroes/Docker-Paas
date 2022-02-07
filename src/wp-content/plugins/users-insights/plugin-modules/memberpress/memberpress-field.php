<?php

class USIN_MemberPress_Field{

	protected $config;
	protected $prefix;
	protected $module_id;
	

	public function __construct($config, $prefix, $module_id){
		$this->config = $config;
		$this->prefix = $prefix;
		$this->module_id = $module_id;
		$this->setup();
	}


	protected function setup(){
		$this->meta_key = $this->get_config('field_key');
		$this->id = $this->prefix.$this->meta_key;
		$this->name = $this->get_config('field_name');
		$this->mepr_type = $this->get_config('field_type');
		$this->type = $this->get_usin_field_type();
	}


	protected function get_config($key, $default = ''){
		if(isset($this->config[$key])){
			return $this->config[$key];
		}
		return $default;
	}


	/**
	 * Generates the field options in a Users Insights field options format.
	 * @return array the Users Insights field options for this MemberPress field
	 */
	public function to_usin_field(){
		$field = array(
			'id' => $this->id,
			'meta_key' => $this->meta_key,
			'name' => $this->name,
			'order' => $this->stores_serialized_data() ? false : 'ASC',
			'show' => false,
			'fieldType' => 'general',
			'filter' => array(
				'type' => $this->type
			),
			'module' => $this->module_id
		);
		
		if($this->is_option_field()){
			//this is a field with an option to select, such as a select or radio field
			$field['filter']['options'] = $this->get_field_options_list();
		}

		if($this->stores_serialized_data()){
			//remove the "is/is not set" operators for the serialized filters, as they
			//store an empty array when no fields are submitted, which is technically
			//a value stored for this field
			$field['filter']['disallow_null'] = true;
		}
		
		return $field;
		
	}


	public function get_usin_field_type(){
		switch ($this->mepr_type) {
			case 'dropdown':
			case 'radios':
				return 'select';
			case 'checkboxes':
			case 'multiselect':
				return 'serialized_multioption';
			case 'date':
				return 'date';
			default:
				return 'text';
		}
	}


	public function stores_serialized_data(){
		return $this->type == 'serialized_multioption';
	}


	public function is_option_field(){
		$opt_fields = array('select', 'serialized_multioption');
		return in_array($this->type, $opt_fields);
	}


	public function get_field_options_list(){
		if(empty($this->config['options']) || !is_array($this->config['options'])){
			return array();
		}

		$option_list = array();

		foreach ($this->config['options'] as $option ) {
			if(isset($option['option_value']) && isset($option['option_name'])){
				$option_list[]=array('key' => $option['option_value'], 'val' => $option['option_name']);
			}
		}

		return $option_list;
	}


	public function format_value($value){

		if($this->type == 'select'){
			return $this->get_field_value_by_key($value);
		}

		if($this->type == 'serialized_multioption'){
			$value = maybe_unserialize($value);
			//checkboxes store the values in the format of a:2:{s:3:"one";s:2:"on";s:3:"two";s:2:"on";}
			$values = $this->mepr_type == 'checkboxes' ? array_keys($value) : $value;
			$values = array_map(array($this, 'get_field_value_by_key'), $values);
			return implode(', ', $values);	
		}

		return $value;
	}


	public function get_field_value_by_key($key){
		//use an associative array for a faster key/value search
		$options = $this->get_options_assoc();
		if(isset($options[$key])){
			return $options[$key];
		}
		return $key;
	}


	protected function get_options_assoc(){
		if(isset($this->options_assoc)){
			return $this->options_assoc;
		}
		
		$options_assoc = array();
		foreach ($this->get_field_options_list() as $option ) {
			$options_assoc[$option['key']] = $option['val'];
		}

		$this->options_assoc = $options_assoc;
		return $this->options_assoc;
	}
	
	


}