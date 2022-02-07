<?php

class USIN_Pmpro_Members_Per_Level_Loader extends USIN_Standard_Report_Loader {
	
	protected function load_data(){
		
		$data = $this->load_db_data();

		$levels = USIN_Pmpro::get_levels(true);

		return $this->match_ids_to_names($data, $levels, true);
	}

	protected function load_db_data(){
		global $wpdb;

		$filter = $this->getSelectedFilter();

		$condition = '';
		if($filter != 'all'){
			$condition = $wpdb->prepare(" WHERE status = %s", $filter);
		}

		$query = "SELECT COUNT(DISTINCT user_id) AS $this->total_col, membership_id AS $this->label_col FROM $wpdb->pmpro_memberships_users".
			" $condition GROUP BY membership_id";

		return $wpdb->get_results( $query );
	}
}