<?php

class USIN_Report{

	const GENERAL_GROUP = 'general';
	const BAR = 'bar';
	const PIE = 'pie';

	public $id;
	public $name;
	public $type = self::PIE;
	public $subtype = 'none';
	public $group = self::GENERAL_GROUP;
	public $filters = null;

	protected $visible = true;
	protected $loader;
	protected $public_fields = array('id', 'name', 'type', 'subtype', 'group', 'info', 'filters', 'format');


	public function __construct($id, $name, $options = array()){
		$this->id = $id;
		$this->name = $name;

		$optional_fields = array('type', 'group', 'info', 'loader_class',
			'type', 'field_id', 'filters', 'visible', 'format');

		foreach ($optional_fields as $key) {
			if(isset($options[$key])){
				$this->$key = $options[$key];
			}
		}

	}

	/**
	 * Converts the report object to an array.
	 *
	 * @return array with the public report object properties
	 */
	public function to_array(){
		$arr = array();
		foreach ($this->public_fields as $key ) {
			if(isset($this->$key)){
				$arr[$key] = $this->$key;
			}
		}
		$arr['visible'] = $this->is_visible();
		return $arr;
	}

	public function is_visible(){
		$visibility_option = USIN_Report_Options::get_visibility_option($this->id);
		if($visibility_option !== null){
			//the user has changed the default visibility for this report
			return $visibility_option;
		}
		return $this->visible;
	}

	public function has_filters(){
		if(!empty($this->filters)){
			return true;
		}
		return false;
	}

	/**
	 * Get the report data based on the passed options.
	 *
	 * @param array $options the report data options
	 * @return array with the data if successful or WP_Error on error
	 */
	public function get_data($options = array()){
		$loader_class_name = $this->get_loader_class_name();

		if(!class_exists($loader_class_name)){
			return new WP_Error('error_class_loading', "Error loading class $loader_class_name");
		}

		$loader_class = new ReflectionClass($loader_class_name);
		$loader = $loader_class->newInstance($this, $options);
		return $loader->call();
	}

	public function get_field_id(){
		if(isset($this->field_id)){
			return $this->field_id;
		}
		return $this->id;
	}

	/**
	 * Generate the class name of the loader to load based on the report ID.
	 * For example, a report with ID registered_users should have a loader
	 * with class name USIN_Registered_Users_Loader
	 *
	 * @return string the class name
	 */
	protected function get_loader_class_name(){
		if(isset($this->loader_class)){
			return $this->loader_class;
		}

		$loader_class = ucwords(str_replace('_', ' ', $this->id));
		$loader_class = str_replace(' ', '_', $loader_class);
		$loader_class = 'USIN_'.$loader_class.'_Loader';

		return $loader_class;
	}

	/**
	 * Get the group options of this report object.
	 *
	 * @return array with the group options
	 */
	protected function get_group_options(){
		foreach (self::groups() as $group ) {
			if($group['id'] == $this->group){
				return $group;
			}
		}
	}


	/**
	 * Registers all the available report groups.
	 *
	 * @return array of the groups - each group has the following keys:
	 * - id - the ID of the group
	 * - name - the name of the group
	 * - loader_path - the path of the folder that contains the Report Loaders for this
	 * group, relative to the plugin's root path
	 */
	public static function groups(){
		$groups = array(
			array('id' => self::GENERAL_GROUP, 'name' => __('General', 'usin'))
		);

		return apply_filters('usin_report_groups', $groups);
	}


}