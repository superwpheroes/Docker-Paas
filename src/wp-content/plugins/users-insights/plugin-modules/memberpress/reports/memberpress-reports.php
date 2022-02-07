<?php

class USIN_MemberPress_Reports extends USIN_Module_Reports{

	protected $group = 'memberpress';

	public function get_group(){
		
		return array(
			'id' => $this->group,
			'name' => 'MemberPress'
		);
	}

	public function get_reports(){

		$products = USIN_MemberPress::get_membership_products();
		$products['all'] = __('All memberships', 'usin');

		$reports = array(
			new USIN_Period_Report('memberpress_signups', __('Membership signups', 'usin'), 
				array('group' => $this->group)),
			new USIN_Period_Report('memberpress_payments', __('Payments', 'usin'), 
				array('group' => $this->group)),
			new USIN_Period_Report('memberpress_payments_total', __('Payments total amount', 'usin'), 
				array('group' => $this->group, 'format' => 'float')),
			new USIN_Period_Report('memberpress_ended_memberships', __('Ended memberships', 'usin'), 
				array('group' => $this->group)),
			new USIN_Standard_Report('memberpress_users_per_membership', __('Number of users per membership', 'usin'), 
			array(
				'group' => $this->group, 
				'filters' => array(
					'default' => 'active',
					'options' => array('active'=>__('active', 'usin'), 'inactive' => __('inactive', 'usin'), 'all' => __('all statuses'))
				))),
			new USIN_Standard_Report('memberpress_membership_statuses', __('Membership statuses', 'usin'), 
			array(
				'group' => $this->group, 
				'filters' => array(
					'default' => 'all',
					'options' => $products
				))),
			new USIN_Standard_Report('memberpress_ltv', __('Member lifetime value', 'usin'), 
				array('group' => $this->group, 'type' => USIN_Report::BAR)),
			new USIN_Standard_Report('memberpress_coupons_used', __('Top coupons used', 'usin'), 
				array('group' => $this->group, 'type' => USIN_Report::BAR))
		);

		return $reports;

	}
}