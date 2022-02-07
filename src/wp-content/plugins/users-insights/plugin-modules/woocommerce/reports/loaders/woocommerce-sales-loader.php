<?php

class USIN_Woocommerce_Sales_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by('post_date');

		$query = $wpdb->prepare("SELECT COUNT(*) AS $this->total_col, post_date AS $this->label_col FROM $wpdb->posts ".
			"WHERE post_type = %s AND post_status IN ('wc-completed', 'wc-processing', 'wc-on-hold') AND post_date >= %s AND post_date <= %s GROUP BY $group_by",
			USIN_Woocommerce::ORDER_POST_TYPE, $this->get_period_start(), $this->get_period_end());

		return $wpdb->get_results( $query );
	}
}