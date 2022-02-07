<?php

class USIN_Module{

	protected $prefix = 'usin_module_';
	protected $config = array();
	protected $settings = null;
	protected static $cache = array();

	public $id;
	public $license = null;
	public $active = false;
	
	
	protected function __construct($id, $config){
		$this->id = $id;
		$this->init($config);
	}

	/**
	 * Find a module by ID.
	 *
	 * @param string $id the ID of the module
	 * @return USIN_Module object
	 */
	public static function get($id, $use_cache = true){
		if($use_cache && isset(self::$cache[$id])){
			return self::$cache[$id];
		}

		$config = USIN_Module_Defaults::get_by_id($id);

		if(!empty($config)){
			$module = new USIN_Module($id, $config);
			self::$cache[$id] = $module;
			return $module;
		}
	}

	
	/**
	 * Initializes the module
	 *
	 * @param array $config config opions
	 */
	protected function init($config){
		$this->config = $config;

		$data = $this->get_module_data();

		//set active
		if(isset($data['active'])){
			$this->active = $data['active'];
		}elseif(isset($config['active_by_default'])){
			$this->active = $config['active_by_default'];
		}

		//setup the license
		if($this->requires_own_license()){
			$license_data = isset($data['license']) ? $data['license'] : array();
			$this->license = new USIN_License($license_data);
		}

		if($this->has_settings()){
			$saved_settings = isset($data['settings']) ? $data['settings'] : array();
			$this->set_settings($saved_settings);
		}

	}

	protected function set_settings($settings_values = array()){
		
		$this->settings = new USIN_Settings_Manager($this->config['settings'], $settings_values);
	}

	public function reload(){
		$this->init($this->config);
	}

	/**
	 * Converts the module object to array that can be used for storing or
	 * passed to JavaScript.
	 *
	 * @return array array presentation of the module
	 */
	public function to_array(){
		$res = array_merge(
			$this->config,
			array(
				'active' => $this->active,
				'has_options' => $this->has_options()
			));

		if($this->has_settings()){
			$res['settings'] = $this->settings->to_array();
		}

		if($this->requires_own_license()){
			$res['license'] = $this->license->to_array();
		}

		return $res;
	}

	/**
	 * Retrieves the license key of a module. If the module uses a license from another module,
	 * this other module's license is returned.
	 *
	 * @return string the license key or null if it is not set
	 */
	public function get_license_key(){
		if(isset($this->config['uses_module_license'])){
			//this module uses license from another module
			$dep_module = USIN_Module::get($this->config['uses_module_license']);
			return $dep_module->get_license_key();
		}else{
			return $this->license->key;
		}
	}


	/**
	 * Activates a module.
	 */
	public function activate(){
		$this->active = true;
		return $this->save();
	}

	/**
	 * Deactivates a module.
	 *
	 * @return boolean
	 */
	public function deactivate(){
		if($this->allows_deactivate()){
			$this->active = false;
			return $this->save();
		}
		return false;
	}

	public function update_settings($values){
		$this->set_settings($values);
		$this->save();
		return true;
	}

	/**
	 * Saves the module options.
	 *
	 * @return boolean the result of the update_option function
	 * !!! IMPORTANT: update_option returns false when the data has not changed
	 */
	public function save(){
		$data = array(
			'active' => $this->active
		);
		if($this->requires_own_license()){
			$data['license'] = $this->license->to_array(true);
		}
		if($this->has_settings()){
			$data['settings'] = $this->settings->get_saved_values();
		}
		return update_option( $this->prefix.$this->id, $data );
	}

	
	/**
	 * Checks if the module has any input options.
	 *
	 * @return boolean
	 */
	protected function has_options(){
		return $this->requires_own_license() || $this->has_settings();
	}

	/**
	 * Checks if the module requires license in order to be activated.
	 *
	 * @return boolean
	 */
	public function requires_license(){
		return ( isset($this->config['requires_license']) && $this->config['requires_license'] === true );
	}

	/**
	 * Checks if the module requires its own license and not a license from another module
	 * in order to be activated.
	 *
	 * @return boolean
	 */
	protected function requires_own_license(){
		return $this->requires_license() && !isset($this->config['uses_module_license']);
	}

	/**
	 * Checls whether the module has any input options that are not license options.
	 *
	 * @return boolean
	 */
	public function has_settings(){
		return isset($this->config['settings']);
	}

	public function get_setting($field_id){
		return $this->settings->get_field_value($field_id);
	}

	/**
	 * Retrieves the module's saved options.
	 *
	 * @return array
	 */
	protected function get_module_data(){
		return get_option($this->prefix.$this->id, array());
	}

	/**
	 * Checks whether the module allows to be deactivated.
	 *
	 * @return boolean
	 */
	public function allows_deactivate(){
		if(isset($this->config['allow_deactivate'])){
			return $this->config['allow_deactivate'];
		}
		return true;
	}



}

?>