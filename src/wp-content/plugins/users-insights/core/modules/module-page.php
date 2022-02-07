<?php

/**
 * Includes the main initialization functionality for the Module Options page.
 */
class USIN_Module_Page{
	
	protected $capability;
	protected $nonce_key = 'usin_module_options';
	protected $assets;
	protected $ajax;
	protected $parent_slug;
	protected $modules;

	public $slug = 'usin_modules';
	public $title;
	public $ajax_nonce;

	/**
	 * @param string $parent_slug    the slug of the parent menu item
	 * @param string $capability     the user capability required to access this page
	 * @param USIN_Modules $modules the Module Options object
	 */
	public function __construct($parent_slug, $modules){
		$this->title = __('Module Options', 'usin');
		$this->parent_slug = $parent_slug;
		$this->capability = USIN_Capabilities::MANAGE_OPTIONS;
		$this->modules = $modules;
	}

	/**
	 * Main initialization functionality, registers the required action hooks.
	 */
	public function init(){
		add_action ( 'admin_menu', array($this, 'add_menu_page'), 20 );
		add_action ( 'admin_init', array($this, 'create_nonce') );
		add_action ( 'current_screen', array($this, 'setup_notice') );

		$this->assets = new USIN_Module_Assets($this);
		$this->assets->init();

		$this->ajax = new USIN_Module_Ajax($this->modules, $this->capability, $this->nonce_key);
		$this->ajax->add_actions();
	}

	public function is_current_page(){
		global $current_screen;

		return strpos( $current_screen->base, $this->slug ) !== false;
	}

	/**
	 * Adds the page as a menu item.
	 */
	public function add_menu_page(){

		add_submenu_page( $this->parent_slug, $this->title, $this->title, 
			$this->capability, $this->slug, array($this, 'print_page_markup') );
	}

	/**
	 * Creates a nonce for the AJAX requests on this page.
	 */
	public function create_nonce(){
		$this->ajax_nonce = wp_create_nonce($this->nonce_key);
	}

	public function setup_notice(){
		if($this->is_current_page() && usin_modules()->is_module_active('devices') && !usin_modules()->is_module_active('activity')){
			$message = __("For best results, it is recommended to have the Users Insights Activity module active when using Device Detection. 
				When the Activity module is deactivated, device detection will be only performed upon a user's manual sign in.", 'usin');
			USIN_Notice::create('info', $message, 'devices_active_without_last_seen', 10 * YEAR_IN_SECONDS);
		}
	}

	/**
	 * Prints the main page markup.
	 */
	public function print_page_markup(){
		?>
		<div class="usin-header-wrap">
			<div class="usin-header">
				<div class="usin-logo-wrap"></div>
				<h2 class="usin-main-title"><?php echo $this->title; ?></h2>
				<div class="clear"></div>
			</div>
		</div>
		<div ng-app="usinModuleApp" class="usin">
			<div class="usin-main"></div>
		</div>
		<?php
	}



}