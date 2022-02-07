<?php

class USIN_Memberpress_Users_Per_Membership_Loader extends USIN_Standard_Report_Loader {
	
	protected function load_data(){
		$data = $this->load_db_data();
		$levels = USIN_MemberPress::get_membership_products(false);
		
		return $this->match_ids_to_names($data, $levels, true);
	}

	protected function load_db_data(){
		global $wpdb;

		$filter = $this->getSelectedFilter();

		$condition = '';
		if($filter == 'active'){
			$condition = " WHERE is_active = 1";
		}elseif($filter == 'inactive'){
			$condition = " WHERE is_active = 0";
		}

		$memberships_query = USIN_MemberPress_Query::get_memberships_query();
		$query = "SELECT COUNT(DISTINCT user_id) AS $this->total_col, product_id AS $this->label_col FROM ($memberships_query) m".
			" $condition GROUP BY product_id";

		return $wpdb->get_results( $query );
	}
}