<?php

class USIN_User_Groups_Loader extends USIN_Standard_Report_Loader {

	public function load_data(){
		
		$groups = get_terms(USIN_Groups::$slug);
		$data = array();

		foreach($groups as $group){
			$data[]=$this->data_item($group->name, $group->count);
		}

		return $data;

	}


	
}