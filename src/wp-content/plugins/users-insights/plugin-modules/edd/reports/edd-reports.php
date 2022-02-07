<?php

class USIN_EDD_Reports extends USIN_Module_Reports{

	protected $group = 'edd';

	public function get_group(){
		
		return array(
			'id' => $this->group,
			'name' => 'EDD',
			'info' => '* All of the EDD reports reflect both user and guest orders'
		);
	}

	public function get_reports(){


		$reports = array(
			new USIN_Period_Report('edd_sales', __('Sales', 'usin'), 
				array('group'=>$this->group)),
			new USIN_Period_Report('edd_earnings', __('Earnings', 'usin'), 
				array('group'=>$this->group, 'format' => 'float', 'info' => 'Total earnings including taxes')),
			new USIN_Period_Report('edd_new_customers', __('New customers', 'usin'), 
				array('group'=>$this->group)),
			new USIN_Standard_Report('edd_order_number', __('Number of sales per customer', 'usin'), 
				array('group'=>$this->group)),
			new USIN_Standard_Report('edd_order_statuses', __('Order statuses', 'usin'), 
				array('group'=>$this->group)),
			new USIN_Standard_Report('edd_lifetime_value', __('Customer Lifetime value', 'usin'), 
				array('group'=>$this->group, 'type' => USIN_Report::BAR)),
			new USIN_Standard_Report('edd_product_sales', __('Best sellers', 'usin'), 
				array('group'=>$this->group, 'type' => USIN_Report::BAR))
		);

		if(USIN_EDD::is_licensing_enabled()){
			$reports[]= new USIN_Standard_Report('edd_license_statuses', __('License statuses', 'usin'), 
				array('group'=>$this->group));

			$reports[]= new USIN_Period_Report('edd_license_renewals', __('License renewals', 'usin'), 
				array('group'=>$this->group));
		}


		return $reports;

	}
}