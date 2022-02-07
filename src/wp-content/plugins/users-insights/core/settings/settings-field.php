<?php

class USIN_Settings_Field{

	const TYPE_TEXT = 'text';
	const TYPE_CHECKBOXES = 'checkboxes';

	public $id;
	protected $config_args = array('name', 'default', 'options', 'desc');
	protected $export_args = array('id', 'name', 'options', 'desc', 'value', 'type');

	protected $name = '';
	protected $default = '';
	protected $value = null;
	protected $type = self::TYPE_TEXT;

	public function __construct($id, $config, $value = null){

		$this->id = $id;

		foreach ($this->config_args as $key) {
			if(isset($config[$key])){
				$this->$key = $config[$key];
			}
		}

		$this->value = $value !== null ? $value : $this->default;

	}

	public function to_array(){
		$res = array();

		foreach ($this->export_args as $key) {
			if(isset($this->$key)){
				$res[$key] = $this->$key;
			}
		}
		
		return $res;
	}

	public function get_value(){
		return $this->value;
	}

	public function set_value($value){
		$this->value = $value;
	}

}