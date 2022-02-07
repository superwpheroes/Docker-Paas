<?php

if(!defined( 'ABSPATH' )){
	exit;
}

class USIN_Plugin_Module_Initializer{

	protected static $include_path;
	protected static $modules = array();
	protected static $initialized = false;
	protected static $module_folders = array(
		'woocommerce',
		'wc-subscriptions',
		'wc-memberships',
		'bbpress',
		'buddypress',
		'edd',
		'ultimate-member',
		'gravity-forms',
		'learndash',
		'pmpro',
		'pbpro',
		'events-calendar',
		'memberpress'
	);

	public static function init(){
		if(!is_admin() || self::$initialized){
			return;
		}

		self::setup_modules();
		self::$include_path = USIN_PLUGIN_PATH . 'plugin-modules/';

		spl_autoload_register( array('USIN_Plugin_Module_Initializer', 'autoload_class'));

		//include the modules manually as they are self-initialized
		foreach (self::$modules as $key => $module ) {
			include_once("$module/$module.php");
		}

		self::$initialized = true;

	}

	/**
	 * Setup the modules as an array of module key pointing to module folder.
	 * The module key is the same as the folder, but using underscores.
	 *
	 * @return void
	 */
	protected static function setup_modules(){
		foreach (self::$module_folders as $module_folder ) {
			$key = str_replace( '-', '_', $module_folder );
			self::$modules[$key] = $module_folder;
		}
	}

	/**
	 * Autoload a plugin module class based on its name.
	 *
	 * @param string $class_name the class name
	 * @return void
	 */
	public static function autoload_class($class_name){
		$folder_path = self::get_folder_path_by_class_name($class_name);
		if(!$folder_path){
			return;
		}
		
		$file_name = self::get_file_name_by_class_name($class_name);
		$file_path = $folder_path.'/'.$file_name;

		if(is_readable($file_path)){
			include_once $file_path;
		}

	}

	/**
	 * Get the corresponding file name based on a class name.
	 * 
	 * Examples:
	 * USIN_Woocommerce_Query => woocommerce-query.php
	 * USIN_EDD_User_Activity => edd-user-activity.php
	 *
	 * @param string $class_name the name of the class
	 * @return string the name of the file based on the class name
	 */
	protected static function get_file_name_by_class_name($class_name){
		$name = strtolower(str_replace( 'USIN_', '', $class_name ));
		$name = str_replace( '_', '-', $name );
		return $name.'.php';
	}

	/**
	 * Find the autoload path of a module file, by following these rules:
	 * 1. The name of the class must start with USIN
	 * 2. The name of the class must be followed by the module name by using
	 * underscores
	 * 3. If it is a report class the name should contain the word "report" and the
	 * class should be located within the reports/ folder of the module folder
	 * 4. If it is a report loader class, the name of the class should contain
	 * the word "loader" and the class should be located in the /reports/loaders
	 * folder.
	 * 
	 * Examples:
	 * USIN_Woocommerce_Query => woocommerce/ folder
	 * USIN_BuddyPress_User_Activity => buddypress/ folder
	 * USIN_Woocommerce_Reports => woocommerce/reports folder
	 * USIN_Woocommerce_Sales_Loader => woocommerce/reports/loaders folder
	 * 
	 * @param string $class_name the name of the class
	 * @return string the path of the file if it is a module file and null
	 * if it is not.
	 */
	protected static function get_folder_path_by_class_name($class_name){
		if(strpos($class_name, 'USIN') !== 0){
			return;
		}

		$class_name = strtolower($class_name);

		foreach (self::$modules as $key => $module ) {
			if(strpos($class_name, "usin_$key") === 0){
				$path = self::$include_path.$module;

				if(strpos($class_name, 'report') !== false){
					$path .= '/reports';
				}elseif(strpos($class_name, 'loader') !== false){
					$path .= '/reports/loaders';
				}

				return $path;
			}
		}
	}

}