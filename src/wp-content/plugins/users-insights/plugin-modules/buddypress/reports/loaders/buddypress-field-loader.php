<?php

class USIN_BuddyPress_Field_Loader extends USIN_Standard_Report_Loader{

	public function load_data(){
		return USIN_BuddyPress_Query::get_field_counts($this->report->get_field_id(), $this->total_col, $this->label_col);
	}

}