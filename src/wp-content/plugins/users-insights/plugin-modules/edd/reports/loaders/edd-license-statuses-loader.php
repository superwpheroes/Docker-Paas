<?php

class USIN_Edd_License_Statuses_Loader extends USIN_Standard_Report_Loader {

	/**
	 * NOTE: Do not compare to the EDD license statuses number, as their 
	 * inactive and expired licenses overlap
	 *
	 * @return void
	 */
	protected function load_data(){
		
		if($this->uses_licenses_db_table()){
			return $this->get_results();
		}else{
			return $this->get_legacy_db_results();
		}
	}


	protected function get_results(){
		global $wpdb;

		$query = "SELECT COUNT(*) AS $this->total_col, `status` AS $this->label_col FROM ".$wpdb->prefix."edd_licenses".
		" WHERE parent = 0 GROUP BY status ORDER BY $this->total_col DESC";

		return $wpdb->get_results( $query );
	}

	protected function get_legacy_db_results(){
		global $wpdb;

		$query = "SELECT COUNT(*) as $this->total_col, meta_value as $this->label_col FROM $wpdb->postmeta AS meta".
			" INNER JOIN $wpdb->posts AS posts ON meta.post_id = posts.ID AND posts.post_status = 'publish'".
			" WHERE meta_key = '_edd_sl_status' GROUP BY meta_value";

		$data = $wpdb->get_results( $query );

		//get the number of disabled licenses, because their status meta key is not set as "disabled"
		//but the actual post is not with a publish status
		$disabled_count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->posts WHERE post_status != 'publish'".
			" AND post_type = %s", USIN_EDD::LICENSE_POST_TYPE));

		$data[]=(object)array($this->label_col => "disabled", $this->total_col=>$disabled_count);

		return $data;
	}



	protected function uses_licenses_db_table(){
		global $wpdb;

		$table_name = $wpdb->prefix.'edd_licenses';

		$licenses_table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'") === $table_name;
		if(!$licenses_table_exists){
			return false;
		}

		if(function_exists('edd_has_upgrade_completed') && edd_has_upgrade_completed('migrate_licenses')){
			return true;
		}

		$count = (int)$wpdb->get_var( "SELECT COUNT(*) FROM $table_name");
		if($count > 0){
			return true;
		}

		return false;
	}

}