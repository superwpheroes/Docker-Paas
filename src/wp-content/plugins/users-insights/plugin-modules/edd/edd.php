<?php

if(!defined( 'ABSPATH' )){
	exit;
}

/**
 * Easy Digital Downloads module - retrieves and displays data from the Easy 
 * Digital Downloads orders made by the WordPress users
 */
class USIN_EDD extends USIN_Plugin_Module{

	const ORDER_POST_TYPE = 'edd_payment';
	const LICENSE_POST_TYPE = 'edd_license';

	protected $module_name = 'edd';
	protected $plugin_path = 'easy-digital-downloads/easy-digital-downloads.php';
	protected $product_post_type = 'download';
	public $edd_query;
	public $edd_user_activity;
	
	
	protected function apply_module_actions(){
		add_filter('usin_exclude_comment_types', array($this , 'exclude_edd_private_comment_types'));
	}
	
	/**
	 * Initalizes the EDD Query and User Activity functionality.
	 */
	public function init(){
		add_filter('usin_exclude_post_types', array($this , 'exclude_post_types'));
		
		$this->edd_query = new USIN_EDD_Query(self::ORDER_POST_TYPE);
		$this->edd_query->init();
		
		$this->edd_user_activity = new USIN_EDD_User_Activity(self::ORDER_POST_TYPE, $this->product_post_type);
		$this->edd_user_activity->init();
	}

	protected function init_reports(){
		new USIN_EDD_Reports();
	}

	/**
	 * Registers the EDD Module by filtering the default module options.
	 * @param  array $default_modules the default modules array
	 * @return array                  the default modules, including the EDD module
	 */
	public function register_module(){
		return array(
			'id' => $this->module_name,
			'name' => __('Easy Digital Downloads', 'usin'),
			'desc' => __('Retrieves and displays data from the Easy Digital Downloads orders made by the WordPress users.', 'usin'),
			'allow_deactivate' => true,
			'buttons' => array(
				array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/easy-digital-downloads-users-data/', 'target'=>'_blank')
			),
			'active' => false
		);
	}
	
	/**
	 * Registers the additional EDD fields.
	 * @param  array $fields the default Users Insights table fields 
	 * @return array         the default fields including the EDD fields
	 */
	public function register_fields(){
		$fields = array();

		$fields[]=array(
			'name' => __('Orders', 'usin'),
			'id' => 'edd_order_num',
			'order' => 'DESC',
			'show' => true,
			'fieldType' => 'edd',
			'filter' => array(
				'type' => 'number',
				'disallow_null' => true
			),
			'module' => 'edd'
		);
		
		$fields[]=array(
			'name' => __('Lifetime value', 'usin'),
			'id' => 'edd_total_spent',
			'order' => 'DESC',
			'show' => true,
			'fieldType' => 'general',
			'filter' => array(
				'type' => 'number',
				'disallow_null' => true
			),
			'module' => 'edd'
		);
		
		
		$fields[]=array(
			'name' => __('Last order date', 'usin'),
			'id' => 'edd_last_order',
			'order' => 'DESC',
			'show' => true,
			'fieldType' => 'edd',
			'filter' => array(
				'type' => 'date'
			),
			'module' => 'edd'
		);

		$fields[]=array(
			'name' => __('Ordered products', 'usin'),
			'id' => 'edd_has_ordered',
			'order' => 'ASC',
			'show' => false,
			'hideOnTable' => true,
			'fieldType' => 'edd',
			'filter' => array(
				'type' => 'include_exclude',
				'options' => $this->get_product_options()
			),
			'module' => 'edd'
		);
		
		$fields[]=array(
			'name' => __('Order statuses', 'usin'),
			'id' => 'edd_has_order_status',
			'order' => 'ASC',
			'show' => false,
			'hideOnTable' => true,
			'fieldType' => 'edd',
			'filter' => array(
				'type' => 'include_exclude',
				'options' => self::get_order_status_options()
			),
			'module' => 'edd'
		);

		return $fields;
	}
	
	/**
	 * Loads the EDD product list.
	 * @return array the product list
	 */
	protected function get_product_options(){
		$product_options = array();
		$products = get_posts( array( 'post_type' => $this->product_post_type, 'posts_per_page' => -1 ) );

		foreach ($products as $product) {
			$product_options[] = array('key'=>$product->ID, 'val'=>$product->post_title);
		}

		return $product_options;
	}
	
	/**
	 * Loads the registered EDD statuses.
	 * @return array the statuses list
	 */
	public static function get_order_status_options($assoc_res = false){
		$status_options = array();
	
		if(function_exists('edd_get_payment_statuses')){
			$edd_statuses = edd_get_payment_statuses();
			if(!empty($edd_statuses)){
				foreach ($edd_statuses as $key => $value) {
					if($assoc_res){
						$status_options[$key] = $value;
					}else{
						$status_options[]= array('key'=>$key, 'val'=>$value);
					}
				}
			}
		}
	
		return $status_options;
	}

	/**
	 * Excludes the EDD custom post types from the "Posts Created" field in the query
	 * @param  array $exclude the default posts types to exclude
	 * @return array          the default post types to exclude merged with the 
	 * EDD custom post types
	 */
	public function exclude_post_types($exclude){
		return array_merge ($exclude,  array('edd_log','edd_payment','edd_discount', 'edd_license_log'));
	}
	
	public function exclude_edd_private_comment_types($exclude){
		return array_merge ($exclude,  array('edd_payment_note'));
	}

	public static function is_licensing_enabled(){
		return USIN_Helper::is_plugin_activated('edd-software-licensing/edd-software-licenses.php');
	}
}

new USIN_EDD();