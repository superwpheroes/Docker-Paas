<?php

if(!defined( 'ABSPATH' )){
	exit;
}

class USIN_Woocommerce extends USIN_Plugin_Module{

	protected $module_name = 'woocommerce';
	protected $plugin_path = 'woocommerce/woocommerce.php';
	protected $yith_wishlist_active = false;
	protected $wc_wishlist_active = false;
	protected static $wc_countries = null;

	const ORDER_POST_TYPE = 'shop_order';
	const PRODUCT_POST_TYPE = 'product';
	const WC_WISHLIST_POST_TYPE = 'wishlist';

	protected function apply_module_actions(){
		add_filter('usin_exclude_post_types', array($this , 'exclude_post_types'));
	}

	public function init(){
		$this->set_active_extensions();

		$this->product_search = new USIN_Post_Option_Search(self::PRODUCT_POST_TYPE);

		$this->wc_query = new USIN_Woocommerce_Query(self::ORDER_POST_TYPE);
		$this->wc_query->init();

		$wc_user_activity = new USIN_Woocommerce_User_Activity(self::ORDER_POST_TYPE);
		$wc_user_activity->init();

		if($this->yith_wishlist_active){
			$this->yiwl_query = new USIN_Woocommerce_Yith_Wishlists_Query();
			$this->yiwl_query->init();

			$this->yiwl_user_activity = new USIN_Woocommerce_Yith_Wishlists_User_Activity();
			$this->yiwl_user_activity->init();
		}

		if($this->wc_wishlist_active){
			$this->wcwl_query = new USIN_Woocommerce_Wishlists_Query();
			$this->wcwl_query->init();

			$this->wcwl_user_activity = new USIN_Woocommerce_Wishlists_User_Activity();
			$this->wcwl_user_activity->init();
		}

		add_action('woocommerce_admin_order_data_after_order_details', array($this, 'add_usin_profile_link_to_order'));

	}

	protected function set_active_extensions(){
		$this->yith_wishlist_active = USIN_Helper::is_plugin_activated('yith-woocommerce-wishlist/init.php') ||
			USIN_Helper::is_plugin_activated('yith-woocommerce-wishlist-premium/init.php');
		
		$this->wc_wishlist_active = USIN_Helper::is_plugin_activated('woocommerce-wishlists/woocommerce-wishlists.php');
	}

	public function register_module(){
		return array(
			'id' => $this->module_name,
			'name' => __('WooCommerce', 'usin'),
			'desc' => __('Retrieves and displays data from the WooCommerce orders made by the WordPress users.', 'usin'),
			'allow_deactivate' => true,
			'buttons' => array(
				array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/woocommerce-users-data/', 'target'=>'_blank')
			),
			'active' => false
		);
	}

	protected function init_reports(){
		new USIN_WooCommerce_Reports();
	}

	public function register_fields(){

		$product_search_options = $this->product_search->get_options();
		$product_search_action = $this->product_search->get_search_action();
		
		$fields = array(
			array(
				'name' => __('Placed an order', 'usin'),
				'id' => 'placed_order',
				'order' => 'DESC',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'combined',
					'items' => array(
						array('name' => __('Date', 'usin'), 'id' => 'date', 'type' => 'date'),
						array('name' => __('Status', 'usin'), 'id' => 'status', 'type' => 'select', 'options' => $this->get_order_status_options()),
						array('name' => __('Order total', 'usin'), 'id' => 'total', 'type' => 'number'),
						array('name' => __('Product', 'usin'), 'id' => 'product', 'type' => 'select', 'options' => $product_search_options, 'searchAction' => $product_search_action),
					),
					'disallow_null' => true
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Orders', 'usin'),
				'id' => 'order_num',
				'order' => 'DESC',
				'show' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'number',
					'disallow_null' => true
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('First order', 'usin'),
				'id' => 'first_order',
				'order' => 'DESC',
				'show' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'date'
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Last order', 'usin'),
				'id' => 'last_order',
				'order' => 'DESC',
				'show' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'date'
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Lifetime value', 'usin'),
				'id' => 'lifetime_value',
				'order' => 'DESC',
				'show' => true,
				'fieldType' => 'general',
				'filter' => array(
					'type' => 'number',
					'disallow_null' => true
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Billing country', 'usin'),
				'id' => 'wc_billing_country',
				'order' => false,
				'show' => false,
				'fieldType' => 'general',
				'filter' => array(
					'type' => 'select',
					'options' => $this->get_wc_country_options()
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Billing state', 'usin'),
				'id' => 'wc_billing_state',
				'order' => 'ASC',
				'show' => false,
				'fieldType' => 'general',
				'filter' => array(
					'type' => 'text'
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Billing city', 'usin'),
				'id' => 'wc_billing_city',
				'order' => 'ASC',
				'show' => false,
				'fieldType' => 'general',
				'filter' => array(
					'type' => 'text'
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Ordered products', 'usin'),
				'id' => 'has_ordered',
				'order' => 'ASC',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'include_exclude',
					'options' => $product_search_options,
					'searchAction' => $product_search_action
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Order statuses', 'usin'),
				'id' => 'has_order_status',
				'order' => 'ASC',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'include_exclude',
					'options' => $this->get_order_status_options()
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Reviews', 'usin'),
				'id' => 'reviews',
				'order' => 'DESC',
				'show' => false,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'number',
					'disallow_null' => true
				),
				'module' => $this->module_name
			),
			array(
				'name' => __('Has used coupon', 'usin'),
				'id' => 'has_used_coupon',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'select_option',
					'options' => $this->get_coupon_options()
				),
				'module' => $this->module_name
			),
		);

		if($this->yith_wishlist_active){
			$fields = array_merge($fields, array(
				array(
					'name' => __('Wishlist products', 'usin'),
					'id' => 'yiwl_product_num',
					'show' => false,
					'order' => 'DESC',
					'fieldType' => $this->module_name,
					'filter' => array(
						'type' => 'number',
						'disallow_null' => true
					),
					'module' => $this->module_name
				),
				array(
					'name' => __('Has product in wishlist', 'usin'),
					'id' => 'yiwl_has_wishlist_product',
					'show' => false,
					'hideOnTable' => true,
					'fieldType' => $this->module_name,
					'filter' => array(
						'type' => 'select_option',
						'options' => $product_search_options,
						'searchAction' => $product_search_action
					),
					'module' => $this->module_name
				),
			));
		}

		if($this->wc_wishlist_active){
			$fields[]=array(
				'name' => __('Has product in wishlist', 'usin'),
				'id' => 'wc_has_wishlist_product',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'select_option',
					'options' => $product_search_options,
					'searchAction' => $product_search_action
				),
				'module' => $this->module_name
			);
		}

		return $fields;
	}


	protected function get_order_status_options(){
		$status_options = array();

		if(function_exists('wc_get_order_statuses')){
			$wc_statuses = wc_get_order_statuses();
			if(!empty($wc_statuses)){
				foreach ($wc_statuses as $key => $value) {
					$status_options[]= array('key'=>$key, 'val'=>$value);
				}
			}
		}

		return $status_options;
	}
	
	protected function get_coupon_options(){
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'asc',
			'post_type'        => 'shop_coupon'
		);

		$coupons = get_posts( $args );
		$coupon_options = array();
		
		foreach ($coupons as $coupon ) {
			$coupon_options[]=array('key'=>strtolower($coupon->post_title), 'val'=>$coupon->post_title);
		}
		
		return $coupon_options;
		
	}

	public function exclude_post_types($exclude){
		return array_merge ($exclude,  array('shop_order','shop_order_refund','shop_coupon','shop_webhook', 'product_variation'));
	}

	public function add_usin_profile_link_to_order($order){
		$should_add_link = apply_filters('usin_add_profile_link_to_order', true);

		if(!$should_add_link){
			return;
		}

		if(!method_exists($order, 'get_user_id')){
			return;
		}

		$user_id = $order->get_user_id();

		if(empty($user_id) || intval($user_id) <= 0){
			return;
		}

		$slug = usin_manager()->slug;
		$link = add_query_arg('page', $slug, admin_url('admin.php'))."#/user/$user_id";
		
		echo sprintf('<p class="usin-wc-profile-link"><a href="%s" target="_blank">%s</a></p>', $link,
			__('Users Insights Profile', 'usin').' →');

	}

	protected function get_wc_country_options(){
		$countries = self::get_wc_countries();
		$options = array();
		foreach ($countries as $key => $value) {
			$options[]= array('key' => $key, 'val' => $value);
		}
		return $options;
	}

	public static function get_wc_countries(){
		if(self::$wc_countries !== null){
			return self::$wc_countries;
		}

		self::$wc_countries = array();
		if(function_exists('WC')){
			$wc = WC();
			if(property_exists($wc, 'countries') && method_exists($wc->countries, 'get_countries')){
				self::$wc_countries = $wc->countries->countries;
			}
		}
		return self::$wc_countries;
	}

	public static function get_wc_country_name_by_code($code){
		$countries = self::get_wc_countries();
		if(isset($countries[$code])){
			return html_entity_decode($countries[$code]);
		}

		return $code;
	}
	
}

new USIN_Woocommerce();