<?php

class USIN_BuddyPress_Numeric_Loader extends USIN_Numeric_Field_Loader{

	public function get_default_data(){
		return USIN_BuddyPress_Query::get_field_counts($this->report->get_field_id(), $this->total_col, $this->label_col);
	}

	public function get_data_in_ranges($chunk_size){
		global $wpdb;

		$select = $this->get_select('`value`', $chunk_size);
		$group_by = $this->get_group_by('`value`', $chunk_size);
		$xprofile_table = USIN_BuddyPress_Query::get_xprofile_table_name();

		$query = $wpdb->prepare("$select FROM $xprofile_table WHERE field_id = %d $group_by",
			$this->report->get_field_id());

		return $wpdb->get_results( $query );

	}

}