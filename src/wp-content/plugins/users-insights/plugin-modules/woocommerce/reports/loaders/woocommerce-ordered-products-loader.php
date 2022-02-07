<?php

class USIN_Woocommerce_Ordered_Products_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		global $wpdb;

		$query = "SELECT COUNT(*) AS $this->total_col, posts.post_title AS $this->label_col". 
			" FROM ".$wpdb->prefix."woocommerce_order_itemmeta AS meta".
			" INNER JOIN $wpdb->posts AS posts ON meta.meta_value = posts.ID".
			" WHERE meta_key = '_product_id'".
			" GROUP BY meta_value ORDER BY $this->total_col DESC LIMIT $this->max_items";

		return $wpdb->get_results( $query );
	}


}