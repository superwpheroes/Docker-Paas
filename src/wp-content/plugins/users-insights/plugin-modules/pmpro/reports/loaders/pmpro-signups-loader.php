<?php

class USIN_Pmpro_Signups_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by('startdate');

		$query = $wpdb->prepare("SELECT COUNT(DISTINCT user_id) AS $this->total_col, startdate AS $this->label_col".
			" FROM $wpdb->pmpro_memberships_users".
			" WHERE startdate >= %s AND startdate <= %s GROUP BY $group_by",
			$this->get_period_start(), $this->get_period_end());
		
		return $wpdb->get_results( $query );
	}
}