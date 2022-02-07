<?php

class USIN_Edd_Order_Statuses_Loader extends USIN_Standard_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$query = $wpdb->prepare("SELECT COUNT(*) AS $this->total_col, post_status AS $this->label_col".
			" FROM $wpdb->posts WHERE post_type = %s GROUP BY post_status",
			USIN_EDD::ORDER_POST_TYPE);
		$data = $wpdb->get_results( $query );

		$statuses = USIN_EDD::get_order_status_options(true);
		return $this->match_ids_to_names($data, $statuses, true);
	
	}

}