<?php

class USIN_Pbpro_Field{

	public $id;
	public $name;
	public $meta_key;
	public $type;
	public $pb_type;

	protected $config;
	protected $module_id;
	protected $options = null;

	public function __construct($config, $module_id){
		$this->config = $config;
		$this->module_id = $module_id;
		$this->setup();
	}

	protected function setup(){
		$this->meta_key = $this->get_config('meta-name');
		$this->id = USIN_Pbpro::PREFIX.$this->meta_key;
		$this->name = $this->get_config('field-title');
		$this->pb_type = $this->get_config('field');
		$this->type = $this->get_usin_field_type();
	}

	public function to_usin_field(){
		
		$field = array(
			'id' => $this->id,
			'meta_key' => $this->meta_key,
			'name' => $this->name,
			'order' => $this->is_option_field() ? false : 'ASC',
			'show' => false,
			'fieldType' => 'general',
			'filter' => array(
				'type' => $this->type
			),
			'module' => $this->module_id
		);
		
		
		if($this->is_option_field()){
			//this is a field with an option to select, such as a select or radio field
			$field['filter']['options'] = $this->get_filter_options();
		}

		if($this->is_date_field()){
			$field['filter']['disallow_null'] = true;
		}

		
		return $field;
		
	}


	protected function get_usin_field_type(){
		if($this->is_date_field()){
			return 'date_custom';
		}

		switch ($this->pb_type) {
			case 'Number':
				return 'number';
			case 'Select':
			case 'Select (Country)':
			case 'Select (Timezone)':
			case 'Select (Currency)':
			case 'Select (CPT)':
			case 'Select (User Role)':
			case 'Radio':
				return 'select';
			case 'Select (Multiple)':
			case 'Checkbox':
				return 'comma_multioption';
			default:
				return 'text';
		}
	}

	public function is_option_field(){
		return in_array($this->type, array('select', 'comma_multioption'));
	}

	public function should_be_ignored(){
		if(empty($this->meta_key) || empty($this->pb_type)){
			return true;
		}
		if(strpos($this->pb_type, 'Default -') === 0){
			//this is one of the default fields that Users Insights already supports
			return true;
		}

		$ignore_fields = apply_filters('usin_ignore_pb_field_types',
			array('Heading','Input (Hidden)','WYSIWYG','HTML','Upload','Avatar','Validation','Map','reCAPTCHA', 'Repeater'));

		if(in_array($this->pb_type, $ignore_fields)){
			return true;
		}

		return false;
	}

	protected function get_filter_options(){
		return USIN_Helper::assoc_array_to_multidim($this->get_options());
	}

	public function get_options(){
		if($this->options !== null){
			//returned the cached options
			return $this->options;
		}
		$this->options = array();

		$options_str = trim($this->get_config('options'));
		$labels_str = trim($this->get_config('labels'));

		$keys = empty($options_str) ? array() :  array_map('trim', explode(',', $options_str));
		$vals = empty($labels_str) ? array() : array_map('trim', explode(',', $labels_str));

		foreach ($keys as $i => $key) {
			$this->options[$key] = isset($vals[$i]) ? $vals[$i] : $key;
		}

		return $this->options;
	}

	public function format_value($value){
		if($value === '' || $value === null){
			return $value;
		}

		if($this->type == 'select'){
			$options = $this->get_options();
			$val = trim($value);
			if(isset($options[$val])){
				return $options[$val];
			}
		}elseif($this->type == 'comma_multioption'){
			$options = $this->get_options();
			$vals = array_map('trim', explode(',', $value));
			$formatted = array();
			foreach ($vals as $val) {
				$formatted[]= isset($options[$val]) ? $options[$val] : $val;
			}
			return implode(', ', $formatted);
		}
	

		return $value;
	}


	protected function get_config($key, $default = ''){
		if(isset($this->config[$key])){
			return $this->config[$key];
		}
		return $default;
	}

	public function is_date_field(){
		return $this->pb_type == 'Datepicker' && $this->get_date_format() !== null;
	}

	public function get_date_format(){
		$formats = array(
			'mm/dd/yy' => '%m/%d/%Y',
			'mm/yy/dd' => '%m/%Y/%d',
			'dd/yy/mm' => '%d/%Y/%m',
			'dd/mm/yy' => '%d/%m/%Y',
			'yy/dd/mm' => '%Y/%d/%m',
			'yy/mm/dd' => '%Y/%m/%d',
			'yy-mm-dd' => '%Y-%m-%d',
			'DD, dd-M-y' => '%W, %d-%b-%y',
			'D, dd M yy' => '%a, %d %b %Y',
			'D, d M y' => '%a, %e %b %y',
			'D, d M yy' => '%a, %e %b %Y',
			'mm-dd-yy' => '%m-%d-%Y'
		);
		$formats = apply_filters('usin_pbpro_date_formats', $formats);

		$pb_format = $this->get_config('date-format');
		if(isset($formats[$pb_format])){
			return $formats[$pb_format];
		}
		return null;
	}


}