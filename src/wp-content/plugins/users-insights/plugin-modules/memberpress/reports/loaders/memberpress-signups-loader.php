<?php

class USIN_Memberpress_Signups_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$memberships_query = USIN_MemberPress_Query::get_memberships_query();
		$column = 'created_at_local';
		$query = $wpdb->prepare("SELECT COUNT(id) AS $this->total_col, $column AS $this->label_col".
			" FROM ($memberships_query) m".
			" WHERE $column >= %s AND $column <= %s".
			" GROUP BY ".$this->get_period_group_by($column),
			$this->get_period_start(), $this->get_period_end());

		return $wpdb->get_results( $query );
	}
}