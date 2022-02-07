<?php

class USIN_Woocommerce_Billing_States_Loader extends USIN_Standard_Report_Loader {

	
	public function load_data(){
	
		return $this->load_post_meta_data('_billing_state', true);

	}


}