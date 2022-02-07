<?php

class USIN_Memberpress_Coupons_Used_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		global $wpdb;

		$query = "SELECT usage_count.meta_value AS $this->total_col, coupons.post_title AS $this->label_col".
			" FROM $wpdb->posts AS coupons".
			" INNER JOIN $wpdb->postmeta AS usage_count ON coupons.ID = usage_count.post_id AND usage_count.meta_key = '_mepr_coupons_usage_count'".
			" ORDER BY $this->total_col DESC LIMIT $this->max_items";

		return $wpdb->get_results( $query );

	}


}