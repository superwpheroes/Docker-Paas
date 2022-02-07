<?php

/**
 * Includes the AJAX functionality for the Modules page
 */
class USIN_Reports_Ajax extends USIN_Ajax{

	protected $user_capability;
	protected $nonce_key;

	/**
	 * @param string $user_capability the required user capability to access the modules page
	 * @param string $nonce_key       the nonce key for the security checks
	 */
	public function __construct($user_capability, $nonce_key){
		$this->user_capability = $user_capability;
		$this->nonce_key = $nonce_key;
	}

	/**
	 * Registers the required actions hooks.
	 */
	public function add_actions(){
		add_action('wp_ajax_usin_get_report', array($this, 'get_report'));
		add_action('wp_ajax_usin_update_report_visibility', array($this, 'update_report_visibility'));
	}

	public static function is_reports_ajax(){
		if((defined('DOING_AJAX') && DOING_AJAX) && isset($_REQUEST['action']) && strpos($_REQUEST['action'], 'usin_') === 0
			&& strpos($_REQUEST['action'], 'report') !== false){
			return true;
		}
		return false;
	}

	protected function get_options(){
		$allowed_options = array('filter');
		$options = array();

		foreach($allowed_options as $key){
			if(isset($_GET[$key])){
				$options[$key] = $_GET[$key];
			}
		}

		return $options;
	}

	public function get_report(){
		$this->verify_request($this->user_capability);
		$this->validate_required_get_params(array('report_id'));

		$report = USIN_Reports_Defaults::get_by_id($_GET['report_id']);
		$options = $this->get_options();
		
		if(!$report){
			$this->respond_error(__('Error finding a report with this ID', 'usin'));
		}

		if($report->has_filters() && empty($options['filter'])){
			$this->respond_error(__('Missing filter parameter', 'usin'));
		}

		$this->respond( $report->get_data($options) );
	}

	public function update_report_visibility(){
		$this->verify_request($this->user_capability);
		$this->validate_required_post_params(array('report_id', 'visibility'));

		$report_id = $_POST['report_id'];
		$visibility = $_POST['visibility'] === "true" ? true : false;
		
		USIN_Report_Options::update_report_visibility($report_id, $visibility);
		$this->respond_success();
		
	}
	
}