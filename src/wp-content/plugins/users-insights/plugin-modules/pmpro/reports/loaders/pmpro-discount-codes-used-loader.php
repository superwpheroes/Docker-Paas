<?php

class USIN_Pmpro_Discount_Codes_Used_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		global $wpdb;

		$query = "SELECT COUNT(dcu.code_id) AS $this->total_col, dc.code AS $this->label_col".
			" FROM $wpdb->pmpro_discount_codes_uses dcu".
			" INNER JOIN $wpdb->pmpro_discount_codes dc ON dcu.code_id = dc.id".
			" GROUP BY dcu.code_id ORDER BY $this->total_col DESC LIMIT $this->max_items";

		return $wpdb->get_results( $query );

	}


}