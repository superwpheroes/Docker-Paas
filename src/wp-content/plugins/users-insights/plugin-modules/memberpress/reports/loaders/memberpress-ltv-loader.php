<?php

class USIN_Memberpress_Ltv_Loader extends USIN_Numeric_Field_Loader {


	protected function get_default_data(){
		global $wpdb;

		$members_table = $wpdb->prefix."mepr_members";
		$query = "SELECT COUNT(*) as $this->total_col, total_spent as $this->label_col FROM $members_table".
			" GROUP BY total_spent";
		
		return $wpdb->get_results( $query );
	}


	protected function get_data_in_ranges($chunk_size){
		
		global $wpdb;

		$select = $this->get_select('total_spent', $chunk_size);
		$group_by = $this->get_group_by('total_spent', $chunk_size);

		$members_table = $wpdb->prefix."mepr_members";
		$query = "$select FROM $members_table $group_by";

		return $wpdb->get_results( $query );
	}

}