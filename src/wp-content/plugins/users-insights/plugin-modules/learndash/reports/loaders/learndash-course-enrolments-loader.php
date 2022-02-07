<?php

class USIN_Learndash_Course_Enrolments_Loader extends USIN_Period_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by($this->label_col);
		$start = mysql2date('U', $this->get_period_start());
		$end =  mysql2date('U', $this->get_period_end());

		$query = $wpdb->prepare("SELECT COUNT(*) as $this->total_col, FROM_UNIXTIME(activity_started) AS $this->label_col".
			" FROM ".$wpdb->prefix."learndash_user_activity WHERE activity_type = 'course'".
			" AND activity_started >= %d AND activity_started <= %d GROUP BY $group_by",
			$start, $end);

		return $wpdb->get_results( $query );
	}

}