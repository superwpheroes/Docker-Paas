<?php

class USIN_Report_Options{

	const VISIBILITY_KEY = '_usin_report_visibility';

	public static function update_report_visibility($report_id, $visibility){
		$visibility_options = self::get_visibility_options();

		$visibility_options[$report_id] = $visibility;
		self::update_visibility_options($visibility_options);
	}

	public static function get_visibility_option($report_id){
		$visibility_options = self::get_visibility_options();
		if(isset($visibility_options[$report_id])){
			return $visibility_options[$report_id];
		}
		return null;
	}

	protected static function get_visibility_options(){
		$user_id = get_current_user_id();
		$res = get_user_meta($user_id, self::VISIBILITY_KEY, true);
		if(is_array($res)){
			return $res;
		}
		return array();
	}

	protected static function update_visibility_options($report_ids){
		$user_id = get_current_user_id();
		return update_user_meta($user_id, self::VISIBILITY_KEY, $report_ids);
	}
	
}