<?php

class USIN_Edd_Order_Number_Loader extends USIN_Standard_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$subquery = $wpdb->prepare("SELECT COUNT(*) AS order_num, meta.meta_value AS customer_id FROM $wpdb->posts AS orders".
			" INNER JOIN $wpdb->postmeta AS meta ON orders.ID = meta.post_id and meta.meta_key = '_edd_payment_customer_id'".
			" WHERE post_type = %s AND post_status = 'publish' GROUP BY customer_id",
			USIN_EDD::ORDER_POST_TYPE);

		$query = "SELECT COUNT(*) as $this->total_col, order_num as $this->label_col".
			" FROM ($subquery) AS order_nums GROUP BY order_num";

		$data = $wpdb->get_results( $query );
		$data = $this->format_names($data);

		return $data;
	}

	protected function format_names($data){
		
		foreach ($data as &$row ) {
			if($row->label != __('Other', 'usin')){
				$row->label .= ' '. _n( 'sale', 'sales', intval($row->label), 'usin' );
			}
		}

		return $data;
	}

}