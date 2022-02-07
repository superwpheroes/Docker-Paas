<?php

class USIN_Learndash_Quiz_Attempts_Loader extends USIN_Standard_Report_Loader {


	protected function load_data(){

		$data = $this->load_db_data();

		$data = $this->format_labels($data);

		return $data;

	}

	protected function load_db_data(){
		global $wpdb;
		
		$filter = $this->getSelectedFilter();
		$condition = "WHERE activity_type = 'quiz'";

		if($filter != 'all'){
			$condition .= $wpdb->prepare(" AND post_id = %d", intval($filter));
		}

		$subquery = "SELECT COUNT(*) AS attempts FROM ".$wpdb->prefix."learndash_user_activity".
			" $condition GROUP BY user_id, post_id";


		$query = "SELECT COUNT(*) as $this->total_col, attempts as $this->label_col".
			" FROM ($subquery) AS ld_attempts GROUP BY attempts";

		return $wpdb->get_results( $query );
	}

	protected function format_labels($data){
		foreach ($data as &$item) {
			$item->label = $item->label.' '._n( 'attempt', 'attempts', intval($item->label), 'usin' );
		}
		return $data;
	}

}