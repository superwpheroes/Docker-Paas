<?php

class USIN_BuddyPress_Multioption_Loader extends USIN_Multioption_Field_Loader{

	public function get_data(){
		return USIN_BuddyPress_Query::get_field_counts($this->report->get_field_id(), 
			$this->total_col, $this->label_col);
	}

}