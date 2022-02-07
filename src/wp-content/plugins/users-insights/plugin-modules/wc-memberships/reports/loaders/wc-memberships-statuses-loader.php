<?php

class USIN_Wc_Memberships_Statuses_Loader extends USIN_Standard_Report_Loader {
	
	protected function load_data(){
		
		$data = $this->load_db_data();

		$statuses = USIN_WC_Memberships::get_status_options(true);

		return $this->match_ids_to_names($data, $statuses, true);
	}

	protected function load_db_data(){
		global $wpdb;

		$filter = $this->getSelectedFilter();
		$condition = $wpdb->prepare("WHERE post_type = %s", USIN_WC_Memberships::POST_TYPE);

		if($filter != 'all'){
			$condition .= $wpdb->prepare(" AND post_parent = %d", intval($filter));
		}

		$query = "SELECT COUNT(*) AS $this->total_col, post_status AS $this->label_col".
			"  FROM $wpdb->posts $condition GROUP BY post_status";

		return $wpdb->get_results( $query );
	}
}