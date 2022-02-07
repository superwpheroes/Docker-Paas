<?php

class USIN_Standard_Report_Loader extends USIN_Report_Loader{
	
	protected $options;
	protected $format_data = true;


	protected function format_data($data){
		if(!$this->format_data){
			return $data;
		}

		usort($data, array('USIN_Standard_Report_Loader', 'compare'));

		if(sizeof($data) <= $this->max_items){
			return $data;
		}

		$formatted = array_slice($data, 0, $this->max_items);
		$other_data = array_slice($data, $this->max_items);
		$other_sum = array_sum(wp_list_pluck($other_data, $this->total_col));
		$formatted[]= (object)array('label' => __('Other', 'usin'), $this->total_col => $other_sum);

		return $formatted;
	}

	protected function load_meta_data($meta_key, $limit = false){
		global $wpdb;
		$query = $wpdb->prepare( "SELECT COUNT(*) AS $this->total_col, meta_value AS $this->label_col FROM $wpdb->usermeta".
			" WHERE meta_key = %s AND meta_value != '' GROUP BY meta_value ORDER BY $this->total_col DESC", 
			$meta_key);

		if($limit){
			$query.= " LIMIT $this->max_items";
		}
		
		return $wpdb->get_results( $query );
	}

	protected function load_post_meta_data($meta_key, $limit = false){
		global $wpdb;
		$query = $wpdb->prepare( "SELECT COUNT(*) as $this->total_col, meta_value as $this->label_col FROM $wpdb->postmeta".
			" WHERE meta_key = %s AND meta_value != '' GROUP BY meta_value ORDER BY $this->total_col DESC", 
			$meta_key);

		if($limit){
			$query.= " LIMIT $this->max_items";
		}
		
		return $wpdb->get_results( $query );
	}

	protected function load_users_insights_data($field, $limit = false){
		global $wpdb;
		$query = "SELECT COUNT(*) AS $this->total_col, $field AS $this->label_col FROM ".$wpdb->prefix."usin_user_data".
		" WHERE $field != 'unknown' AND $field IS NOT NULL".
		" GROUP BY $field ORDER BY $this->total_col DESC";

		if($limit){
			$query.= " LIMIT $this->max_items";
		}

		return $wpdb->get_results( $query );
	}

	protected function match_ids_to_names($data, $names, $delete_nonexistent = false){
		
		foreach ($data as $key => &$item ) {
			$id = $item->label;

			if(isset($names[$id])){
				$item->label = $names[$id];
			}elseif($delete_nonexistent === true){
				unset($data[$key]);
			}
		}

		if($delete_nonexistent === true){
			$data = array_values($data);
		}

		return $data;

	}

	public static function compare($a, $b){
		return (float)$a->total < (float)$b->total;
	}


	protected function load_data(){
		return array();
	}
}

