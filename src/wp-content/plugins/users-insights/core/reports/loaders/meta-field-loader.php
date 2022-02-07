<?php

class USIN_Meta_Field_Loader extends USIN_Standard_Report_Loader{

	protected function load_data(){
		$field_id = $this->report->get_field_id();
		
		return $this->load_meta_data($field_id);
	}
	
}