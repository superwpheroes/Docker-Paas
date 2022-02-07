<?php

class USIN_Woocommerce_Billing_Countries_Loader extends USIN_Standard_Report_Loader {

	protected $countries = null;
	
	public function load_data(){
	
		$data = $this->load_post_meta_data('_billing_country', true);

		foreach($data as &$row){
			$row->label = USIN_Woocommerce::get_wc_country_name_by_code($row->label);
		}

		return $data;
	}

}