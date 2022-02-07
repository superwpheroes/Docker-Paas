<?php

class USIN_Woocommerce_Payment_Methods_Loader extends USIN_Standard_Report_Loader {

	
	public function load_data(){
	
		return $this->load_post_meta_data('_payment_method_title', true);

	}


}