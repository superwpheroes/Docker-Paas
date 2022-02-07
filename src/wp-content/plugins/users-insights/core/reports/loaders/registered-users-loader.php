<?php

class USIN_Registered_Users_Loader extends USIN_Period_Report_Loader {


	protected function load_data(){
		global $wpdb;

		$group_by = $this->get_period_group_by('user_registered');

		$query ="SELECT COUNT(*) AS $this->total_col, user_registered AS $this->label_col".
			" FROM $wpdb->users u";

		if(is_multisite()){
			//load only the users for the current site
			$blog_id = $GLOBALS['blog_id'];
			if($blog_id){
				$key = $wpdb->get_blog_prefix( $blog_id ) . 'capabilities';
				$query .= $wpdb->prepare(" INNER JOIN $wpdb->usermeta m ON".
					" u.ID = m.user_id AND m.meta_key = %s", $key);
			}
		}
			
		$query .=  $wpdb->prepare(" WHERE user_registered >= %s AND user_registered <= %s GROUP BY $group_by",
			$this->get_period_start(), $this->get_period_end());

		return $wpdb->get_results( $query );
	}

}