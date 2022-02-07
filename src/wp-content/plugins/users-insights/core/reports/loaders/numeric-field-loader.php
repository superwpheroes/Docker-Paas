<?php

abstract class USIN_Numeric_Field_Loader extends USIN_Standard_Report_Loader{

	abstract protected function get_default_data();
	abstract protected function get_data_in_ranges($chunk_size);

	protected $format_data = false;
	
	public function load_data(){
		$field_id = $this->report->get_field_id();

		$data = $this->get_default_data();
		return $this->unify_into_ranges($data);
	}

	protected function unify_into_ranges($data){

		usort($data, array($this, 'compare_res'));

		if(sizeof($data) <= $this->max_items){
			return $data;
		}

		$chunk_size = $this->get_chunk_size($data);
		$ranged_data = $this->get_data_in_ranges($chunk_size);

		foreach ($ranged_data as $i => &$item) {
			$item->label = sprintf("%s - %s", $item->range_start, $item->range_end);
		}

		return $ranged_data;

	}

	protected function get_chunk_size($data){
		
		$min_val = intval($data[0]->label);
		$max_val = intval($data[sizeof($data)-1]->label);

		$chunk_size = ceil(($max_val - $min_val)/$this->max_items);
		$len = strlen("$chunk_size") - 1;
		return round($chunk_size, -$len);
	}


	public static function compare_res($a, $b){
		return intval($a->label) > intval($b->label);
	}

	protected function get_select($column, $chunk_size){
		global $wpdb;
		
		return $wpdb->prepare("SELECT %d * ($column div %d) AS range_start,  %d * ($column div %d) + %d AS range_end, COUNT(*) AS %s",
			$chunk_size, $chunk_size, $chunk_size, $chunk_size, $chunk_size-1, $this->total_col);
	}

	protected function get_group_by($column, $chunk_size){
		global $wpdb;
		
		return $wpdb->prepare(" GROUP BY $column div %d ORDER BY range_start ASC", $chunk_size);
	}

}