<?php

class USIN_Learndash_Groups_Loader extends USIN_Standard_Report_Loader {


	protected function load_data(){
		$data = $this->load_db_data();
		$groups = USIN_LearnDash::get_items(USIN_LearnDash::GROUP_POST_TYPE, true);

		return $this->match_ids_to_names($data, $groups);
	}

	protected function load_db_data(){
		global $wpdb;
		
		$query = "SELECT COUNT(*) as $this->total_col, meta_value as $this->label_col FROM $wpdb->usermeta".
			" WHERE meta_key LIKE 'learndash_group_users%' AND meta_value != '' ".
			" GROUP BY meta_value ORDER BY $this->total_col DESC LIMIT $this->max_items";

		return $wpdb->get_results( $query );
	}

}