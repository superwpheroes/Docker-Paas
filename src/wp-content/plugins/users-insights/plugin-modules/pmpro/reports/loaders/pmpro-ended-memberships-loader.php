<?php

class USIN_Pmpro_Ended_Memberships_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by('enddate');
		$statuses = USIN_Helper::array_to_sql_string(array('inactive','expired','cancelled','admin_cancelled'));

		$query = $wpdb->prepare("SELECT COUNT(DISTINCT user_id) AS $this->total_col, enddate AS $this->label_col".
			" FROM $wpdb->pmpro_memberships_users".
			" WHERE enddate >= %s AND enddate <= %s AND status IN ($statuses) GROUP BY $group_by",
			$this->get_period_start(), $this->get_period_end());
		
		return $wpdb->get_results( $query );
	}
}