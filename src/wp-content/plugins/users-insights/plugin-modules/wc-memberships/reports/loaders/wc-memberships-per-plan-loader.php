<?php

class USIN_Wc_Memberships_Per_Plan_Loader extends USIN_Standard_Report_Loader {
	
	protected function load_data(){
		
		$data = $this->load_db_data();

		$plans = USIN_WC_Memberships::get_membership_plans(true);

		return $this->match_ids_to_names($data, $plans, true);
	}

	protected function load_db_data(){
		global $wpdb;

		$filter = $this->getSelectedFilter();
		$condition = $wpdb->prepare("WHERE post_type = %s", USIN_WC_Memberships::POST_TYPE);

		if($filter == 'all'){
			$statuses = USIN_WC_Memberships_Query::get_status_string();
			$condition .= " AND post_status IN (".$statuses.")";
		}else{
			$condition .= $wpdb->prepare(" AND post_status = %s", $filter);
		}

		$query = "SELECT COUNT(*) AS $this->total_col, post_parent AS $this->label_col FROM $wpdb->posts".
			" $condition GROUP BY post_parent";

		return $wpdb->get_results( $query );
	}
}