<?php

class USIN_Learndash_Active_Students_Loader extends USIN_Period_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by($this->label_col);
		$start = mysql2date('U', $this->get_period_start());
		$end =  mysql2date('U', $this->get_period_end());

		$query = $wpdb->prepare("SELECT COUNT(DISTINCT(user_id)) AS $this->total_col, FROM_UNIXTIME(activity_updated) AS $this->label_col".
			" FROM ".$wpdb->prefix."learndash_user_activity WHERE activity_updated >= %d AND activity_updated <= %d AND activity_type != 'access' GROUP BY $group_by",
			$start, $end);

		return $wpdb->get_results( $query );
	}

}