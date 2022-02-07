<?php

class USIN_Woocommerce_Query{

	protected $order_post_type;
	protected $has_ordered_join_applied = false;
	protected $has_order_status_join_applied = false;
	protected $coupon_join_applied = false;
	protected $placed_orders_count = 0;

	public function __construct($order_post_type){
		$this->order_post_type = $order_post_type;
	}

	public function init(){
		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_query_filter', array($this, 'apply_filters'), 10, 2);
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
		add_filter('usin_query_fields_without', array($this, 'filter_fields_without'));
		add_filter('usin_users_raw_data', array($this, 'filter_raw_db_data'));
		add_filter('usin_user_db_data', array($this, 'replace_country_code_with_name'));
		add_filter('usin_custom_query_filter_placed_order', array($this, 'apply_placed_order_filter'), 10, 2);

		$billing_keys = array('billing_country', 'billing_state', 'billing_city');
		foreach ($billing_keys as $key ) {
			$meta_query = new USIN_Meta_Query($key, 'text', 'wc_');
			$meta_query->init();
		}
	}

	public function filter_db_map($db_map){
		$db_map['order_num'] = array('db_ref'=>'order_num', 'db_table'=>'orders', 'null_to_zero'=>true, 'set_alias'=>true);
		$db_map['has_ordered'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		$db_map['placed_order'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		$db_map['has_order_status'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		$db_map['has_used_coupon'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		$db_map['last_order'] = array('db_ref'=>'last_order', 'db_table'=>'orders', 'nulls_last'=>true, 'cast'=>'DATETIME');
		$db_map['first_order'] = array('db_ref'=>'first_order', 'db_table'=>'orders', 'nulls_last'=>true, 'cast'=>'DATETIME');
		$db_map['lifetime_value'] = array('db_ref'=>'value', 'db_table'=>'lifetime_values', 'null_to_zero'=>true, 'custom_select'=>true, 'cast'=>'DECIMAL', 'set_alias'=>true);
		$db_map['reviews'] = array('db_ref'=>'reviews_num', 'db_table'=>'reviews', 'null_to_zero'=>true, 'set_alias'=>true);
		return $db_map;
	}
	
	public function filter_query_select($query_select, $field){
		if($field == 'lifetime_value'){
			$query_select='CAST(IFNULL(lifetime_values.value, 0) AS DECIMAL(10,2))';
		}
		return $query_select;
	}

	public function filter_query_joins($query_joins, $table){
		global $wpdb;

		if($table === 'orders'){
			$query_joins .= " LEFT JOIN (".$this->get_orders_select().") AS orders ON $wpdb->users.ID = orders.user_id";
		}elseif ($table === 'lifetime_values') {
			$query_joins .= " LEFT JOIN (".$this->get_lifetime_values_select().") AS lifetime_values ON $wpdb->users.ID = lifetime_values.user_id";
		}elseif ($table === 'reviews') {
			$query_joins.= " LEFT JOIN (SELECT count(comment_ID) as reviews_num, user_id FROM $wpdb->comments ".
			"INNER JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID AND $wpdb->posts.post_type = 'product' ".
			"GROUP BY user_id) AS reviews ON $wpdb->users.ID = reviews.user_id";
		}

		return $query_joins;
	}
	
	public function filter_fields_without($fields_without){
		return array_merge($fields_without, array('order_num', 'first_order', 'last_order', 'lifetime_value'));
	}
	
	public function filter_raw_db_data($data){
		global $wpdb;
		$orders = array();
		
		if(!empty($data)){
			
			//number of orders
			$user_ids = wp_list_pluck($data, 'ID');

			$order_field_defaults = array(
				'order_num' => 0,
				'first_order' => null,
				'last_order' => null
			);

			foreach ($order_field_defaults as $field_id => $default_value) {
				if($this->should_load_field_data($field_id, $data)){
					$orders = $this->get_orders($user_ids);
					foreach ($data as &$user_data) {
						$user_id = intval($user_data->ID);
						$user_data->$field_id = isset($orders[$user_id]) ? $orders[$user_id]->$field_id : $default_value;
					}
				}
			}
			
			//lifeime value
			if($this->should_load_field_data('lifetime_value', $data)){
				$lt_values = $this->get_lifetime_values($user_ids);
				foreach ($data as &$user_data) {
					$user_id = intval($user_data->ID);
					$val = isset($lt_values[$user_id]) ? $lt_values[$user_id]->value : 0;
					$user_data->lifetime_value = number_format($val, 2);
				}
			}
		}
		
		
		return $data;
	}
	
	protected function should_load_field_data($field_id, $current_data){
		if(!empty($current_data) && !isset($current_data[0]->$field_id) &&
			usin_options()->is_field_visible($field_id)){
				return true;
			}
		return false;
	}
	
	protected function get_orders($user_ids){
		if(!isset($this->orders)){
			global $wpdb;
			$query = $this->get_orders_select($user_ids);
			$orders = $wpdb->get_results( $query );
			$this->orders = $this->set_user_id_as_array_index($orders);
		}
		return $this->orders;
	}
	
	protected function get_lifetime_values($user_ids){
		global $wpdb;
		$query = $this->get_lifetime_values_select($user_ids);
		$lt_values = $wpdb->get_results( $query );
		return $this->set_user_id_as_array_index($lt_values);
	}
	
	protected function get_orders_select($for_users = null){
		global $wpdb;
		
		$query = "SELECT count(ID) as order_num,  MIN(post_date) as first_order, MAX(post_date) as last_order,".
			" $wpdb->postmeta.meta_value as user_id FROM $wpdb->posts".
			" INNER JOIN $wpdb->postmeta on $wpdb->posts.ID = $wpdb->postmeta.post_id".
			" WHERE $wpdb->postmeta.meta_key = '_customer_user' AND $wpdb->posts.post_type = '$this->order_post_type'";
			
		if(!empty($for_users)){
			$query .= " AND $wpdb->postmeta.meta_value IN (".implode(",", $for_users).")";
		}
		
		$query .=" GROUP BY user_id";
		return $query;
	}
	
	protected function set_user_id_as_array_index($arr){
		$new_arr = array();
		foreach ($arr as $val) {
			$user_id = intval($val->user_id);
			$new_arr[$user_id] = $val;
		}
		return $new_arr;
	}
	
	protected function get_lifetime_values_select($for_users = null){
		global $wpdb;
		
		$query = "SELECT SUM(meta2.meta_value) AS value, meta.meta_value AS user_id ".
			"FROM $wpdb->posts as posts ".
			"LEFT JOIN $wpdb->postmeta AS meta ON posts.ID = meta.post_id ".
			"LEFT JOIN $wpdb->postmeta AS meta2 ON posts.ID = meta2.post_id ".
			"WHERE   meta.meta_key       = '_customer_user' ".
			"AND     posts.post_type     = '$this->order_post_type' ".
			"AND     posts.post_status   IN ( 'wc-completed', 'wc-processing' ) ".
			"AND     meta2.meta_key      = '_order_total' ";
		
		if(!empty($for_users)){
			$query .= " AND meta.meta_value IN (".implode(",", $for_users).")";
		}
				
		$query .= " GROUP BY meta.meta_value";
		
		return $query;
	}



	public function apply_filters($custom_query_data, $filter){
		global $wpdb;
		
		if(in_array($filter->operator, array('include', 'exclude'))){
			global $wpdb;
			
			$operator = $filter->operator == 'include' ? '>' : '=';

			if($filter->by == 'has_ordered'){
				
				if(!$this->has_ordered_join_applied){
					//apply the joins only once, even when this type of filter is applied multiple times
					$custom_query_data['joins'] .= 
						" INNER JOIN $wpdb->postmeta AS wpm ON $wpdb->users.ID = wpm.meta_value".
						" INNER JOIN $wpdb->posts AS woop ON wpm.post_id = woop.ID AND woop.post_type = '$this->order_post_type'".
						" INNER JOIN ".$wpdb->prefix."woocommerce_order_items AS woi ON woop.ID =  woi.order_id".
						" INNER JOIN ".$wpdb->prefix."woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id";

					$this->has_ordered_join_applied = true;
				}
				

				$custom_query_data['where'] = " AND wpm.meta_key = '_customer_user' AND woim.meta_key = '_product_id'";

				$custom_query_data['having'] = $wpdb->prepare(" AND SUM(woim.meta_value IN (%d)) $operator 0", $filter->condition);


			}elseif($filter->by == 'has_order_status'){

				if(!$this->has_order_status_join_applied){
					//apply the joins only once, even when this type of filter is applied multiple times
					$custom_query_data['joins'] .=
						" INNER JOIN $wpdb->postmeta AS wsm ON $wpdb->users.ID = wsm.meta_value".
						" INNER JOIN $wpdb->posts AS wsp ON wsm.post_id = wsp.ID";

					$this->has_order_status_join_applied = true;
				}


				$custom_query_data['where'] = " AND wsm.meta_key = '_customer_user'";

				$custom_query_data['having'] = $wpdb->prepare(" AND SUM(wsp.post_status IN (%s)) $operator 0", $filter->condition);
			
			}
		}elseif($filter->by == 'has_used_coupon'){
			if(!$this->coupon_join_applied){
				$custom_query_data['joins'] .= 
					" INNER JOIN $wpdb->postmeta AS wccm ON $wpdb->users.ID = wccm.meta_value".
					" INNER JOIN $wpdb->posts AS wccp ON wccm.post_id = wccp.ID".
					" INNER JOIN ".$wpdb->prefix."woocommerce_order_items AS wc_coupons ON wccp.ID =  wc_coupons.order_id";
				$this->coupon_join_applied = true;
			}
			
			$custom_query_data['where'] = " AND wccm.meta_key = '_customer_user' AND wc_coupons.order_item_type = 'coupon'";
			$custom_query_data['having'] = $wpdb->prepare(" AND SUM(wc_coupons.order_item_name = %s) > 0", $filter->condition);
			
		}

		return $custom_query_data;
	}

	public function apply_placed_order_filter($custom_query_data, $filter){
		global $wpdb;
		$joins = array();
		$wheres = array("WHERE 1 = 1", "o.post_type = '$this->order_post_type'");

		foreach ($filter->condition as $condition ) {
			switch ($condition->id) {
				case 'status':
					$wheres[]= $wpdb->prepare("o.post_status = %s", $condition->val);
					break;
				case 'date':
					if(isset($condition->val[0])){
						//or just use DATE()
						$wheres[]= $wpdb->prepare("DATE(o.post_date) >= %s", $condition->val[0]);
					}
					if(isset($condition->val[1])){
						$wheres[]= $wpdb->prepare("DATE(o.post_date) <= %s", $condition->val[1]);
					}
					break;
				case 'total':
					$joins[]="INNER JOIN $wpdb->postmeta t ON o.ID = t.post_id AND t.meta_key = '_order_total'";
					if(isset($condition->val[0])){
						$wheres[]= $wpdb->prepare("t.meta_value >= %f", $condition->val[0]);
					}
					if(isset($condition->val[1])){
						$wheres[]= $wpdb->prepare("t.meta_value <= %f", $condition->val[1]);
					}
					break;
				case 'product':
					$joins[]="INNER JOIN ".$wpdb->prefix."woocommerce_order_items AS woi ON o.ID =  woi.order_id";
					$joins[]="INNER JOIN ".$wpdb->prefix."woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id";
					$wheres[]= $wpdb->prepare("woim.meta_value = %d", $condition->val);
					break;
			}
		}

		$table_name = "placed_order_".$this->placed_orders_count;

		$custom_query_data['joins'] .= " INNER JOIN ( 
				SELECT c.meta_value AS user_id FROM $wpdb->posts AS o
				INNER JOIN $wpdb->postmeta c ON o.ID = c.post_id AND c.meta_key = '_customer_user' ".
				implode(" ", $joins)." ".
				implode(" AND ", $wheres).
			") AS $table_name ON $wpdb->users.ID = $table_name.user_id
		";


		$this->placed_orders_count++;

		return $custom_query_data;
	}


	public function replace_country_code_with_name($user_data){
		if(!empty($user_data->wc_billing_country)){
			$user_data->wc_billing_country = USIN_Woocommerce::get_wc_country_name_by_code($user_data->wc_billing_country);
		}

		return $user_data;
	}
	
	
	/**
	 * Resets the query options - this should be called when more than one
	 * query is performed per http request
	 */
	public function reset(){
		unset($this->orders);
		$this->has_ordered_join_applied = false;
		$this->has_order_status_join_applied = false;
		$this->coupon_join_applied = false;
	}

}