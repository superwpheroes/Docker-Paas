<?php

class USIN_User_Regions_Loader extends USIN_Standard_Report_Loader {

	protected function load_data(){
		return $this->load_users_insights_data('region', true);
	}


	
}