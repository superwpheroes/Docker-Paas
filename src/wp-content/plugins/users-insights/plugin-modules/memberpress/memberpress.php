<?php

if(!defined( 'ABSPATH' )){
	exit;
}

class USIN_MemberPress extends USIN_Plugin_Module{
	protected $module_name = 'memberpress';
	protected $plugin_path = 'memberpress/memberpress.php';
	protected $custom_fields = null;
	protected $option_fields = null;

	const MEMBERSHIP_POST_TYPE = 'memberpressproduct';
	const COUPON_POST_TYPE = 'memberpresscoupon';
	const FIELD_PREFIX = 'mepr_cf_';
	const MIN_MEPR_VERSION = '1.3.20';
	

	public function init(){
		add_filter('usin_user_db_data', array($this , 'format_option_fields_data'));

		$custom_fields = $this->get_custom_fields();
		$query = new USIN_MemberPress_Query($custom_fields);
		$query->init();

		$user_activity = new USIN_MemberPress_User_Activity($this->module_name);
		$user_activity->init();
	}

	protected function init_reports(){
		new USIN_MemberPress_Reports();
	}


	public function register_module(){
		add_filter('usin_should_activate_module_'.$this->module_name, array($this, 'check_if_activation_is_allowed'));

		return array(
			'id' => $this->module_name,
			'name' => 'MemberPress',
			'desc' => __('Makes the MemberPress user membership and profile data available in the user table, filters and reports.', 'usin'),
			'allow_deactivate' => true,
			'buttons' => array(
				array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/memberpress-search-user-data/', 'target'=>'_blank')
			),
			'in_beta' => true,
			'active' => false
		);
	}

	public function check_if_activation_is_allowed($should_activate){
		if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, self::MIN_MEPR_VERSION, '<')){
			return new WP_Error('usin_mepr', sprintf( __('Error: Minimum supported MemberPress version is %s. Your current version is: %s', 'usin'), self::MIN_MEPR_VERSION, MEPR_VERSION));	
		}
		return $should_activate;
	}

	public function register_fields(){
		$fields = array();

		$fields[]=array(
			'name' => __('Member status', 'usin'),
			'id' => 'mepr_status',
			'order' => 'ASC',
			'show' => false,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'select',
				'options' => array(
					array('key' => 'active', 'val' => __('active', 'usin')),
					array('key' => 'inactive', 'val' => __('inactive', 'usin'))
				),
				'disallow_null' => true
			),
			'module' => $this->module_name
		);

		$fields[]=array(
			'name' => __('Lifetime value', 'usin'),
			'id' => 'mepr_ltv',
			'order' => 'DESC',
			'show' => true,
			'fieldType' => 'general',
			'filter' => array(
				'type' => 'number'
			),
			'module' => $this->module_name
		);

		$fields[]=array(
			'name' => __('Transactions', 'usin'),
			'id' => 'mepr_transaction_count',
			'order' => 'DESC',
			'show' => true,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'number'
			),
			'module' => $this->module_name
		);

		$fields[]=array(
			'name' => __('Memberships', 'usin'),
			'id' => 'mepr_membership_count',
			'order' => 'DESC',
			'show' => false,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'number'
			),
			'module' => $this->module_name
		);


		$fields[]=array(
			'name' => __('First transaction', 'usin'),
			'id' => 'mepr_first_transaction',
			'order' => 'DESC',
			'show' => false,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'date'
			),
			'module' => $this->module_name
		);

		$fields[]=array(
			'name' => __('Last transaction', 'usin'),
			'id' => 'mepr_last_transaction',
			'order' => 'DESC',
			'show' => false,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'date'
			),
			'module' => $this->module_name
		);

		$fields[]=array(
			'name' => __('Has a membership', 'usin'),
			'id' => 'mepr_has_membership',
			'order' => 'DESC',
			'show' => false,
			'hideOnTable' => true,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'combined',
				'items' => array(
					array('name' => __('Product', 'usin'), 'id' => 'product', 'type' => 'select', 'options' => self::get_membership_products(true)),
					array('name' => __('Status', 'usin'), 'id' => 'status', 'type' => 'select', 'options' => array(array('key' => 'active', 'val' => __('active', 'usin')), array('key' => 'inactive', 'val' => __('inactive', 'usin')))),
					array('name' => __('Date created', 'usin'), 'id' => 'date_created', 'type' => 'date'),
					array('name' => __('Date expiring', 'usin'), 'id' => 'date_expiring', 'type' => 'date')
				),
				'disallow_null' => true
			),
			'module' => $this->module_name
		);

		$fields[]=array(
			'name' => __('Has used coupon', 'usin'),
			'id' => 'mepr_has_used_coupon',
			'show' => false,
			'hideOnTable' => true,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'select_option',
				'options' => USIN_Helper::get_post_list(self::COUPON_POST_TYPE, true)
			),
			'module' => $this->module_name
		);

		$custom_fields = $this->get_custom_fields();
		foreach ($custom_fields as $field ) {
			$fields[]= $field->to_usin_field();
		}

		return $fields;
	}


	protected function get_custom_fields(){
		if($this->custom_fields !== null){
			return $this->custom_fields;
		}

		$this->custom_fields = array();
		$options = get_option('mepr_options');

		if(!is_array($options) || !isset($options['custom_fields']) || !is_array($options['custom_fields'])){
			return $this->custom_fields;
		}

		foreach ($options['custom_fields'] as $field_config ) {
			if(is_array($field_config)){
				$this->custom_fields[]= new USIN_MemberPress_Field($field_config, self::FIELD_PREFIX, $this->module_name);
			}
		}

		return $this->custom_fields;
	}


	public function format_option_fields_data($user_data){
		$option_fields = $this->get_option_fields();
		foreach ($option_fields as $field) {
			$field_id = $field->id;
			if(isset($user_data->$field_id)){
				$user_data->$field_id = $field->format_value($user_data->$field_id);
			}
		}
		return $user_data;
	}


	protected function get_option_fields(){
		if($this->option_fields === null){
			$this->option_fields = array_filter($this->get_custom_fields(), array($this, 'is_option_field'));
		}
		return $this->option_fields;
	}


	protected function is_option_field($field){
		return $field->is_option_field();
	}

	public static function get_membership_products($format_as_options = false){
		return USIN_Helper::get_post_list(self::MEMBERSHIP_POST_TYPE, $format_as_options);
	}

}


new USIN_MemberPress();