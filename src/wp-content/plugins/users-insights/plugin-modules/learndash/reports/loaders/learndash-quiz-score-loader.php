<?php

/**
 * We are only extending the USIN_Numeric_Field_Loader class so we can use the
 * get_select and get_group_by methods.
 */
class USIN_Learndash_Quiz_Score_Loader extends USIN_Numeric_Field_Loader {

	protected $chunk_size = 20;

	public function load_data(){
		$data = $this->load_db_data();
		$data = $this->format_ranges($data);
		return $data;
	}


	protected function load_db_data(){
		global $wpdb;

		$select = $this->get_select('activity_meta_value', $this->chunk_size);

		$filter = $this->getSelectedFilter();
		$condition = " WHERE ua.activity_type = 'quiz' and activity_meta_key = 'percentage'";

		if($filter != 'all'){
			$condition .= $wpdb->prepare(" AND ua.post_id = %d", intval($filter));
		}
		
		$group_by = $this->get_group_by('activity_meta_value', $this->chunk_size);

		$query = "$select FROM ".$wpdb->prefix."learndash_user_activity_meta uam".
		" INNER JOIN ".$wpdb->prefix."learndash_user_activity ua ON uam.activity_id = ua.activity_id".
		"$condition $group_by";

		return $wpdb->get_results( $query );
	}


	protected function format_ranges($data){
		foreach ($data as &$item ) {
			if(intval($item->range_start) == 100){
				$item->label = '100%';
			}else{
				$item->label = sprintf("%s%% - %s%%", $item->range_start, $item->range_end);
			}
		}
		return $data;
	}
	

	//include the abstract method definitions
	protected function get_default_data(){}
	protected function get_data_in_ranges($chunk_size){}

}