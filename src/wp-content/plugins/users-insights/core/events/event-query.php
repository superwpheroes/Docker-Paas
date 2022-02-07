<?php

class USIN_Event_Query{

	const INCLUDE_TYPE = 'include';
	const EXCLUDE_TYPE = 'exclude';

	public static $join_applied = false;

	protected $field_id;
	protected $type;
	protected $table_alias;

	public function __construct($field_id, $event_type, $filter_type){
		$this->field_id = $field_id;
		$this->event_type = $event_type;
		$this->filter_type = $filter_type;
		$this->table_alias = 'usin_events_'.$event_type;
		$this->init();
	}

	protected function init(){
		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_query_filter_'.$this->field_id, array($this, 'apply_filters'), 10, 2);
	}

	public function filter_db_map($db_map){
		$db_map[$this->field_id] = array('db_ref'=>'', 'db_table'=>$this->table_alias, 'no_select'=>true);
		return $db_map;
	}

	public function filter_query_joins($query_joins, $table){
		if($table == $this->table_alias && !self::$join_applied){
			global $wpdb;
			$db_table = USIN_Event::get_table_name();
			$query_joins .= $wpdb->prepare(" LEFT JOIN $db_table AS $this->table_alias".
				" ON $wpdb->users.ID = $this->table_alias.user_id AND $this->table_alias.event_type = %s", $this->event_type);
			self::$join_applied = true;
		}

		return $query_joins;
	}

	public function apply_filters($custom_query_data, $filter){
		global $wpdb;
		$condition = strval($filter->condition);

		if($this->filter_type == self::INCLUDE_TYPE){
			$custom_query_data['where'] = $wpdb->prepare(" AND FIND_IN_SET(%s, $this->table_alias.items)", $filter->condition);
		}elseif($this->filter_type == self::EXCLUDE_TYPE){
			$custom_query_data['where'] = $wpdb->prepare(" AND ($this->table_alias.items IS NULL OR NOT FIND_IN_SET(%s, $this->table_alias.items))", $filter->condition);
		}

		return $custom_query_data;
	}



}