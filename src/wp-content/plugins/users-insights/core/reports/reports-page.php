<?php

/**
 * Includes the main initialization functionality for the Module Options page.
 */
class USIN_Reports_Page{
	
	protected $capability;
	protected $nonce_key = 'usin_reports';
	protected $assets;
	protected $ajax;
	protected $parent_slug;

	public static $page_slug = 'usin_reports';
	public $slug;
	public $title;
	public $ajax_nonce;

	/**
	 * @param string $parent_slug    the slug of the parent menu item
	 * @param string $capability     the user capability required to access this page
	 * @param USIN_Modules $modules the Module Options object
	 */
	public function __construct($parent_slug){
		$this->slug = self::$page_slug;
		$this->title = __('Reports', 'usin');
		$this->parent_slug = $parent_slug;
		$this->capability = USIN_Capabilities::VIEW_REPORTS;
	}

	/**
	 * Main initialization functionality, registers the required action hooks.
	 */
	public function init(){
		add_action ( 'admin_menu', array($this, 'add_menu_page'), 20 );
		add_action ( 'admin_init', array($this, 'create_nonce') );

		if(self::is_reports_page() || USIN_Reports_Ajax::is_reports_ajax()){
			$this->assets = new USIN_Reports_Assets($this);
			$this->assets->init();

			$this->ajax = new USIN_Reports_Ajax($this->capability, $this->nonce_key);
			$this->ajax->add_actions();
		}
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
		<div ng-app="usinReportsApp" class="usin">
			<usin-main></usin-main>
		</div>
		<?php
	}

	public static function is_reports_page(){
		if(is_admin() && isset($_GET['page']) && $_GET['page'] == self::$page_slug){
			return true;
		}
		return false;
	}



}