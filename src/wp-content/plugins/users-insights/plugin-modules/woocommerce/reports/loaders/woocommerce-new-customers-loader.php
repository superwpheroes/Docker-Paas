<?php

class USIN_Woocommerce_New_Customers_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$subquery = $wpdb->prepare("SELECT MIN(post_date) AS first_order FROM $wpdb->posts AS posts".
			" INNER JOIN $wpdb->postmeta AS emails ON posts.ID = emails.post_id AND emails.meta_key = '_billing_email'".
			" WHERE posts.post_type = %s GROUP BY emails.meta_value".
			" HAVING first_order >= %s AND first_order <= %s", USIN_Woocommerce::ORDER_POST_TYPE, $this->get_period_start(), $this->get_period_end() );

		$group_by = $this->get_period_group_by('order_dates.first_order');

		$query = "SELECT COUNT(*) AS $this->total_col, first_order AS $this->label_col".
			" FROM ($subquery) AS order_dates GROUP BY $group_by";

		return $wpdb->get_results( $query );
	}
}