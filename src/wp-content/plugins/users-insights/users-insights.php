<?php
/**
 * Plugin Name: Users Insights
 * Plugin URI: https://usersinsights.com/
 * Description: Everything about your WordPress users in one place
 * Version: 3.8.2
 * Author: Pexeto
 * Text Domain: usin
 * Domain Path: /lang
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright: Pexeto 2020
 *
 * Users Insights is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 */

if(!defined( 'ABSPATH' )){
	exit;
}

global $usin;
$usin = new StdClass();

if(! class_exists('USIN_Manager')){

	/**
	 * Includes all of the initalization functionality of the Users Insights plugin.
	 */
	class USIN_Manager{

		public $title;
		public $slug = 'users_insights';
		public $user_data_db_table = 'usin_user_data';
		public $events_db_table = 'usin_events';

		protected static $instance;

		protected function __construct(){}

		/**
		 * Returns the instance of the class, it is a singleton class.
		 */
		public static function get_instance(){
			if(! self::$instance ){
				self::$instance = new USIN_Manager();
				self::$instance->init();
			}
			return self::$instance;
		}

		/**
		 * Initializes the main plugin functionality.
		 */
		public function init(){
			$this->config();
			$this->include_files();

			$this->modules = USIN_Modules::get_instance();

			USIN_Visit_Tracking::init();

			if(is_admin()){
				USIN_Plugin_Module_Initializer::init();
				
				new USIN_Capabilities();

				$this->options = new USIN_Options();
				
				$this->list_page = new USIN_List_Page($this->title, $this->slug, $this->options);
				$this->list_page->init();

				$this->reports_page = new USIN_Reports_Page($this->slug);
				$this->reports_page->init();
				
				$this->module_page = new USIN_Module_Page($this->slug, $this->modules);
				$this->module_page->init();
				
				new USIN_Custom_Fields();
				$this->cf_page = new USIN_Custom_Fields_Page($this->slug);
				$this->cf_page->init();

				USIN_Filters::init();
				
				$notes = new USIN_Notes();
				$notes->init();

				USIN_Notice::register_ajax_handlers();

				//updater
				$updates_license = $this->modules->get_license('globallicense');
				$updater = new USIN_Plugin_Updater('https://usersinsights.com', __FILE__, array(
					'version' => USIN_VERSION,
					'license' => $updates_license,
				));

				USIN_Assets::load_global_inline_css();

				new USIN_Debug();

			}

			USIN_Actions::init();
			USIN_Groups::init($this->slug);
			USIN_Privacy::init();

			$user_detect = new USIN_User_Detect();
			$user_detect->init();

			$schema = new USIN_Schema($this->user_data_db_table, $this->events_db_table, USIN_PLUGIN_FILE, USIN_VERSION);
			$schema->init();
			
			do_action('usin_loaded');

			//load the text domain
			add_action( 'plugins_loaded', array($this, 'load_textdomain') );
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_page_link') );
		}

		/**
		 * Sets the main configuration options.
		 */
		public function config(){

			$this->title = __('Users Insights', 'usin');

			//set constants
			if ( ! defined( 'USIN_VERSION' ) ) {
				define( 'USIN_VERSION', '3.8.2' );
			}

			if ( ! defined( 'USIN_PLUGIN_FILE' ) ) {
				define( 'USIN_PLUGIN_FILE', __FILE__);
			}

			if ( ! defined( 'USIN_PLUGIN_PATH' ) ) {
				define( 'USIN_PLUGIN_PATH', plugin_dir_path(__FILE__));
			}

		}

		/**
		 * Load the text domain for translations.
		 */
		public function load_textdomain(){
			load_plugin_textdomain( 'usin', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}
		
		/**
		 * Adds a "Settings" link to the Module Options page in the plugin listing
		 */
		public function add_settings_page_link($links){
			$links[]= sprintf('<a href="%s">%s</a>',
				admin_url( 'admin.php?page='.$this->module_page->slug ), __('Settings', 'usin'));
			return $links;
		}

		/**
		 * Include the required core files.
		 */
		public function include_files(){

			include_once('includes.php');
			USIN_Includes::call();

		}
	}

}


$usin->manager = USIN_Manager::get_instance();

