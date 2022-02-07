<?php

class USIN_Edd_Product_Sales_Loader extends USIN_Standard_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$query = "SELECT meta.meta_value AS $this->total_col, posts.post_title as $this->label_col FROM $wpdb->posts AS posts".
			" INNER JOIN $wpdb->postmeta AS meta ON posts.ID = meta.post_id AND meta_key='_edd_download_sales'".
			" WHERE posts.post_type = 'download' LIMIT $this->max_items";

		return $wpdb->get_results( $query );
	}


}