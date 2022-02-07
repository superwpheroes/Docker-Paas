<?php

class USIN_Events_Calendar_Ticket_Orders_Query{

	protected $ticket_joins_set = false;
	protected $ticket_event_joins_set = false;

	public function init(){
		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
		add_filter('usin_db_aggregate_columns', array($this, 'filter_aggregate_columns'));
		add_filter('usin_custom_query_filter', array($this, 'apply_filters'), 10, 2);
	}


	public function filter_db_map($db_map){
		$db_map['ec_tickets'] = array('db_ref'=>'ec_tickets', 'db_table'=>'ec_ticket_posts', 'null_to_zero'=>true, 'set_alias'=>false, 'custom_select'=>true, 'no_ref'=>true);
		$db_map['ec_has_ordered_ticket'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		$db_map['ec_has_not_ordered_ticket'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		return $db_map;
	}

	public function filter_query_select($query_select, $field){
		if($field == 'ec_tickets'){
			$query_select='COUNT(DISTINCT(ec_ticket_posts.ID)) AS ec_tickets';
		}
		return $query_select;
	}

	public function filter_aggregate_columns($columns){
		$columns[]='ec_tickets';
		return $columns;
	}
	
	public function filter_query_joins($query_joins, $table){
		if($table == 'ec_ticket_posts'){
			$query_joins.=$this->get_ticket_joins();
		}
		return $query_joins;
	}

	protected function get_ticket_joins(){
		global $wpdb;

		if($this->ticket_joins_set){
			return '';
		}
		$this->ticket_joins_set = true;

		$post_types = USIN_Helper::array_to_sql_string(USIN_Events_Calendar::$et_post_types);
		
		$subquery = "SELECT ID, pm.meta_value AS user_id FROM $wpdb->posts p".
			" INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_tribe_tickets_attendee_user_id'".
			" WHERE p.post_type IN ($post_types)";

		return " LEFT JOIN ($subquery) AS ec_ticket_posts ON $wpdb->users.ID = ec_ticket_posts.user_id";
	}

	protected function get_ticket_event_joins(){
		global $wpdb;

		if($this->ticket_event_joins_set){
			return '';
		}
		$this->ticket_event_joins_set = true;

		return $this->get_ticket_joins()." LEFT JOIN $wpdb->postmeta ticket_events".
			" ON ec_ticket_posts.ID = ticket_events.post_id AND ticket_events.meta_key IN ('_tribe_eddticket_event', '_tribe_wooticket_event', '_tribe_tpp_event')";

	}

	public function apply_filters($custom_query_data, $filter){
		
		if(!in_array($filter->by, array('ec_has_ordered_ticket', 'ec_has_not_ordered_ticket'))){
			return $custom_query_data;
		}

		global $wpdb;

		$custom_query_data['joins'] = $this->get_ticket_event_joins();
		if($filter->by == 'ec_has_ordered_ticket'){
			$custom_query_data['having'] = $wpdb->prepare(" AND SUM(ticket_events.meta_value = %s) > 0", $filter->condition);
		}else{
			$custom_query_data['having'] = $wpdb->prepare(" AND (SUM(ticket_events.meta_value = %s) = 0 OR SUM(ticket_events.meta_value IS NULL) > 0)", $filter->condition);
		}

		return $custom_query_data;
	}

}