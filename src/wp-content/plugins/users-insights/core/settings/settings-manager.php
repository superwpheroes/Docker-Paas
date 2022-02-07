<?php

class USIN_Settings_Manager{

	protected $fields = array();

	public function __construct($config, $saved_values){
		$this->init($config, $saved_values);
	}

	protected function init($config, $saved_values){
		foreach ($config as $field_id => $field_config) {
			$value = isset($saved_values[$field_id]) ? $saved_values[$field_id] : null;

			switch ($field_config['type']) {
				case 'checkboxes':
					$field = new USIN_Checkboxes_Field($field_id, $field_config, $value);
					break;
				
				default:
					$field = new USIN_Settings_Field($field_id, $field_config, $value);
					break;
			}

			$this->fields[$field_id]= $field;
		}
	}

	public function to_array(){
		$res = array();

		foreach ($this->fields as $field ) {
			$res[$field->id] = $field->to_array();
		}

		return $res;
	}

	public function get_saved_values(){
		$values = array();

		foreach ($this->fields as $field ) {
			$values[$field->id] = $field->get_value();
		}

		return $values;
	}

	public function get_field($field_id){
		if(isset($this->fields[$field_id])){
			return $this->fields[$field_id];
		}
		return null;
	}

	public function get_field_value($field_id){
		$field = $this->get_field($field_id);

		if($field){
			return $field->get_value();
		}

		return null;
	}


}