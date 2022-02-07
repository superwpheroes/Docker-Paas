<?php

class USIN_Pmpro_Membership_Statuses_Loader extends USIN_Standard_Report_Loader {
	
	protected function load_data(){
		
		$data = $this->load_db_data();
		
		return $data;
	}

	protected function load_db_data(){
		global $wpdb;

		$filter = $this->getSelectedFilter();

		$condition = '';
		if($filter != 'all'){
			$condition = $wpdb->prepare(" WHERE membership_id = %d", $filter);
		}

		$query = "SELECT COUNT(*) AS $this->total_col, status AS $this->label_col FROM $wpdb->pmpro_memberships_users".
			" $condition GROUP BY status";

		return $wpdb->get_results( $query );
	}
}