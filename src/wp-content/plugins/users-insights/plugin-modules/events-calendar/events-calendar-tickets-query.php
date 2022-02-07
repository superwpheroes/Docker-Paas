<?php

class USIN_Events_Calendar_Tickets_Query{

	protected $rsvp_joins_set = false;
	protected $rsvp_statuses_joins_set = array();

	public function init(){
		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
		add_filter('usin_db_aggregate_columns', array($this, 'filter_aggregate_columns'));
		add_filter('usin_custom_query_filter', array($this, 'apply_filters'), 10, 2);
	}


	public function filter_db_map($db_map){
		$db_map['rsvp_yes'] = array('db_ref'=>'rsvp_yes', 'db_table'=>'rsvps_yes', 'null_to_zero'=>true, 'set_alias'=>false, 'custom_select'=>true, 'no_ref'=>true);
		$db_map['rsvp_no'] = array('db_ref'=>'rsvp_no', 'db_table'=>'rsvps_no', 'null_to_zero'=>true, 'set_alias'=>false, 'custom_select'=>true, 'no_ref'=>true);
		$db_map['has_rsvped'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		return $db_map;
	}

	public function filter_query_select($query_select, $field){
		if($field == 'rsvp_yes'){
			$query_select='COUNT(DISTINCT(rsvps_yes.post_id)) as rsvp_yes';
		}elseif($field == 'rsvp_no'){
			$query_select='COUNT(DISTINCT(rsvps_no.post_id)) as rsvp_no';
		}
		return $query_select;
	}

	public function filter_aggregate_columns($columns){
		$columns[]='rsvp_yes';
		$columns[]='rsvp_no';
		return $columns;
	}
	
	public function filter_query_joins($query_joins, $table){
		global $wpdb;
		if($table == 'rsvps_yes'){
			
			$query_joins.=$this->get_rsvp_post_join() . " LEFT JOIN $wpdb->postmeta AS rsvps_yes".
				" ON rsvps.ID = rsvps_yes.post_id AND rsvps_yes.meta_key = '_tribe_rsvp_status' AND rsvps_yes.meta_value='yes'";
			
		}elseif($table == 'rsvps_no'){
			
			$query_joins.=$this->get_rsvp_post_join() . " LEFT JOIN $wpdb->postmeta AS rsvps_no".
				" ON rsvps.ID = rsvps_no.post_id AND rsvps_no.meta_key = '_tribe_rsvp_status' AND rsvps_no.meta_value='no'";
			
		}
		return $query_joins;
	}

	protected function get_rsvp_post_join(){
		if($this->rsvp_joins_set){
			return '';
		}

		global $wpdb;

		$query = $wpdb->prepare(" LEFT JOIN $wpdb->posts AS rsvps".
			" ON $wpdb->users.ID = rsvps.post_author AND rsvps.post_type = %s", USIN_Events_Calendar::ET_POST_TYPE);

		$this->rsvp_joins_set = true;

		return $query;		

	}

	public function apply_filters($custom_query_data, $filter){
		
		if($filter->by != 'has_rsvped'){
			return $custom_query_data;
		}

		$event_id = $filter->condition;
		$ref = "rsvps_$event_id";
		$st_ref = "rsvp_statuses_$event_id";
		$ev_ref = "rsvp_events_$event_id";
		global $wpdb;

		if(!isset($this->rsvp_statuses_joins_set[$event_id])){
			$custom_query_data['joins'] = $wpdb->prepare(" LEFT JOIN $wpdb->posts AS $ref ON $wpdb->users.ID = $ref.post_author AND $ref.post_type = %s".
				" LEFT JOIN $wpdb->postmeta AS $ev_ref ON $ref.ID = $ev_ref.post_id AND $ev_ref.meta_key = '_tribe_rsvp_event' AND $ev_ref.meta_value = %d".
				" LEFT JOIN $wpdb->postmeta AS $st_ref ON $ev_ref.post_id = $st_ref.post_id AND $st_ref.meta_key = '_tribe_rsvp_status'",
				USIN_Events_Calendar::ET_POST_TYPE, $event_id);
	
			$this->rsvp_statuses_joins_set[$event_id] = true;
		}

		switch ($filter->operator) {
			case 'ec_is_yes':
			case 'ec_is_no':
				$condition = $filter->operator == 'ec_is_yes' ? 'yes' : 'no';
				$custom_query_data['having'] = $wpdb->prepare(" AND SUM($st_ref.meta_value = %s) > 0", $condition);
				break;
			
			case 'ec_is_any':
				$custom_query_data['having'] = " AND SUM($st_ref.meta_value IS NOT NULL) > 0";
				break;

			case 'ec_is_none':
				$custom_query_data['having'] = " AND SUM($st_ref.meta_value IS NOT NULL) = 0";
				break;
		}
		
		return $custom_query_data;
	}

}