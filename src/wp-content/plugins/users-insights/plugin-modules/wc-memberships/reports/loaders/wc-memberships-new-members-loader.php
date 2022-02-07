<?php

class USIN_Wc_Memberships_New_Members_Loader extends USIN_Period_Report_Loader {
	
	protected function load_data(){
		global $wpdb;

		$statuses = USIN_WC_Memberships_Query::get_status_string();

		$subquery = $wpdb->prepare("SELECT MIN(CAST(meta_value AS DATETIME)) AS member_since, p.post_author AS user_id".
			" FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON pm.post_id = p.ID".
			" WHERE pm.meta_key = '_start_date' AND p.post_type = %s AND p.post_status IN (".$statuses.")".
			" GROUP BY user_id HAVING member_since >= %s AND member_since <= %s", 
			USIN_WC_Memberships::POST_TYPE, $this->get_period_start(), $this->get_period_end());

		$group_by = $this->get_period_group_by('member_since');
		$query ="SELECT COUNT(*) as $this->total_col, member_since as $this->label_col".
			" FROM ($subquery) AS member_since_dates GROUP BY $group_by";
			

		return $wpdb->get_results( $query );
	}
}