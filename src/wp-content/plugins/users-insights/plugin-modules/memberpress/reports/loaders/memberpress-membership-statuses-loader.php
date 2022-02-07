<?php

class USIN_Memberpress_Membership_Statuses_Loader extends USIN_Standard_Report_Loader {
	
	protected function load_data(){
		$data = $this->load_db_data();
		$statuses = array('0'=>__('inactive', 'usin'), '1'=>__('active', 'usin'));
		return $this->match_ids_to_names($data, $statuses, true);
	}

	protected function load_db_data(){
		global $wpdb;

		$filter = $this->getSelectedFilter();

		$condition = '';
		if($filter != 'all'){
			$condition = $wpdb->prepare(" WHERE product_id = %d", intval($filter));
		}

		$memberships_query = USIN_MemberPress_Query::get_memberships_query();
		$query = "SELECT COUNT(DISTINCT id) AS $this->total_col, is_active AS $this->label_col FROM ($memberships_query) m".
			" $condition GROUP BY is_active";

		return $wpdb->get_results( $query );
	}
}