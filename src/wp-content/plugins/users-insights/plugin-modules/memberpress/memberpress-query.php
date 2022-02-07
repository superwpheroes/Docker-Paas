<?php

if(!defined( 'ABSPATH' )){
	exit;
}

class USIN_MemberPress_Query{

	const NEVER_DATETIME = '0000-00-00 00:00:00';

	protected $has_membership_count = 0;

	public function __construct($custom_fields){
		$this->custom_fields = $custom_fields;
	}

	public function init(){
		$this->init_meta_query();

		add_filter('usin_db_map', array($this, 'filter_db_map'));
		add_filter('usin_custom_select', array($this, 'filter_query_select'), 10, 2);
		add_filter('usin_query_join_table', array($this, 'filter_query_joins'), 10, 2);
		add_filter('usin_db_aggregate_columns', array($this, 'filter_aggregate_columns'));
		add_filter('usin_custom_query_filter_mepr_has_membership', array($this, 'apply_has_membership_filter'), 10, 2);
		add_filter('usin_custom_query_filter_mepr_has_used_coupon', array($this, 'apply_has_used_coupon_filter'), 10, 2);

	}

	protected function init_meta_query(){
		foreach ($this->custom_fields as $field ) {
			$query = new USIN_Meta_Query($field->meta_key, $field->type, USIN_MemberPress::FIELD_PREFIX);
			$query->init();
		}
	}


	public function filter_db_map($db_map){
		$db_map['mepr_status'] = array('db_ref'=>'mepr_status', 'db_table'=>'mepr_memberships', 'custom_select'=>true, 'no_ref'=>true, 'set_alias'=>true);
		$db_map['mepr_ltv'] = array('db_ref'=>'total_spent', 'db_table'=>'mepr_members');
		$db_map['mepr_transaction_count'] = array('db_ref'=>'mepr_transaction_count', 'db_table'=>'mepr_transactions', 'custom_select'=>true, 'no_ref'=>true, 'set_alias'=>true);
		$db_map['mepr_membership_count'] = array('db_ref'=>'mepr_membership_count', 'db_table'=>'mepr_memberships', 'custom_select'=>true, 'no_ref'=>true, 'set_alias'=>true);
		$db_map['mepr_first_transaction'] = array('db_ref'=>'first_transaction_local', 'db_table'=>'mepr_transaction_dates', 'nulls_last'=>true);
		$db_map['mepr_last_transaction'] = array('db_ref'=>'last_transaction_local', 'db_table'=>'mepr_transaction_dates', 'nulls_last'=>true);
		$db_map['mepr_has_membership'] = array('db_ref'=>'', 'db_table'=>'', 'no_select'=>true);
		$db_map['mepr_has_used_coupon'] = array('db_ref'=>'', 'db_table'=>'mepr_transactions', 'no_select'=>true);
		
		return $db_map;
	}

	
	public function filter_query_select($query_select, $field){
		if($field == 'mepr_status'){
			$query_select="CASE WHEN SUM(mepr_memberships.is_active) >= 1 THEN 'active' ELSE 'inactive' END";
		}elseif($field == 'mepr_membership_count'){
			$query_select='COUNT(DISTINCT mepr_memberships.id)';
		}elseif($field == 'mepr_transaction_count'){
			$query_select='COUNT(DISTINCT mepr_transactions.id)';
		}
		return $query_select;
	}


	public function filter_query_joins($query_joins, $table){
		global $wpdb;

		if(strpos($table, 'mepr_')!==0){
			return $query_joins;
		}

		switch($table){
			case 'mepr_members':
				$query_joins .= " LEFT JOIN ".$wpdb->prefix."mepr_members AS mepr_members ON mepr_members.user_id=$wpdb->users.ID";
				break;

			case 'mepr_memberships':
				$subquery = self::get_memberships_query();
				$query_joins .= " LEFT JOIN ($subquery) AS mepr_memberships ON mepr_memberships.user_id=$wpdb->users.ID"; 
				break;

			case 'mepr_transactions':
				$subquery = self::get_transactions_query();
				$query_joins .= " LEFT JOIN ($subquery) AS mepr_transactions ON mepr_transactions.user_id=$wpdb->users.ID"; 
				break;
				
			case 'mepr_transaction_dates':
				$first_trans_select = self::get_gmt_offset_date_select('MIN(created_at)');
				$last_trans_select = self::get_gmt_offset_date_select('MAX(created_at)');
				$subquery = "SELECT user_id, $first_trans_select AS first_transaction_local, $last_trans_select AS last_transaction_local ".
					" FROM ".$wpdb->prefix."mepr_transactions WHERE ".self::get_payment_transaction_condition()." GROUP BY user_id";
				$query_joins .= " LEFT JOIN ($subquery) AS mepr_transaction_dates ON mepr_transaction_dates.user_id=$wpdb->users.ID";
				break;
		}

		return $query_joins;
	}

	public function filter_aggregate_columns($columns){
		$columns[]='mepr_status';
		$columns[]='mepr_membership_count';
		$columns[]='mepr_transaction_count';
		return $columns;
	}

	public function apply_has_membership_filter($custom_query_data, $filter){
		global $wpdb;
		$wheres = array();

		$table_alias = "mepr_memberships_".$this->has_membership_count;

		foreach ($filter->condition as $condition ) {
			switch ($condition->id) {
				case 'status':
					$val = $condition->val == 'active' ? 1 : 0;
					$wheres[]= "$table_alias.is_active = $val";
					break;

				case 'date_created':
					if(isset($condition->val[0])){
						$wheres[]= $wpdb->prepare("DATE($table_alias.created_at_local) >= %s", $condition->val[0]);
					}
					if(isset($condition->val[1])){
						$wheres[]= $wpdb->prepare("DATE($table_alias.created_at_local) <= %s", $condition->val[1]);
					}
					break;

				case 'date_expiring':
					$min_date_condition = isset($condition->val[0]) ? $condition->val[0] : null;
					$max_date_condition = isset($condition->val[1]) ? $condition->val[1] : null;

					if($min_date_condition && $max_date_condition){
						//just apply the limits
						$wheres[]= $wpdb->prepare("DATE($table_alias.expires_at_local) >= %s AND DATE($table_alias.expires_at_local) <= %s", 
							$min_date_condition, $max_date_condition);
					}elseif($min_date_condition && !$max_date_condition){
						//there is no upper limit, include lifetime memberships
						$wheres[]= $wpdb->prepare("(DATE($table_alias.expires_at_local) >= %s OR $table_alias.expires_at_local = %s)", 
							$min_date_condition, self::NEVER_DATETIME);
					}elseif(!$min_date_condition && $max_date_condition){
						//exclude the lifetime memberships as a zero date will be smaller than any end date condition
						$wheres[]= $wpdb->prepare("DATE($table_alias.expires_at_local) <= %s AND $table_alias.expires_at_local != %s", 
							$max_date_condition, self::NEVER_DATETIME);
					}
					break;

				case 'product':
					$wheres[]= $wpdb->prepare("$table_alias.product_id = %d", $condition->val);
					break;
			}
		}
		
		$subquery = $this->get_memberships_query();
		$custom_query_data['joins'] .= " INNER JOIN ($subquery) AS $table_alias ON $table_alias.user_id=$wpdb->users.ID"; 
		if(!empty($wheres)){
			$custom_query_data['where'] .= " AND ".implode(" AND ", $wheres);
		}

		$this->has_membership_count++;

		return $custom_query_data;
		
	}


	public function apply_has_used_coupon_filter($custom_query_data, $filter){
		global $wpdb;
		
		$custom_query_data['having'] = $wpdb->prepare(" AND SUM(mepr_transactions.coupon_id = %d) > 0", $filter->condition);

		return $custom_query_data;
	}

	public static function get_memberships_query($for_user_id = null){
		global $wpdb;

		$additional_condition = $for_user_id === null ? '' : $wpdb->prepare(" AND user_id=%d", $for_user_id);
		$current_time_gmt = current_time('mysql', 1);

		//We define a unique membership by a transaction with unique user ID, subscription ID and membership ID.
		//The membership is active if it has at least one confirmed/complete transaction with a lifetime or future expiry.
		//is_active will be 0 or 1 for each membership
		//a date with a suffix _local means that it is converted to local time (from gmt)
		$never_datetime = self::NEVER_DATETIME;
		$created_at_select = self::get_gmt_offset_date_select("min(created_at)");
		$expires_at_select = self::get_gmt_offset_date_select("max(expires_at)");
		$query = $wpdb->prepare("SELECT id, user_id, product_id, subscription_id, $created_at_select AS created_at_local,
			CASE WHEN MIN(expires_at) = '$never_datetime' THEN MIN(expires_at) ELSE $expires_at_select END AS expires_at_local,
			COUNT(DISTINCT CASE WHEN (expires_at = '$never_datetime' OR expires_at > %s) THEN 'active' END ) AS is_active
			FROM ".$wpdb->prefix."mepr_transactions
			WHERE `status` IN ('confirmed','complete')".$additional_condition."
			GROUP BY user_id, product_id, subscription_id", $current_time_gmt);

		return $query;
	}


	public static function get_transactions_query($for_user_id = null){
		global $wpdb;

		$never_datetime = self::NEVER_DATETIME;
		$created_at_select = self::get_gmt_offset_date_select("created_at");
		$expires_at_select = self::get_gmt_offset_date_select("expires_at");

		//the dates with a suffix _local means that they are converted to local time (from gmt)
		$query = "SELECT id, user_id, `status`, product_id, total, $created_at_select AS created_at_local, coupon_id,
			CASE WHEN expires_at = '$never_datetime' THEN expires_at ELSE $expires_at_select END AS expires_at_local
			FROM ".$wpdb->prefix."mepr_transactions AS t WHERE ".self::get_payment_transaction_condition();

		if($for_user_id !== null){
			$query .= $wpdb->prepare(" AND user_id=%d", $for_user_id);
		}
			
		$query .= " ORDER BY created_at DESC";

		return $query;
	}

	protected static function get_payment_transaction_condition(){
		//this is the condition that MemberPress uses on their Transactions page
		//to load the list of transactions
		return "txn_type = 'payment' AND `status` != 'confirmed'";
	}


	public static function get_gmt_offset_date_select($date_column){
		global $wpdb;
		$offset = get_option('gmt_offset');
		return $wpdb->prepare("DATE_ADD($date_column, INTERVAL %d HOUR)", $offset);
	}

}