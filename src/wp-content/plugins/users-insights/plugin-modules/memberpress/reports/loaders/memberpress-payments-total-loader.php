<?php

class USIN_Memberpress_Payments_Total_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$column = USIN_MemberPress_Query::get_gmt_offset_date_select('created_at');
		$group_by = $this->get_period_group_by($this->label_col);

		$query = $wpdb->prepare("SELECT SUM(total) AS $this->total_col, $column AS $this->label_col".
			" FROM ".$wpdb->prefix."mepr_transactions".
			" WHERE $column >= %s AND $column <= %s AND txn_type='payment' AND `status`='complete' AND total > 0".
			" GROUP BY $group_by",
			$this->get_period_start(), $this->get_period_end());

		return $wpdb->get_results( $query );
	}
}