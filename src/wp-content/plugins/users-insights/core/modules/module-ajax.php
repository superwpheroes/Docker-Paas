<?php

/**
 * Includes the AJAX functionality for the Modules page
 */
class USIN_Module_Ajax extends USIN_Ajax{

	protected $user_capability;
	protected $modules;
	protected $nonce_key;

	/**
	 * @param USIN_Modules $modules  the module options object
	 * @param string $user_capability the required user capability to access the modules page
	 * @param string $nonce_key       the nonce key for the security checks
	 */
	public function __construct($modules, $user_capability, $nonce_key){
		$this->modules = $modules;
		$this->user_capability = $user_capability;
		$this->nonce_key = $nonce_key;
	}

	/**
	 * Registers the required actions hooks.
	 */
	public function add_actions(){
		add_action('wp_ajax_usin_add_license', array($this, 'add_license'));
		add_action('wp_ajax_usin_deactivate_license', array($this, 'deactivate_license'));
		add_action('wp_ajax_usin_activate_module', array($this, 'activate_module'));
		add_action('wp_ajax_usin_deactivate_module', array($this, 'deactivate_module'));
		add_action('wp_ajax_usin_refresh_license_status', array($this, 'refresh_license_status'));
		add_action('wp_ajax_usin_update_module_settings', array($this, 'update_module_settings'));
	}
	
	/**
	 * Handler for the Add & Activate License functionality.
	 */
	public function add_license(){
		$this->verify_request($this->user_capability);
		$this->validate_required_post_params(array('license_key', 'module_id'));

		$license_key = $_POST['license_key'];
		$module_id = $_POST['module_id'];

		$res = $this->modules->activate_license($license_key, $module_id);

		$this->respond($res);
	}

	/**
	 * Handler for the Deactivate & Remove License functionality.
	 */
	public function deactivate_license(){
		$this->verify_request($this->user_capability);
		$this->validate_required_post_params(array('license_key', 'module_id'));

		$license_key = $_POST['license_key'];
		$module_id = $_POST['module_id'];

		$res = $this->modules->deactivate_license($license_key, $module_id);

		$this->respond($res);
	}
	
	public function refresh_license_status(){
		$this->verify_request($this->user_capability);
		$this->validate_required_post_params(array('module_id'));
		
		$module_id = $_POST['module_id'];

		$res = $this->modules->refresh_license_status($module_id);
		$this->respond($res);
	}

	/**
	 * Activates a module.
	 */
	public function activate_module(){
		$this->verify_request($this->user_capability);
		$this->validate_required_post_params(array('module_id'));

		$res = $this->modules->activate_module($_POST['module_id']);
		
		$this->respond($res);
	}


	/**
	 * Deactivates a module.
	 */
	public function deactivate_module(){
		$this->verify_request($this->user_capability);
		$this->validate_required_post_params(array('module_id'));

		$this->modules->deactivate_module($_POST['module_id']);
		
		$this->respond_success();
	}

	public function update_module_settings(){
		$this->verify_request($this->user_capability);
		$this->validate_required_post_params(array('module_id', 'settings'));

		$settings = get_object_vars($this->get_request_array('settings'));

		$res = $this->modules->update_settings($_POST['module_id'], $settings);

		$this->respond($res);

	}


}