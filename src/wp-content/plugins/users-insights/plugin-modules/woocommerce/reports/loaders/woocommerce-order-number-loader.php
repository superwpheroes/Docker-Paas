<?php

class USIN_Woocommerce_Order_Number_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		global $wpdb;

		$subquery = "SELECT COUNT(orders.ID) AS order_num, pm.meta_value AS email FROM $wpdb->posts AS orders".
			" INNER JOIN $wpdb->postmeta AS pm ON orders.ID = pm.post_id AND pm.meta_key = '_billing_email'".
			" WHERE orders.post_type = '".USIN_Woocommerce::ORDER_POST_TYPE."'";
		
		$filter = $this->getSelectedFilter();
		if($filter != 'all'){
			$subquery .= $wpdb->prepare(" AND orders.post_status = %s", $filter);
		}

		$subquery .= " GROUP BY email";

		$query = "SELECT COUNT(*) AS $this->total_col, order_num AS $this->label_col".
			" FROM ($subquery) AS order_nums GROUP BY order_num";

		$data = $wpdb->get_results( $query );
		$data = $this->format_names($data);

		return $data;

	}

	protected function format_names($data){

		foreach ($data as &$row ) {
			if($row->label != __('Other', 'usin')){
				$row->label .= ' '. _n( 'order', 'orders', intval($row->label), 'usin' );
			}
		}

		return $data;
	}


}