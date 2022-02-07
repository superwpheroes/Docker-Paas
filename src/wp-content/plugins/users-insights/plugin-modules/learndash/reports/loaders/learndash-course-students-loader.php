<?php

class USIN_Learndash_Course_Students_Loader extends USIN_Standard_Report_Loader {


	protected function load_data(){

		$data = $this->load_db_data();

		$courses = USIN_LearnDash::get_items(USIN_LearnDash::COURSE_POST_TYPE, true);

		return $this->match_ids_to_names($data, $courses);

	}

	protected function load_db_data(){
		global $wpdb;
		
		$filter = $this->getSelectedFilter();
		$condition = "WHERE activity_type = 'course'";

		if($filter == 'completed'){
			$condition .= ' AND activity_status = 1';
		}elseif($filter == 'in_progress'){
			$condition .= ' AND activity_status = 0';
		}

		$query = "SELECT COUNT(*) as $this->total_col, post_id as $this->label_col FROM ".$wpdb->prefix."learndash_user_activity".
			" $condition GROUP BY post_id LIMIT $this->max_items";

		return $wpdb->get_results( $query );
	}

}