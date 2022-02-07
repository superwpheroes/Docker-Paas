<?php

class USIN_Woocommerce_Yith_Wishlists_Query{

	protected $list_joins_set = false;
	protected $wishlist_table = 'yiwl_lists';

	public function init(){

		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_db_aggregate_columns', array($this, 'filter_aggregate_columns'));
		add_filter('usin_custom_query_filter', array($this, 'apply_filters'), 10, 2);
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
	}

	public function filter_db_map($db_map){
		$db_map['yiwl_product_num'] = array('db_ref'=>'yiwl_product_num', 'db_table'=>$this->wishlist_table, 'null_to_zero'=>true, 'set_alias'=>false, 'custom_select'=>true, 'no_ref'=>true);
		$db_map['yiwl_has_wishlist_product'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		return $db_map;
	}

	public function filter_query_select($query_select, $field){
		if($field == 'yiwl_product_num'){
			$query_select="COUNT(DISTINCT($this->wishlist_table.ID)) as $field";
		}
		return $query_select;
	}

	public function filter_aggregate_columns($columns){
		$columns[]='yiwl_product_num';
		return $columns;
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

		$table = $wpdb->prefix.'yith_wcwl';
		return " LEFT JOIN $table AS $this->wishlist_table ON $wpdb->users.ID = $this->wishlist_table.user_id";
	}
	
	public function apply_filters($custom_query_data, $filter){
		
		if($filter->by != 'yiwl_has_wishlist_product'){
			return $custom_query_data;
		}

		global $wpdb;

		$custom_query_data['joins'] = $this->get_lists_join();
		$custom_query_data['having'] = $wpdb->prepare(" AND SUM($this->wishlist_table.prod_id = %d) > 0", $filter->condition);

		return $custom_query_data;
	}

}