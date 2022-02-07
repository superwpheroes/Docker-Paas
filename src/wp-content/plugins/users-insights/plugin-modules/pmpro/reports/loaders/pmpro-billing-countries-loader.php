<?php

class USIN_Pmpro_Billing_Countries_Loader extends USIN_Standard_Report_Loader {

	protected $countries = null;
	
	public function load_data(){

		$data = $this->load_meta_data('pmpro_bcountry', true);
		return $this->set_country_names($data);
	}

	protected function set_country_names($data){
		$countries = USIN_Pmpro::get_countries(true);
		
		if(empty($countries)){
			return $data;
		}

		foreach ($data as &$row ) {
			if(isset($countries[$row->label])){
				$row->label = $countries[$row->label];
			}
		}
		return $data;
	}
}