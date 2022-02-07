<?php

class USIN_Memberpress_Ended_Memberships_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$memberships_query = USIN_MemberPress_Query::get_memberships_query();
		$column = 'expires_at_local';
		$query = $wpdb->prepare("SELECT COUNT(id) AS $this->total_col, $column AS $this->label_col".
			" FROM ($memberships_query) m".
			" WHERE $column >= %s AND $column <= %s AND is_active = 0".
			" GROUP BY ".$this->get_period_group_by($column),
			$this->get_period_start(), $this->get_period_end());

		return $wpdb->get_results( $query );
	}
}