<?php

class USIN_Pmpro_Reports extends USIN_Module_Reports{

	protected $group = 'pmpro';

	public function get_group(){
		return array(
			'id' => $this->group,
			'name' => 'Paid Memberships Pro'
		);
	}

	public function get_reports(){

		$statuses = USIN_Pmpro::get_statuses(true);
		$statuses['all'] = __('All statuses', 'usin');

		$levels = USIN_Pmpro::get_levels(true);
		$levels['all'] = __('All levels', 'usin');

		return array(
			new USIN_Period_Report('pmpro_signups', __('Signups', 'usin'), 
				array('group' => $this->group)),
			new USIN_Period_Report('pmpro_payments', __('Payments', 'usin'), 
				array('group' => $this->group)),
			new USIN_Period_Report('pmpro_payments_total', __('Payments total amount', 'usin'), 
				array('group' => $this->group, 'format' => 'float')),
			new USIN_Period_Report('pmpro_ended_memberships', __('Ended memberships', 'usin'), 
				array('group' => $this->group)),
			new USIN_Standard_Report('pmpro_members_per_level', __('Number of members per level', 'usin'), 
				array(
					'group' => $this->group, 
					'filters' => array(
						'default' => 'active',
						'options' => $statuses
					))),
			new USIN_Standard_Report('pmpro_membership_statuses', __('Membership statuses', 'usin'), 
				array(
					'group' => $this->group, 
					'filters' => array(
						'default' => 'all',
						'options' => $levels
					))),
			new USIN_Standard_Report('pmpro_ltv', __('Customer lifetime value', 'usin'), 
					array('group' => $this->group, 'type' => USIN_Report::BAR)),
			new USIN_Standard_Report('pmpro_discount_codes_used', __('Top discount codes used', 'usin'), 
					array('group' => $this->group, 'type' => USIN_Report::BAR)),
			new USIN_Standard_Report('pmpro_billing_countries', __('Top billing countries', 'usin'), 
				array('group' => $this->group, 'type' => USIN_Report::BAR)),
			new USIN_Standard_Report('pmpro_billing_states', __('Top billing states', 'usin'), 
				array('group' => $this->group, 'type' => USIN_Report::BAR, 'visible' => false)),
			new USIN_Standard_Report('pmpro_billing_cities', __('Top billing cities', 'usin'), 
				array('group' => $this->group, 'type' => USIN_Report::BAR, 'visible' => false))
		);
	}
}