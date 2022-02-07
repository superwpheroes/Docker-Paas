<?php

class USIN_Buddypress_Active_Users_Loader extends USIN_Period_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by($this->label_col);
		$prefix = USIN_BuddyPress_Query::get_prefix();

		$query = $wpdb->prepare("SELECT COUNT(DISTINCT(user_id)) AS $this->total_col, date_recorded AS $this->label_col".
			" FROM ".$prefix."bp_activity WHERE date_recorded >= %s AND date_recorded <= %s AND `type` != 'last_activity'".
			" GROUP BY $group_by",
			$this->get_period_start(), $this->get_period_end());

		return $wpdb->get_results( $query );
	}

}