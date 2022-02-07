<?php

class USIN_Pmpro_Billing_States_Loader extends USIN_Standard_Report_Loader {

	public function load_data(){
		return $this->load_meta_data('pmpro_bstate', true);
	}

}