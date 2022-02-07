<?php

/**
 * User meta field query - generates the required database statements for a
 * field from the WordPress user_meta table.
 */
class USIN_Post_Query{
	
	protected $post_type = '';
	protected $db_table = '';
	
	/**
	 * [__construct description]
	 * @param string $key    the key of the field as used in the user_meta table
	 * @param string $type   the type of the field: text/number
	 * @param string $prefix optional prefix that can be used to prefix the field
	 * in the database queries. It is recommened to set a prefix to avoid conflicts
	 * with existing fields.
	 */
	public function __construct($post_type){
		$this->post_type = $post_type;
		$this->ref = $this->post_type.'_count';
		$this->db_table = $this->post_type.'_counts';
		$this->init();
	}
	
	/**
	 * Registers all of the required hooks.
	 */
	public function init(){
		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
		add_filter('usin_db_aggregate_columns', array($this, 'filter_aggregate_columns'));
	}

	public function filter_aggregate_columns($columns){
		$columns[]=$this->ref;
		return $columns;
	}

	public function filter_query_select($query_select, $field){
		if($field == $this->ref){
			$query_select="COUNT(DISTINCT($this->db_table.ID)) as $this->ref";
		}
		return $query_select;
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
			'db_ref'=>$this->ref, 
			'db_table'=>$this->db_table,
			'null_to_zero' => true,
			'set_alias'=>false, 
			'custom_select'=>true, 
			'no_ref'=>true
		);

		$db_map[$this->ref] = $map;
		
		return $db_map;
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
			$allowed_statuses = USIN_Helper::get_allowed_post_statuses('sql_string');

			$status_cond = empty($allowed_statuses) ? '' : " AND $this->db_table.post_status IN ($allowed_statuses)";
			$query_joins .= " LEFT JOIN $wpdb->posts AS $this->db_table ON ".
					"($wpdb->users.ID = $this->db_table.post_author AND $this->db_table.post_type = '$this->post_type'$status_cond)";			
		}

		return $query_joins;
	}
	
	
}