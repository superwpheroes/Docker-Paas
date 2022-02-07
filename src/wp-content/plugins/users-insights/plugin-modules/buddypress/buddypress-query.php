<?php

class USIN_BuddyPress_Query{
	
	protected $prefix;
	protected $xprofile;
	protected $xp_table_prefix = 'xprofile_';
	
	public function __construct($xprofile){
		$this->xprofile = $xprofile;
	}

	public function init(){
		$this->prefix = self::get_prefix();
		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
		add_filter('usin_db_aggregate_columns', array($this, 'filter_aggregate_columns'));
		add_filter('usin_single_user_query_fields', array($this, 'remove_xprofile_fields_from_single_user_query'));
		add_filter('usin_single_user_db_data', array($this, 'add_xprofile_fields_to_single_user_data'));
	}

	public static function get_prefix(){
		global $wpdb;
		return is_multisite() ? $wpdb->base_prefix : $wpdb->prefix;
	}

	protected function is_bp_feature_active($feature){
		return USIN_BuddyPress::is_bp_feature_active($feature);
	}

	public function filter_db_map($db_map){
		global $wpdb;

		if($this->is_bp_feature_active('groups')){
			$db_map['groups'] = array('db_ref'=>'groups', 'db_table'=>'gm', 'null_to_zero'=>true, 'set_alias'=>true);
			$db_map['groups_created'] = array('db_ref'=>'groups_created', 'db_table'=>'gr', 'null_to_zero'=>true, 'set_alias'=>true);
			$db_map['bp_group'] = array('db_ref'=>'group_id', 'db_table'=>'bpg', 'set_alias'=>true);
		}
			
		if($this->is_bp_feature_active('friends')){
			$db_map['friends'] = array('db_ref'=>'meta_value', 'db_table'=>'friends_meta', 'null_to_zero'=>true, 'cast'=>'DECIMAL', 'custom_select'=>true);
		}

		if($this->is_bp_feature_active('activity')){
			$db_map['activity_updates'] = array('db_ref'=>'activity_updates', 'db_table'=>'au', 'null_to_zero'=>true, 'set_alias'=>true);
		}
		
		//xprofile fields
		$fields = $this->xprofile->get_fields();
		foreach ($fields as $field ) {
			$map = array('db_ref'=>'value', 'db_table'=>$this->xp_table_prefix.$field['bpx_id'], 'nulls_last'=>true);
			if($field['filter']['type']=='number'){
				$map['cast'] = 'DECIMAL';
			}
			if($field['filter']['type']=='date'){
				$map['cast'] = 'DATETIME';
			}
			
			$db_map[$field['id']] = $map;
		}
		
		return $db_map;
	}

	public function filter_query_select($query_select, $field){
		if($field == 'friends'){
			if($this->is_bp_feature_active('friends')){
				$query_select.="IFNULL(cast(friends_meta.meta_value AS DECIMAL),0)";
			}
		}
		return $query_select;
	}
	
	public function filter_aggregate_columns($columns){
		$columns[]='bp_group';
		return $columns;
	}

	public function filter_query_joins($query_joins, $table){
		global $wpdb;

		if(strpos($table, $this->xp_table_prefix) === 0){
			//xprofile field
			$field_id = (int)str_replace($this->xp_table_prefix, '', $table);
			$query_joins .= " LEFT JOIN ".self::get_xprofile_table_name()." AS $table ON".
				" $wpdb->users.ID = $table.user_id AND $table.field_id = $field_id";
		}else{
			switch ($table) {
				case 'gm':
					$query_joins .= " LEFT JOIN (SELECT user_id, COUNT(".$this->prefix."bp_groups_members.id) as groups FROM ".$this->prefix."bp_groups_members GROUP BY user_id) gm on $wpdb->users.ID = gm.user_id";
					break;
				case 'gr':
					$query_joins .= " LEFT JOIN (SELECT creator_id, COUNT(".$this->prefix."bp_groups.id) as groups_created FROM ".$this->prefix."bp_groups GROUP BY creator_id) gr on $wpdb->users.ID = gr.creator_id";
					break;
				case 'friends_meta':
					$query_joins .= " LEFT JOIN $wpdb->usermeta AS friends_meta ON ".
						"($wpdb->users.ID = friends_meta.user_id AND friends_meta.meta_key = 'total_friend_count')";
					break;
				case 'au':
					$query_joins .= " LEFT JOIN (SELECT user_id, COUNT(".$this->prefix."bp_activity.id) as activity_updates FROM ".$this->prefix."bp_activity WHERE type='activity_update' GROUP BY user_id) au on $wpdb->users.ID = au.user_id";
					break;
				case 'bpg':
					$query_joins .= " LEFT JOIN ".$this->prefix."bp_groups_members AS bpg ON $wpdb->users.ID = bpg.user_id AND bpg.is_confirmed = 1";
					break;
				
			}
		}
			return $query_joins;
	}

	public static function get_field_counts($field_id, $total_col, $label_col){
		global $wpdb;
		
		$query = $wpdb->prepare("SELECT `value` AS $label_col, COUNT(*) AS $total_col FROM ".self::get_xprofile_table_name().
			" WHERE field_id = %d GROUP BY $label_col", $field_id);

		return $wpdb->get_results($query);
	}

	public static function get_xprofile_table_name(){
		return self::get_prefix().'bp_xprofile_data';
	}

	/**
	 * Remove the xProfile fields from the single user query. In this way we'll
	 * avoid having too many table joins in the case of a large number of profile
	 * fields. We'll use another method to load these in a single query instead.
	 */
	public function remove_xprofile_fields_from_single_user_query($fields){

		foreach ($fields as $key => $field) {
			if(strpos($field, USIN_BuddyPress_XProfile::$field_prefix) === 0){
				unset($fields[$key]);
			}
		}
		$fields = array_values($fields);

		return $fields;
	}

	/**
	 * Loads all the xProfile fields within a single database query and applies
	 * the values to the user data object.
	 */
	public function add_xprofile_fields_to_single_user_data($user_data){
		global $wpdb;

		$xprofile_table = self::get_xprofile_table_name();
		$query = $wpdb->prepare("SELECT * FROM $xprofile_table WHERE user_id = %d", $user_data->ID);
		$bp_rows = $wpdb->get_results($query);

		$general_fields = usin_options()->get_field_ids_by_field_type(USIN_BuddyPress_XProfile::$field_type);
		
		foreach ($bp_rows as $row) {
			$ref = USIN_BuddyPress_XProfile::$field_prefix.$row->field_id;
			if(in_array($ref, $general_fields)){
				$user_data->$ref = $row->value;
			}
		}

		return $user_data;
	}

}