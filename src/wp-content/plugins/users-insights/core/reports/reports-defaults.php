<?php


class USIN_Reports_Defaults{

	public static function get($format_for_js = false){
		$defaults = array(
			new USIN_Period_Report('registered_users', __('Registered users', 'usin')),
			new USIN_Standard_Report('user_groups', __('User groups', 'usin'))
		);

		if(usin_modules()->is_module_active('geolocation')){
			$defaults[]= new USIN_Standard_Report('user_countries', __('Top user countries', 'usin'), 
				array('type'=>USIN_Report::BAR));
			
			$defaults[]= new USIN_Standard_Report('user_regions', __('Top user regions', 'usin'), 
				array('type'=>USIN_Report::BAR, 'visible' => false));

				$defaults[]= new USIN_Standard_Report('user_cities', __('Top user cities', 'usin'), 
				array('type'=>USIN_Report::BAR));
		}

		if(usin_modules()->is_module_active('devices')){
			$defaults[]= new USIN_Standard_Report('user_browsers', __('User browsers', 'usin'), 
				array('info' => __('Detected browsers displayed only', 'usin')));

			$defaults[]= new USIN_Standard_Report('user_platforms', __('User platforms', 'usin'), 
				array('info' => __('Detected platforms displayed only', 'usin')));
		}

		$defaults = apply_filters('usin_report_defaults', $defaults);

		if($format_for_js){
			$defaults = array_map( array('USIN_Reports_Defaults', 'format_for_js'), $defaults);
		}

		return $defaults;
	}


	public static function format_for_js($report){
		return $report->to_array();
	}

	public static function get_by_id($id){
		$reports = self::get();

		foreach($reports as $report){
			if($report->id == $id){
				return $report;
			}
		}
	}

}