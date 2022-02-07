<?php

class USIN_Pmpro_Payments_Total_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by('timestamp');
		$success_condition = USIN_Pmpro_Query::get_sucessful_order_condition();

		$query = $wpdb->prepare("SELECT SUM(total) AS $this->total_col, timestamp AS $this->label_col".
			" FROM $wpdb->pmpro_membership_orders".
			" WHERE timestamp >= %s AND timestamp <= %s AND $success_condition GROUP BY $group_by",
			$this->get_period_start(), $this->get_period_end());
		
		return $wpdb->get_results( $query );
	}
}