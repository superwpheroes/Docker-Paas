<?php

class USIN_Numeric_Meta_Field_Loader extends USIN_Numeric_Field_Loader{

	protected function get_default_data(){
		return $this->load_meta_data($this->report->get_field_id());
	}

	protected function get_data_in_ranges($chunk_size){
		global $wpdb;

		$select = $this->get_select('meta_value', $chunk_size);
		$group_by = $this->get_group_by('meta_value', $chunk_size);

		$query = $wpdb->prepare("$select FROM $wpdb->usermeta WHERE meta_key = %s $group_by",
			$this->report->get_field_id());

		return $wpdb->get_results( $query );
	}
}