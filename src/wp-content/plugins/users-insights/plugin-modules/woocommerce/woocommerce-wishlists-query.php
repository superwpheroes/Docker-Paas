<?php

class USIN_Woocommerce_Wishlists_Query{

	protected $list_joins_set = false;
	protected $wishlist_table = 'wc_wishlists';

	public function init(){

		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_query_filter', array($this, 'apply_filters'), 10, 2);
	}

	public function filter_db_map($db_map){
		$db_map['wc_has_wishlist_product'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		return $db_map;
	}

	public function filter_query_joins($query_joins, $table){
		global $wpdb;
		if($table == $this->wishlist_table){
			$query_joins.=$this->get_lists_join();
		}
		return $query_joins;
	}

	protected function get_lists_join(){
		if($this->list_joins_set){
			return '';
		}
		$this->list_joins_set = true;
		global $wpdb;

		$subquery = $wpdb->prepare("SELECT p.ID, wl_users.meta_value AS user_id, wl_items.meta_value AS items FROM $wpdb->posts p".
			" INNER JOIN $wpdb->postmeta wl_users ON p.ID = wl_users.post_id AND wl_users.meta_key = '_wishlist_owner'".
			" INNER JOIN $wpdb->postmeta wl_items ON p.ID = wl_items.post_id AND wl_items.meta_key = '_wishlist_items'".
			" WHERE p.post_type = %s", USIN_Woocommerce::WC_WISHLIST_POST_TYPE);

		return " LEFT JOIN ($subquery) AS $this->wishlist_table ON $wpdb->users.ID = $this->wishlist_table.user_id";
	}
	
	public function apply_filters($custom_query_data, $filter){
		
		if($filter->by != 'wc_has_wishlist_product'){
			return $custom_query_data;
		}

		global $wpdb;

		$condition = $this->get_serialized_value('product_id', (int)$filter->condition);

		$custom_query_data['joins'] = $this->get_lists_join();
		$custom_query_data['having'] = $wpdb->prepare(" AND SUM($this->wishlist_table.items LIKE '%%%s%%') > 0", $condition);

		return $custom_query_data;
	}

	protected function get_serialized_value($key, $value){
	    $arr = array($key=>$value);
	    $ser_arr = serialize($arr);
	    
	    //strip the array parts of the serialized string
	    $ser_arr = str_replace('a:1:{', '', $ser_arr);
	    $ser_arr = str_replace('}', '', $ser_arr);
	    
	    return $ser_arr;
	}

}