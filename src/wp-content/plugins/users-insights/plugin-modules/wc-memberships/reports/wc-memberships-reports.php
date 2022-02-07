<?php

class USIN_WC_Memberships_Reports extends USIN_Module_Reports{

	protected $group = 'wc_memberships';

	public function get_group(){
		return array(
			'id' => $this->group,
			'name' => 'WooCommerce Memberships'
		);
	}

	public function get_reports(){

		$statuses = USIN_WC_Memberships::get_status_options(true);
		$statuses['all'] = __('All statuses', 'usin');

		$plans = USIN_WC_Memberships::get_membership_plans(true);
		$plans['all'] = __('All plans', 'usin');

		return array(
			new USIN_Period_Report('wc_memberships_new_members', __('New members', 'usin'), array('group' => $this->group)),
			new USIN_Period_Report('wc_memberships_ended_memberships', __('Ended memberships', 'usin'), 
				array('group' => $this->group, 'info' => __('Cancelled & expired memberships', 'usin'))),
			new USIN_Standard_Report('wc_memberships_per_plan', __('Number of memberships per plan', 'usin'), 
				array(
					'group' => $this->group,
					'filters' => array(
						'default' => 'all',
						'options' => $statuses
					)
				)
			),
			new USIN_Standard_Report('wc_memberships_statuses', __('Membership statuses', 'usin'), 
				array(
					'group' => $this->group,
					'filters' => array(
						'default' => 'all',
						'options' => $plans
					)
				)
			)
		);
	}
}