<?php

class USIN_Pmpro_Ltv_Loader extends USIN_Numeric_Field_Loader {


	protected function get_default_data(){
		global $wpdb;

		$subquery = $this->get_subquery();
		
		$query = "SELECT COUNT(*) as $this->total_col, ltv as $this->label_col FROM ($subquery) AS ltvs".
			" GROUP BY ltv";
		
		return $wpdb->get_results( $query );
	}

	protected function get_subquery(){
		global $wpdb;

		$condition = USIN_Pmpro_Query::get_sucessful_order_condition();
		$subquery = "SELECT SUM(total) AS ltv FROM $wpdb->pmpro_membership_orders".
			" WHERE $condition GROUP BY user_id";

		return $subquery;
	}

	protected function get_data_in_ranges($chunk_size){
		
		global $wpdb;

		$subquery = $this->get_subquery();
		$select = $this->get_select('ltv', $chunk_size);
		$group_by = $this->get_group_by('ltv', $chunk_size);

		$query = "$select FROM ($subquery) AS ltvs $group_by";

		return $wpdb->get_results( $query );
	}

}