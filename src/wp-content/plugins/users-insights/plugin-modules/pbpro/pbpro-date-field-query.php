<?php

class USIN_Pbpro_Date_Field_Query{

	protected $key = '';
	protected $ref = '';
	protected $format = '';
	protected $db_table = '';
	
	public function __construct($key, $format){
		$this->key = $key;
		$this->format = $format;
		$this->prefix = USIN_Pbpro::PREFIX;
		$this->ref = $this->prefix.$key;
		$this->db_table = '`'.$this->ref.'_meta`';
	}
	
	/**
	 * Registers all of the required hooks.
	 */
	public function init(){
		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
		add_filter('usin_custom_query_filter_'.$this->ref, array($this, 'apply_filters'), 10, 2);
	}
	
	/**
	 * Adds the field options to the main query DB map.
	 * @param  array $db_map the default DB map options
	 * @return array         the default DB map options including the custom field
	 * options
	 */
	public function filter_db_map($db_map){
		global $wpdb;

		$map = array(
			'db_ref'=> $this->ref,
			'db_table'=>$this->db_table,
			'no_ref'=>true, // we need no_ref to be true so that ORDER BY will be done by the
			//alias name e.g. pb_date_field which is already converted into a date field.
			//If we don't have no_ref, ORDER BY will be set by $table.$ref which is the string
			//representation of the date
			'nulls_last' => true,
			'custom_select'=>true,
			'set_alias' => true
		);
		
			
		$db_map[$this->ref] = $map;
		
		return $db_map;
	}

	public function filter_query_select($query_select, $field){
		if($field == $this->ref){
			//if the value is an empty string, do not convert it to a date
			$query_select.="IF(".$this->get_table_ref()." = '', NULL, ".$this->get_cast_ref().")";
		}
		return $query_select;
	}

	public function apply_filters($custom_query_data, $filter){
		global $wpdb;
		$operators = array ('date_custom_on'=>'=', 'date_custom_after'=>'>', 'date_custom_before'=>'<');
		$operator = $operators[$filter->operator];

		$cast_ref = $this->get_cast_ref();
		//we need the casted ref part to be outside of prepare, as the MySQL formats contain some strings
		//that are recognized as prepare placeholders (such as %d)
		//we also need to check for not empty value, as Pbpro sores null values as empty strings
		$custom_query_data['where'] = " AND ".$this->get_table_ref()." != '' AND $cast_ref ".$wpdb->prepare("$operator %s", $filter->condition);

		return $custom_query_data;
	}

	/**
	 * Adds a join statement for this field to the main DB query join statement.
	 * @param  string $query_joins the default query join
	 * @return string               the default query join including the join
	 * statement for this field
	 */
	public function filter_query_joins($query_joins, $table){
		global $wpdb;
		
		if($table === $this->db_table){
			$query_joins .= " LEFT JOIN $wpdb->usermeta AS $this->db_table ON ".
					"($wpdb->users.ID = $this->db_table.user_id AND $this->db_table.meta_key = '$this->key')";			
		}

		return $query_joins;
	}

	protected function get_cast_ref(){
		return "STR_TO_DATE($this->db_table.meta_value, '$this->format')";
	}

	protected function get_table_ref(){
		return "$this->db_table.meta_value";
	}
	
}