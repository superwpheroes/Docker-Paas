<?php

class USIN_WooCommerce_Reports extends USIN_Module_Reports{

	protected $group = 'woocommerce';

	public function get_group(){
		return array(
			'id' => $this->group,
			'name' => 'WooCommerce',
			'info' => '* All of the WooCommerce reports reflect both user and guest orders'
		);
	}

	public function get_reports(){
		$statuses = array('all' => __('All statuses', 'usin'));
		if(function_exists('wc_get_order_statuses')){
			$wc_statuses = wc_get_order_statuses();
			if(is_array($wc_statuses)){
				$statuses = array_merge($statuses, $wc_statuses);
			}
		}

		return array(
			new USIN_Period_Report('woocommerce_sales', __('Sales', 'usin'), 
				array('group' => $this->group, 'info' => __('Orders with status completed, processing or on hold'))),
			new USIN_Period_Report('woocommerce_sales_total', __('Sales total', 'usin'), 
				array('group' => $this->group, 'format' => 'float', 'info' => 'Total amount of sales (does not reflect partial refunds)')),
			new USIN_Period_Report('woocommerce_new_customers', __('New customers', 'usin'), 
				array('group' => $this->group)),
			new USIN_Standard_Report('woocommerce_order_number', __('Number of orders per customer', 'usin'), 
				array('group' => $this->group,
				'filters' => array(
					'options' => $statuses,
					'default' => 'all'
				))),
			new USIN_Standard_Report('woocommerce_billing_countries', __('Top billing countries', 'usin'), 
				array('group' => $this->group, 'type'=>USIN_Report::BAR)),
			new USIN_Standard_Report('woocommerce_billing_states', __('Top billing states', 'usin'), 
				array('group' => $this->group, 'type'=>USIN_Report::BAR, 'visible' => false)),
			new USIN_Standard_Report('woocommerce_billing_cities', __('Top billing cities', 'usin'), 
				array('group' => $this->group, 'type'=>USIN_Report::BAR)),
			new USIN_Standard_Report('woocommerce_order_statuses', __('Order status', 'usin'), 
				array('group' => $this->group)),
			new USIN_Standard_Report('woocommerce_payment_methods', __('Payment methods used', 'usin'), 
				array('group' => $this->group, 'visible' => false)),
			new USIN_Standard_Report('woocommerce_coupons_used', __('Top coupons used', 'usin'), 
				array('group' => $this->group, 'type' => USIN_Report::BAR)),
			new USIN_Standard_Report('woocommerce_ordered_products', __('Top ordered products', 'usin'), 
				array('group' => $this->group, 'type'=>USIN_Report::BAR))
		);
	}
}