<?php

class USIN_Edd_Lifetime_Value_Loader extends USIN_Numeric_Field_Loader {


	protected function get_default_data(){
		global $wpdb;

		$query = "SELECT COUNT(*) as $this->total_col, purchase_value as $this->label_col FROM ".$wpdb->prefix."edd_customers".
			" GROUP BY purchase_value";
		return $wpdb->get_results( $query );
	}

	protected function get_data_in_ranges($chunk_size){
		global $wpdb;

		$select = $this->get_select('purchase_value', $chunk_size);
		$group_by = $this->get_group_by('purchase_value', $chunk_size);

		$query = "$select FROM ".$wpdb->prefix."edd_customers $group_by";

		return $wpdb->get_results( $query );
	}

}