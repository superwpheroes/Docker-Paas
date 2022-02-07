<?php

class USIN_Woocommerce_Order_Statuses_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		global $wpdb;

		$status_query = '';

		if($wc_statuses = $this->get_statuses()){
			$status_query = " AND post_status IN (".USIN_Helper::array_to_sql_string(array_keys($wc_statuses)).")";
		}

		$query = "SELECT COUNT(*) AS $this->total_col, post_status AS $this->label_col FROM $wpdb->posts".
			" WHERE post_type = '".USIN_Woocommerce::ORDER_POST_TYPE."'".$status_query." GROUP BY post_status";
		$data = $wpdb->get_results( $query );

		return $this->apply_status_names($data);

	}

	protected function apply_status_names($data){
		if($wc_statuses = $this->get_statuses()){

			foreach ($data as $row ) {
				if(isset($wc_statuses[$row->label])){
					$row->label = $wc_statuses[$row->label];
				}
			}
		}
		return $data;
	}

	protected function get_statuses(){
		if(function_exists('wc_get_order_statuses')){
			return wc_get_order_statuses();
		}
	}

}