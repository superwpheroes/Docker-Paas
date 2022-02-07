<?php

class USIN_Woocommerce_Coupons_Used_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		global $wpdb;

		$query = "SELECT COUNT(*) AS $this->total_col, order_item_name AS $this->label_col".
			" FROM ".$wpdb->prefix."woocommerce_order_items WHERE order_item_type = 'coupon'".
			" GROUP BY order_item_name ORDER BY $this->total_col DESC LIMIT $this->max_items";

		return $wpdb->get_results( $query );

	}


}