<?php

class USIN_Wc_Memberships_Ended_Memberships_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$subquery = $wpdb->prepare("SELECT p.ID, MIN(CAST(pm.meta_value AS DATETIME)) AS end_date".
			" FROM $wpdb->posts AS p".
			" INNER JOIN $wpdb->postmeta AS pm ON p.ID = pm.post_id".
			" WHERE p.post_type = %s AND p.post_status IN ('wcm-expired','wcm-cancelled')".
			" AND pm.meta_key IN ('_end_date', '_cancelled_date') AND pm.meta_value != ''".
			" GROUP BY p.ID HAVING end_date >= %s AND end_date <= %s",
			USIN_WC_Memberships::POST_TYPE, $this->get_period_start(), $this->get_period_end());


		$group_by = $this->get_period_group_by('end_date');
		$query ="SELECT COUNT(*) as $this->total_col, end_date as $this->label_col".
			" FROM ($subquery) AS end_dates GROUP BY $group_by";
			

		return $wpdb->get_results( $query );
	}
}