<?php

/**
 * Includes the default modules options.
 */
class USIN_Module_Defaults{

	protected static $modules;

	/**
	 * Returns the default modules options. New modules can be added via the
	 * usin_module_options filter.
	 * @return array the default modules options
	 */
	public static function get(){

		$def_options = array(
			
			array(
				'id' => 'globallicense',
				'name' => __('Users Insights License', 'usin'),
				'desc' => __('Add your license key in order to have automatic updates of the plugin and use the Geolocation functionality.', 'usin'),
				'allow_deactivate' => false,
				'active_by_default' => true,
				'requires_license' => true
			),

			array(
				'id' => 'activity',
				'name' => __('User Activity', 'usin'),
				'desc' => __('Detects last login date and number of sessions. ', 'usin'),
				'allow_deactivate' => true,
				'buttons' => array(
					array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/wordpress-users-session-detection/', 'target'=>'_blank')
				),
				'active_by_default' => false
			),

			array(
				'id' => 'devices',
				'name' => __('Device Detection', 'usin'),
				'desc' => __('Detects the browser
					and platform details of your logged in users. ', 'usin'),
				'allow_deactivate' => true,
				'buttons' => array(
					array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/wordpress-users-device-detection/', 'target'=>'_blank')
				),
				'active_by_default' => false
			),

			 //Geolocation options
			 
			array(
				'id' => 'geolocation',
				'name' => __('Geolocation', 'usin'),
				'desc' => 'Detects the location (country, region and city) of your logged in users.
				Once the location has been detected, it can be viewed on an interractive map.',
				'allow_deactivate' => true,
				'requires_license' => true,
				'uses_module_license'=>'globallicense',
				'buttons' => array(
					array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/wordpress-users-geolocation/', 'target'=>'_blank')
				)
			),

		);

		$modules = apply_filters('usin_module_options', $def_options);

		return $modules;
	}

	public static function get_cached(){
		if(empty(self::$modules)){
			self::$modules = self::get();
		}
		return self::$modules;
	}

	public static function clear_cache(){
		self::$modules = null;
	}

	public static function get_by_id($id){
		
		$modules = self::get_cached();
		//there is no module with this ID in the cached module list, reload the module
		//options as some modules could have been registered later
		if(!in_array($id, wp_list_pluck($modules, 'id'))){
			$modules = self::get();
		}
		
		foreach($modules as $module){
			if($module['id'] == $id){
				return $module;
			}
		}

	}

}