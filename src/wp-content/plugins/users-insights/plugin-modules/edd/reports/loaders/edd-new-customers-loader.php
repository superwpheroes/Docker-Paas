<?php

class USIN_Edd_New_Customers_Loader extends USIN_Period_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by($this->label_col);

		$query = $wpdb->prepare("SELECT COUNT(*) as $this->total_col, date_created as $this->label_col FROM ".$wpdb->prefix."edd_customers".
			" WHERE date_created >= %s AND date_created <= %s".
			" GROUP BY $group_by", $this->get_period_start(), $this->get_period_end());

		return $wpdb->get_results( $query );
	}

}