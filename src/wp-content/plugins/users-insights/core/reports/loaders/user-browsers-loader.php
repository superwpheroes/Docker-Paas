<?php

class USIN_User_Browsers_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		return $this->load_users_insights_data('browser');
	}


	
}