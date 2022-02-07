<?php

class USIN_Includes{

	public static $paths = array();

	protected static $autoload_files = array(
		'core/' => array(
			'schema', 'user-data', 'geolocation-status', 'user-detect', 'ajax', 'helper', 
			'actions', 'field', 'capabilities', 'notice', 'assets',
		 	'filters', 'field-defaults', 'options', 'user', 'user-activity', 'user-exported', 'segments', 'html'
		),
		'core/events/' => array(
			'event', 'event-query'
		),
		'core/events/visit-tracking/' => array(
			'visit-tracking', 'visit-tracking-data', 'visit-tracker', 'visit-tracking-user-activity'
		),
		'core/modules/' => array(
			'license', 'module', 'remote-license', 'module-defaults', 'modules', 
			'module-page', 'module-assets', 'module-ajax'
		),
		'core/reports/' => array(
			'report','report-options','period-report','standard-report',
			'reports-defaults','reports-page','reports-assets','reports-ajax','report-periods'
		),
		'core/reports/loaders/' => array(
			'report-loader', 'period-report-loader', 'standard-report-loader', 
			'numeric-field-loader', 'meta-field-loader', 'multioption-field-loader', 'numeric-meta-field-loader',
			'registered-users-loader', 'user-browsers-loader', 'user-cities-loader', 'user-countries-loader',
			'user-groups-loader', 'user-platforms-loader', 'user-regions-loader'
		),
		'core/crm/custom-fields/' => array(
			'custom-fields-page', 'custom-fields-assets', 'custom-fields-options', 'custom-fields-ajax', 'custom-fields'
		),
		'core/settings/' => array(
			'settings-manager', 'settings-field', 'checkboxes-field'
		),
		'core/privacy/' => array(
			'privacy', 'privacy-exporter', 'privacy-eraser'
		),
		'core/user-list/' => array('list-export', 'list-assets', 'list-ajax', 'list-page'),
		'core/query/' => array('query', 'user-query', 'coordinates-query', 'meta-query', 'post-query'),
		'core/lib/' => array('browser'),
		'core/crm/' => array('groups'),
		'core/crm/notes/' => array('notes', 'note'),
		'core/updates/' => array('plugin-updater'),
		'core/utils/' => array('debug'),
		'plugin-modules/' => array(
			'plugin-module', 'module-reports', 'option-search', 'post-option-search',
			'plugin-module-initializer'	
		)
	);


	/**
	 * Builds an array of the paths of each classes. The key is the class name
	 * while the value is the relative path to the file of the class.
	 * E.g. array('USIN_Schema' => 'core/schema.php')
	 *
	 * @param array $files array where in each element the key is the path of the folder
	 * and the value is an array containing the names of the files without .php extension.
	 * e.g. array('core/' => array('schema', 'ajax'))
	 * @return array
	 */
	public static function build_paths($files){
		$paths = array();
		foreach ($files as $path => $names ) {
			foreach ($names	as $name) {
				$class_name = self::build_class_name($name);
				$paths[$class_name] = USIN_PLUGIN_PATH.$path.$name.'.php';
			}
		}

		return $paths;
	}

	/**
	 * Builds a class name based on a file name (without the extension).
	 * E.g. user-data will become USIN_User_Data
	 *
	 * @param string $name the file name without the .php extension
	 * @return string the class name
	 */
	public static function build_class_name($name){
		$parts = explode('-', $name);
		$parts = array_map('ucfirst', $parts);
		return 'USIN_'.implode('_', $parts);
	}

	public static function autoload_class($class_name){
		if(isset(self::$paths[$class_name])){
			include_once(self::$paths[$class_name]);
		}
		
	}
	
	public static function call(){

		self::$paths = self::build_paths(self::$autoload_files);

		spl_autoload_register( array('USIN_Includes', 'autoload_class'));
		
		include_once('core/functions.php');

		do_action('usin_files_loaded');
	}
}