<?php
/**
 * UM Gallery Pro Admin Option
 *
 * @version 1.0.0
 */

class UM_GalleryPro_Admin {

	/**
	* Option key, and option page slug
	* @var string
	*/
	private $key = 'um_gallery_pro';

	/**
	* Option key, and option page slug
	* @var string
	*/
	private $setting_key = 'um_gallery_pro_settings';

	/**
	* Options page metabox id
	* @var string
	*/
	private $metabox_id = 'um_gallery_pro';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Active Tab
	 * @var string
	 */
	public $active_tab = '';

	/**
	 * Holds an instance of the object
	 *
	 * @var UM_GalleryPro_Admin
	 **/
	private static $instance = null;

	/**
	 * License Key.
	 *
	 * @var string
	 */
	public $um_license_key = 'um_gallery_pro_license_key';

	/**
	 * License Status.
	 * @var string
	 */
	public $um_license_status = 'um_gallery_pro_license_status';

	public $album = array();
	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'UM Gallery Pro', 'um-gallery-pro' );
		$this->active_tab = ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'general' );
		$this->active_section = ( isset( $_GET['section'] ) && ! empty( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : 'profile' );
		
		if ( isset( $_GET['view'] ) ) {
			$album_id = isset( $_GET['album_id'] ) && ! empty( $_GET['album_id'] ) ? esc_attr( $_GET['album_id'] ) : 0;
			global $wpdb, $album;

			$query = "SELECT a.* FROM {$wpdb->prefix}um_gallery_album AS a WHERE a.id='{$album_id}' ORDER BY a.id DESC";
			$this->album = $wpdb->get_row( $query );
		}
	}

	/**
	 * Returns the running object
	 *
	 * @return UM_GalleryPro_Admin
	 **/
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'update_admin_search_url' ) );
		add_action( 'admin_init', array( $this, 'update_album' ) );
		add_action( 'admin_init', array( $this, 'moderate_addon' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		add_action( 'um_gallery_addon_updated', array( $this, 'alter_database' ), 12, 1 );

		add_action( 'cmb2_render_gheader', array( $this, 'header_field' ), 10, 1 );
		// ajax
		add_action( 'wp_ajax_um_gallery_admin_delete',  array( $this, 'um_gallery_admin_delete' ) );
		//License
		add_action( 'admin_init',  array( $this, 'plugin_updater' ), 0 );
		add_action( 'admin_init',  array( $this, 'register_license_option' ) );
		add_action( 'admin_init',  array( $this, 'activate_license' ) );
		add_action( 'admin_init',  array( $this, 'deactivate_license' ) );
		add_action( 'admin_notices',  array( $this, 'license_notice' ) );
		//add_action( 'um_gallery_pro_album_after_sidebarbox',  array( $this, 'categories_meta_box' ), 12, 1 );
	}

	public function categories_meta_box( $object = array() ) {
		$tax_name = um_gallery()->field->category;
		$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids' );
		$album_terms = wp_get_object_terms( $object->album->id,  'um_gallery_category', $args );
		?>
		<div id="um-gallery-pro-categories" class="postbox">
			<div class="inside">
				<ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
					<?php wp_terms_checklist( $object->album->id, array( 'taxonomy' => $tax_name, 'selected_cats' => $album_terms ) ); ?>
				</ul>
			</div>
		</div>
		<?php
	}
	public function header_field( $field = array() ) {
		//printf( '<h3 class="um-gallery-fields-sub-title">%s</h3>', $field->args['name'] );
	}
	public function alter_database( $addon_id = '' ) {
		global $wpdb;
		$charset_collate = ! empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
		// add the type column to table
		if ( 'videos' == $addon_id ) {
			$result = $wpdb->query( "SHOW COLUMNS FROM `" . $wpdb->prefix . "um_gallery` LIKE 'type'" );
			// if the column doesn't exists then let's add it
			// TODO: In later version, add this to option table to skip this step
			if ( ! $result ) {
				$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'um_gallery` ADD `type` VARCHAR(100) NOT NULL AFTER `description`' );
			}
		}

		if ( 'comments' == $addon_id ) {
			$result = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}um_gallery_comments'" );
			if ( ! $result ) {
				$comments_query = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}um_gallery_comments (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `parent_id` bigint(20) NOT NULL,
					  `user_id` bigint(20) NOT NULL,
					  `photo_id` bigint(20) NOT NULL,
					  `comment` text NOT NULL,
					  `file_url` varchar(255) NOT NULL,
					  `file` varchar(255) NOT NULL,
					  `file_mime_type` varchar(255) NOT NULL,
					  `upvote_count` bigint(20) NOT NULL,
					  `user_has_upvoted` tinyint(2) NOT NULL,
					  `modified_date` DATETIME NULL DEFAULT NULL,
					  `creation_date` DATETIME NULL DEFAULT NULL
				) {$charset_collate};";
				$wpdb->query( $comments_query );
			}
		}

	}
	/**
	 * Add admin notice on album save
	 *
	 * @return  void
	 */
	public function admin_notices() {
		if ( isset( $_GET['album_updated'] ) ) {
		?>
			<div id="message" class="updated notice notice-success">
			  <p><?php esc_html_e( 'Gallery Updated', 'um-gallery-pro' ); ?></p>
			</div>
		<?php
		}

		if ( isset( $_GET['addons_updated'] ) ) {
		?>
		<div id="message" class="updated notice notice-success">
		  <p><?php esc_html_e( 'Addons Updated', 'um-gallery-pro' ); ?></p>
		</div>
		<?php
		}
	}

	/**
	 * Enable or disable addons
	 *
	 *
	 * @return void
	 */
	public function moderate_addon() {
		$option = 'um_gallery_pro_addons';
		if ( isset( $_POST['um_verify_addon_field'] ) ) {
			$nonce = $_POST['um_verify_addon_field'];
			if ( wp_verify_nonce( $nonce, 'um_verify_addon_admin' ) ) {

				$current_addons = get_option( $option );
				if ( empty( $current_addons ) ) {
					$current_addons = array();
				}
				$addon_id = esc_attr( $_POST['addon_id'] );
				if ( 'enable' == $_POST['addon_action'] && ! in_array( $_POST['addon_id'], $current_addons ) ) {
					$current_addons[] = esc_attr( $_POST['addon_id'] );
				} elseif ( 'disable' == $_POST['addon_action'] && in_array( $_POST['addon_id'], $current_addons ) ) {
					$key = array_search( $_POST['addon_id'], $current_addons );
					unset( $current_addons[ $key ] );
				}

				update_option( $option, $current_addons );
				do_action( 'um_gallery_addon_updated', $addon_id, $current_addons );
				$redirect_url = add_query_arg( 'addons_updated', '1', $_POST['_wp_http_referer'] );
				wp_safe_redirect( $redirect_url );
			}
		}
	}

	/**
	 * Update album from admin
	 *
	 * @return void
	 */
	public function update_album() {
		global $wpdb;
		if ( isset( $_POST['um_verify_album_admin_field'] ) ) {
			$nonce = $_POST['um_verify_album_admin_field'];
			if ( wp_verify_nonce( $nonce, 'um_verify_album_admin' ) ) {
				$results           = array();
				$album_id          = 0;
				$user_id           = ( ! empty( $_POST['user_id'] ) ? (int) $_POST['user_id'] : get_current_user_id() );
				$album_name        = ( ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : um_gallery_get_default_album_name( $user_id ) );
				$album_description = ( ! empty( $_POST['description'] ) ? wp_kses_post( $_POST['description'] ) : '' );
				$privacy           = ! empty( $_POST['album_privacy'] ) ? sanitize_text_field( $_POST['album_privacy'] ) : 'public';
				$privacy_state     = um_gallery_privacy_states( $privacy );
				if ( empty( $_POST['album_id'] ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'um_gallery_album',
						array(
							'album_name'        => stripslashes( $album_name ),
							'album_description' => stripslashes( $album_description ),
							'creation_date'	    => date( 'Y-m-d H:i:s' ),
							'user_id'           => $user_id,
							'album_status'      => 1,
							'album_privacy'     => $privacy_state,
						),
						array(
							'%s',
							'%s',
							'%s',
							'%d',
							'%d',
							'%d',
						)
					);
					$album_id = $wpdb->insert_id;
					$results['new'] = true;
				} else {
					$id = (int) $_POST['album_id'];
					$wpdb->update(
						$wpdb->prefix . 'um_gallery_album',
						array(
							'album_name' 		=> $album_name,
							'album_description' => $album_description,
							'user_id' 			=> $user_id,
							'album_privacy'     => $privacy_state,
						),
						array( 'id' => $id ),
						array(
							'%s',
							'%s',
							'%d',
							'%d',
						),
						array( '%d' )
					);
					$album_id = $id;
					$results['new'] = false;
				}
				// Set categories
				// An array of IDs of categories we want this post to have.
				$cat_ids = ! empty( $_POST['tax_input']['um_gallery_category'] ) ? $_POST['tax_input']['um_gallery_category'] : array();
				$cat_ids = array_map( 'intval', $cat_ids );
				$cat_ids = array_unique( $cat_ids );

				$term_taxonomy_ids = wp_set_object_terms( $album_id, $cat_ids, um_gallery()->field->category );

				$results['id'] 		= $album_id;
				$results['user_id'] = $user_id;
				do_action( 'um_gallery_album_updated', $results );
				if ( ! empty( $_POST['_wp_http_referer'] ) ) {
					$redirect_url = add_query_arg( 'album_updated', '1', $_POST['_wp_http_referer'] );
					wp_safe_redirect( $redirect_url );
				}
			}
		}
	}
	public function um_gallery_admin_delete() {
		check_ajax_referer( 'um_gallery_pro_sec', 'sec' );
		$type 	= sanitize_text_field( $_POST['type'] );
		$id		= (int) $_POST['id'];

		if ( empty( $type ) ) {
			wp_send_json_error();
		}

		if ( 'album' == $type ) {
			//delete album
			um_gallery_delete_album( $id );
		} elseif ( 'photo' == $type ) {
			//delete photo
			um_gallery_delete_photo( $id );
		}
		wp_send_json( $_POST );
	}

	public function update_admin_search_url() {
		//$doaction = $wp_list_table->current_action();
		if ( ! empty( $_REQUEST['page'] ) && $this->key == $_REQUEST['page'] && ! empty( $_GET['_wp_http_referer'] ) ) {
			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}
	}
	public function cmb2_celebrations_sanitization( $override_value, $value ) {
		if ( ! empty( $value ) ) {
			$value = maybe_serialize( $value );
		}
		return $value;
	}
	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		//$this->options_page = add_submenu_page( 'edit.php?post_type=um_gallery_pro', $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
		$this->options_page = add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			$this->key,
			array( $this, 'gallery_list' ),
			'dashicons-format-gallery',
			50
		);
		add_submenu_page(
			$this->key,
			__( 'Albums', 'um-gallery-pro' ),
			__( 'Albums', 'um-gallery-pro' ),
			'manage_options',
			$this->key,
			array( $this, 'gallery_list' )
		);

		if ( um_gallery_pro_addon_enabled( 'category' ) ) {
			add_submenu_page( 
				'um_gallery_pro',
				__( 'Categories', 'um-gallery-pro' ),
				__( 'Categories', 'um-gallery-pro' ),
				'manage_options',
				'edit-tags.php?taxonomy=' . um_gallery()->field->category
			);
		}

		if ( um_gallery_pro_addon_enabled( 'tags' ) ) { 
			add_submenu_page( 
				'um_gallery_pro',
				__( 'Tags', 'um-gallery-pro' ),
				__( 'Tags', 'um-gallery-pro' ),
				'manage_options',
				'edit-tags.php?taxonomy=um_gallery_tag'
			);
		}
		// Release in 1.0.9
		/*
		add_submenu_page( 
			'um_gallery_pro',
			__( 'Fields', 'um-gallery-pro' ),
			__( 'Fields', 'um-gallery-pro' ),
			'manage_options',
			'edit.php?post_type=' . um_gallery()->field->field_post_type
		);
		*/
		add_submenu_page(
			$this->key,
			__( 'Settings', 'um-gallery-pro' ),
			__( 'Settings', 'um-gallery-pro' ),
			'manage_options',
			$this->setting_key,
			array( $this, 'um_gallery_settings_page' )
		);
		// Include CMB CSS in the head to avoid FOUC
		//add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_cmb_css' ) );
		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}
	/*
	*	Returns album single view
	*/
	public function album_view_url() {
		global $album;
		return admin_url( 'admin.php?page=' . $this->key . '&view=edit_album&album_id=' . $album['id'] );
	}
	/*
	*
	*/
	public function load_template( $tpl ) {
		$file = UM_GALLERY_PATH . 'admin/templates/' . $tpl . '.php';
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
	/*
	*
	*/
	public function gallery_list() {
		if ( is_admin() ) {
			$screen = get_current_screen();
		}
		if ( empty( $_GET['view'] ) ) {
			$this->load_template( 'gallery-list' );
		} elseif ( ! empty( $_GET['view'] ) && 'edit_album' == $_GET['view'] ) {
			$this->load_template( 'gallery-view' );
		} else {
			$this->load_template( 'gallery-view' );
		}
	}
	/**
	 * Load Settings Template
	 */
	public function um_gallery_settings_page() {
		$this->load_template( 'settings' );
	}


	public function enqueue_cmb_css( $hook ) {
		if ( 'ultimate-member_page_um_gallery_pro' != $hook ) {
			//return;
		}

		wp_enqueue_script(
			'um-gallery-dropzone',
			um_gallery()->plugin_url . '/assets/js/src/dropzone.js',
			array( 'jquery' )
		);
		wp_enqueue_style( 'um-gallery-admin-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css' );
		wp_enqueue_style( 'um-gallery-admin-tag', um_gallery()->plugin_url . 'assets/css/src/jquery.tagit.css' );
		wp_enqueue_script( 'um-gallery-admin', um_gallery()->plugin_url . 'assets/js/um-gallery-pro-admin.min.js', array( 'jquery','jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-autocomplete', 'jquery-ui-position' ) );
		wp_enqueue_style( 'um-gallery-admin', um_gallery()->plugin_url . 'admin/assets/css/um-gallery-admin.css' );
		wp_enqueue_script( 'um-gallery-admin-reveal', um_gallery()->plugin_url . '/admin/assets/js/jquery.slidereveal.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'um-gallery-admin-hb', um_gallery()->plugin_url . '/admin/assets/js/handlebars.js', array( 'jquery' ) );

		// Register the script
		wp_register_script( 'um_gallery_pro', um_gallery()->plugin_url . 'admin/assets/js/um-gallery-admin.js', array( 'jquery' ) );

		// Localize the script with new data
		$obj = array(
			'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
			'nonce' 	=> wp_create_nonce( 'um_gallery_pro_sec' ),
		);
		wp_localize_script( 'um_gallery_pro', 'um_gallery_obj', $obj );
		// Localize the script with new data
		$localization = array(
			'site_url' 				=> site_url(),
			'nonce' 				=> wp_create_nonce( 'um-event-nonce' ),
			'ajax_url' 				=> admin_url( 'admin-ajax.php' ),
			'loading_icon'          => admin_url( 'images/loading.gif' ),
			'is_owner' 				=> true,
			'save_text' 			=> __( 'Save', 'um-gallery-pro' ),
			'edit_text' 			=> __( '<i class="um-faicon-pencil"></i> Edit Caption', 'um-gallery-pro' ),
			'cancel_text' 			=> __( 'Cancel', 'um-gallery-pro' ),
			'album_id' 				=> um_galllery_get_album_id(),
			'dictDefaultMessage' 	=> '<span class="icon"><i class="um-faicon-picture-o"></i></span>
		<span class="str">' . __( 'Upload your photos', 'um-gallery-pro' ) . '</span>',
			'upload_complete' 		=> __( 'Upload Complete', 'um-gallery-pro' ),
			'no_events_txt' 		=> __( 'No photos found.', 'um-gallery-pro' ),
			'confirm_delete' 		=> __( 'Are you sure you want to delete this?', 'um-gallery-pro' ),
		);
		wp_localize_script( 'um_gallery_pro', 'um_gallery_config', $localization );
		// Enqueued script with localized data.
		wp_enqueue_script( 'um_gallery_pro' );
	}
	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		$active_tab = $this->active_tab;
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=general' ); ?>" class="nav-tab <?php echo ( 'general' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'General', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=labels' ); ?>" class="nav-tab <?php echo 'labels' == $active_tab  ? 'nav-tab-active' : ''; ?>"><?php _e( 'Labels', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=license' ); ?>" class="nav-tab <?php echo ( 'license' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'License', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=addons' ); ?>" class="nav-tab <?php echo ( 'addons' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'Addons', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=advanced' ); ?>" class="nav-tab <?php echo ( 'advanced' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'Advanced', 'um-gallery-pro' ); ?></a>
			</h2>
			<?php
			if ( 'license' == $active_tab ) {
				echo '<form method="post" action="options.php">';
				$this->license_fields();
				submit_button( __( 'Update License', 'um-gallery-pro' ), 'primary','submit', true );
				echo '</form>';
			} elseif ( 'addons' === $active_tab ) {
				$this->addons_tab();
			} elseif ( 'advanced' === $active_tab ) {
				$this->tools_tab();
			} elseif ( 'labels' == $active_tab ) {
				cmb2_metabox_form( $this->metabox_id . '-labels', $this->key );
			} elseif ( 'layout' == $active_tab ) {
				cmb2_metabox_form( $this->metabox_id . '-layout', $this->key );
			} else {
				cmb2_metabox_form( $this->metabox_id, $this->key );
			}
			?>
		</div>
		<?php
	}

	public function gallery_admin_head() {
		$active_tab = $this->active_tab;
		?>
		<div class="wrap cmb2-options-page <?php echo $this->setting_key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=general' ); ?>" class="nav-tab <?php echo ( 'general' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'General', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=layout' ); ?>" class="nav-tab <?php echo 'layout' == $active_tab  ? 'nav-tab-active' : ''; ?>"><?php _e( 'Layout', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=labels' ); ?>" class="nav-tab <?php echo 'labels' == $active_tab  ? 'nav-tab-active' : ''; ?>"><?php _e( 'Labels', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=license' ); ?>" class="nav-tab <?php echo ( 'license' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'License', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=addons' ); ?>" class="nav-tab <?php echo ( 'addons' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'Addons', 'um-gallery-pro' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=advanced' ); ?>" class="nav-tab <?php echo ( 'advanced' == $active_tab ? 'nav-tab-active' : '' ); ?>"><?php _e( 'Advanced', 'um-gallery-pro' ); ?></a>
			</h2>
			<?php if ( 'layout' == $active_tab || ! $active_tab ) { ?>
			<!--<div>
				<ul class="subsubsub">
					<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=layout' . '&section=main' ); ?>" class="current">Default Tab</a> | 
					<a href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=layout' . '&section=tab' ); ?>" class="">Tab</a>
				</ul>
			</div>-->
			<?php } ?>
			<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes.
	 *
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {
		global $ultimatemember;
		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'		 => $this->metabox_id,
			'hookup'	 => false,
			'cmb_styles' => true,
			'show_on'	=> array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key ),
			),
		) );

		if ( ! empty( $ultimatemember ) || function_exists( 'UM' ) ) {
			$cmb->add_field( array(
				'name'		=> __( 'Allowed User Roles', 'um-classifieds' ),
				'id'	  	=> 'allowed_roles',
				'type'		=> 'multicheck',
				'options' 	=> function_exists( 'UM' ) ? UM()->roles()->get_roles() : $ultimatemember->query->get_roles(),
			) );

			$cmb->add_field( array(
				'id'       		=> 'um_gallery_tab',
				'type'     		=> 'radio_inline',
				'name'   		=> __( 'Show Gallery Tab','um-gallery-pro' ),
				'default' 		=> 'off',
				'desc' 	   		=> __( 'If enabled, a gallery tab will be placed on a user\'s profile page','um-gallery-pro' ),
				'options' => array(
					'on'			=> __( 'Yes','um-gallery-pro' ),
					'off'			=> __( 'No','um-gallery-pro' ),
				),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_cropped_images',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Disable thumbnails','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'Use full images instead of cropped thumbnails','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
			) );

			$cmb = new_cmb2_box( array(
				'id'         => $this->metabox_id . '-labels',
				'hookup'     => false,
				'cmb_styles' => true,
				'show_on'    => array(
					// These are important, don't remove
					'key'   => 'options-page',
					'value' => array( $this->key ),
				),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_tab_name',
					'type'     		=> 'text',
					'name'   		=> __( 'Tab Name','um-gallery-pro' ),
					'default'		=> __( 'Gallery', 'um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_tab_slug',
					'type'     		=> 'text',
					'name'   		=> __( 'Tab Slug','um-gallery-pro' ),
					'desc' 	   		=> __( 'Slug that displays in URL', 'um-gallery-pro' ),
					'default'		=> __( 'gallery', 'um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_tab_icon',
					'type'     		=> 'text',
					'name'   		=> __( 'Tab Icon','um-gallery-pro' ),
					'desc' 	   		=> __( 'Icon displayed in profile tab', 'um-gallery-pro' ),
					'default'		=> 'um-faicon-camera',
			) );
			
			$cmb->add_field( array(
				'id'       		=> 'um_gallery_default_album_name',
				'type'     		=> 'text',
				'name'   		=> __( 'Default Album Name','um-gallery-pro' ),
				'desc' 	   		=> __( 'Give each album a custom name in single album mode. Use the shortcode [username] or [user_id] to give each album something unique.', 'um-gallery-pro' ),
				'default'		=> __( 'Album by [user_id]','um-gallery-pro' ),
			) );
			
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_add_photo_btn',
					'type'     		=> 'text',
					'name'   		=> __( 'Add Photo Button Text','um-gallery-pro' ),
					'desc' 	   		=> __( 'Displays in single album mode', 'um-gallery-pro' ),
					'default'		=> __( 'Add Photo','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_modal_title',
					'type'     		=> 'text',
					'name'   		=> __( 'Manage Album Title', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Displays above modal popup', 'um-gallery-pro' ),
					'default'		=> __( 'Manage Album','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_add_photos_tab',
					'type'     		=> 'text',
					'name'   		=> __( 'Add Photos', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Photos Tab inside Modal Uploader', 'um-gallery-pro' ),
					'default'		=> __( 'Add Photos','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_add_videos_tab',
					'type'     		=> 'text',
					'name'   		=> __( 'Add Videos', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Videos Tab inside Modal Uploader', 'um-gallery-pro' ),
					'default'		=> __( 'Add Videos','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_upload_photos_text',
					'type'     		=> 'text',
					'name'   		=> __( 'Upload your photos placeholder', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Text inside modal photos upload screen', 'um-gallery-pro' ),
					'default'		=> __( 'Upload your photos','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_video_placeholder_text',
					'type'     		=> 'text',
					'name'   		=> __( 'Video URL Placeholder', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Placeholder text inside of video uploader field', 'um-gallery-pro' ),
					'default'		=> __( 'Video URL','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_add_video_button',
					'type'     		=> 'text',
					'name'   		=> __( 'Add Video Button Text', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Text inside of video add button', 'um-gallery-pro' ),
					'default'		=> __( 'Add Video','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_save_button',
					'type'     		=> 'text',
					'name'   		=> __( 'Save Button', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Save button inside of modal photos uploader', 'um-gallery-pro' ),
					'default'		=> __( 'Save','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_cancel_button',
					'type'     		=> 'text',
					'name'   		=> __( 'Cancel Button', 'um-gallery-pro' ),
					'desc' 	   		=> __( 'Cancel button inside of modal photos uploader', 'um-gallery-pro' ),
					'default'		=> __( 'Cancel','um-gallery-pro' ),
			) );

			$cmb = new_cmb2_box( array(
				'id'         => $this->metabox_id . '-layout',
				'hookup'     => false,
				'cmb_styles' => true,
				'show_on'    => array(
					// These are important, don't remove
					'key'   => 'options-page',
					'value' => array( $this->key ),
				),
			) );
			$cmb->add_field( array(
					'id'       		=> 'main_tab_header',
					'type'     		=> 'gheader',
					'name'			=> __( 'Gallery Tab', 'um-gallery-pro' )
			) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_type',
					'type'     		=> 'select',
					'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
					'name'    		=> __( 'Profile Layout Type','um-gallery-pro' ),
					'desc' 	   		=> __( 'Select the type of layout for photos on the main tab','um-gallery-pro' ),
					'default'  		=> 'grid',
					'options' 		=> array(
										'carousel' 		=> __( 'Carousel','um-gallery-pro' ),
										'grid' 			=> __( 'Grid','um-gallery-pro' ),
										'slideshow' 	=> __( 'Slideshow','um-gallery-pro' ),
					),
					'placeholder' 	=> __( 'Choose layout...','um-gallery-pro' ),
			) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_single_album',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Single Album Mode','um-gallery-pro' ),
					'default' 		=> '0',
					'desc' 	   		=> __( 'If enabled, users will only be allowed to create one album','um-gallery-pro' ),
					'options' 		=> array(
						'1'			=> __( 'Yes','um-gallery-pro' ),
						'0'			=> __( 'No','um-gallery-pro' ),
					),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_profile_count',
					'type'     		=> 'text',
					'name'   		=> __( 'Photos on profile','um-gallery-pro' ),
					'desc' 	   		=> __( 'Set the number of photos on profile','um-gallery-pro' ),
					'default'		=> 10,
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_tab',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Show Gallery Tab','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'If enabled, a gallery tab will be placed on a user\'s profile page','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
			) );

			$cmb->add_field( array(
					'id'       		=> 'main_profile_header',
					'type'     		=> 'gheader',
					'name'			=> __( 'Main/Profile Tab' )
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_main_gallery_type',
					'type'     		=> 'select',
					'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
					'name'    		=> __( 'Profile Layout Type','um-gallery-pro' ),
					'desc' 	   		=> __( 'Select the type of layout for gallery on gallery tab','um-gallery-pro' ),
					'default'  		=> 'grid',
					'options' 		=> array(
										'carousel' 		=> __( 'Carousel','um-gallery-pro' ),
										'grid' 			=> __( 'Grid','um-gallery-pro' ),
										'slideshow' 	=> __( 'Slideshow','um-gallery-pro' ),
					),
					'placeholder' 	=> __( 'Choose layout...','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'carousel_setting',
					'type'     		=> 'gheader',
					'name'			=> __( 'Carousel/Slideshow settings', 'um-gallery-pro' ),
					'description'	=> __( 'Changed the settings used by the Carousel or Slideshow below.', 'um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_carousel_item_count',
					'type'     		=> 'text',
					'name'   		=> __( 'Number of items in Carousel','um-gallery-pro' ),
					'desc' 	   		=> __( 'Set the number of photos to display in Carousel','um-gallery-pro' ),
					'default'		=> 10,
			) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_seconds_count',
					'type'     		=> 'text',
					'name'   		=> __( 'Number of seconds for Autoplay','um-gallery-pro' ),
					'desc' 	   		=> __( 'Set the Slideshow/Carousel Autoplay in seconds','um-gallery-pro' ),
					'default'		=> 0,
			) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_autoplay',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'AutoPlay Slideshow/Carousel','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'If enabled, the gallery will auto play on a user\'s profile page','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_pagination',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Turn Pagination On','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'Enable this to display Pagination','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
			) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_autoheight',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Turn AutoHeight On','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'Enable this to turn AutoHeight on','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
			) );

			$cmb->add_field( array(
					'id'       		=> 'mischeader',
					'type'     		=> 'gheader',
					'name'			=> __( 'Other', 'um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_fullscreen',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Show full screen button','um-gallery-pro' ),
					'default' 		=> 'on',
					'desc' 	   		=> __( 'Enable this to show the fullscreen button','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
			) );

			$cmb->add_field( array(
					'id'       		=> 'close_modal_save',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Close Modal after update','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'Enable this to close modal after an album is updated or after files and videos have been added.','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
			) );

			$cmb = new_cmb2_box( array(
				'id'         => $this->metabox_id . '-layout-main',
				'hookup'     => false,
				'cmb_styles' => true,
				'show_on'    => array(
					// These are important, don't remove
					'key'   => 'options-page',
					'value' => array( $this->key ),
				),
			) );
			
			$cmb->add_field( array(
				'id'       		=> 'um_gallery_profile',
				'type'     		=> 'radio_inline',
				'name'   		=> __( 'Show on Main Tab','um-gallery-pro' ),
				'default' 		=> 'on',
				'desc' 	   		=> __( 'If enabled, recent photo uploads will be placed on a user\'s profile main tab','um-gallery-pro' ),
				'options' => array(
					'on'			=> __( 'Yes','um-gallery-pro' ),
					'off'			=> __( 'No','um-gallery-pro' ),
				),
			) );
			

			$cmb->add_field( array(
					'id'       		=> 'um_main_gallery_type',
					'type'     		=> 'select',
					'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
					'name'    		=> __( 'Profile Layout Type','um-gallery-pro' ),
					'desc' 	   		=> __( 'Select the type of layout for gallery on gallery tab','um-gallery-pro' ),
					'default'  		=> 'grid',
					'options' 		=> array(
										'carousel' 		=> __( 'Carousel','um-gallery-pro' ),
										'grid' 			=> __( 'Grid','um-gallery-pro' ),
										'slideshow' 	=> __( 'Slideshow','um-gallery-pro' ),
					),
					'placeholder' 	=> __( 'Choose layout...','um-gallery-pro' ),
			) );

			$cmb->add_field( array(
					'id'       		=> 'main_carousel_setting',
					'type'     		=> 'gheader',
					'name'			=> __( 'Carousel/Slideshow settings', 'um-gallery-pro' ),
					'description'	=> __( 'Changed the settings used by the Carousel or Slideshow below.', 'um-gallery-pro' ),
					'classes'       => 'umg-carousel',
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_main_carousel_item_count',
					'type'     		=> 'text',
					'name'   		=> __( 'Number of items in Carousel at once','um-gallery-pro' ),
					'desc' 	   		=> __( 'Set the number of photos to display in Carousel','um-gallery-pro' ),
					'default'		=> 10,
					'classes'       => 'umg-carousel',
			) );
			$cmb->add_field( array(
				'id'       		=> 'um_gallery_main_max_carousel_item_count',
				'type'     		=> 'text',
				'name'   		=> __( 'Maiximum Number of items in entire Carousel','um-gallery-pro' ),
				'desc' 	   		=> __( 'Set the number of photos to display in Carousel after scrolling/sliding.','um-gallery-pro' ),
				'default'		=> 10,
				'classes'       => 'umg-carousel',
		) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_main_seconds_count',
					'type'     		=> 'text',
					'name'   		=> __( 'Number of seconds for Autoplay','um-gallery-pro' ),
					'desc' 	   		=> __( 'Set the Slideshow/Carousel Autoplay in seconds','um-gallery-pro' ),
					'default'		=> 0,
					'classes'       => 'umg-carousel',
			) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_main_autoplay',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'AutoPlay Slideshow/Carousel','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'If enabled, the gallery will auto play on a user\'s profile page','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
					'classes'       => 'umg-carousel',
			) );

			$cmb->add_field( array(
					'id'       		=> 'um_gallery_main_pagination',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Turn Pagination On','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'Enable this to display Pagination','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
					'classes'       => 'umg-carousel',
			) );
			$cmb->add_field( array(
					'id'       		=> 'um_gallery_main_autoheight',
					'type'     		=> 'radio_inline',
					'name'   		=> __( 'Turn AutoHeight On','um-gallery-pro' ),
					'default' 		=> 'off',
					'desc' 	   		=> __( 'Enable this to turn AutoHeight on','um-gallery-pro' ),
					'options' => array(
						'on'			=> __( 'Yes','um-gallery-pro' ),
						'off'			=> __( 'No','um-gallery-pro' ),
					),
					'classes'       => 'umg-carousel',
			) );
		}

	}

	/**
	 * Private method used to output field name and description fields.
	 *
	 * @since 1.0.6
	 */
	private function name_and_description() {
		global $album;
	?>

		<div id="titlediv">
			<div class="titlewrap">
				<label id="title-prompt-text" for="title"></label>
				<input type="text" name="title" id="title" value="<?php echo esc_attr( $album->album_name ); ?>" placeholder="<?php echo esc_attr__( 'Name (required)', 'um-gallery-pro' ); ?>" autocomplete="off" />
			</div>
		</div>

		<div class="postbox">
			<h2><?php echo esc_html_x( 'Description', 'UM Gallery Pro admin edit field', 'um-gallery-pro' ); ?></h2>
			<div class="inside">
				<label for="description" class="screen-reader-text"><?php
					/* translators: accessibility text */
					esc_html_e( 'Add description', 'buddypress' );
				?></label>
				<textarea name="description" class="um-gallery-text-edit" id="description" rows="8" cols="60"><?php echo esc_textarea( $album->album_description ); ?></textarea>
			</div>
		</div>

	<?php
	}

	private function add_photo_button() {
		/**
		 * TODO: Make functional in next version
		 */
		return;
		?>
		<div class="um-gallery-pro-button-wrapper"><a href="#" class="um-gallery-form"><span class="dashicons dashicons-plus-alt"></span> <?php _e( 'Add Images','um-gallery-pro' ); ?></a></div>
		<?php
	}
	private function gallery_items( $album_id ) {
		global $wpdb;
		global $photo;
		$query = "SELECT a.* FROM {$wpdb->prefix}um_gallery AS a WHERE a.album_id='{$album_id}' ORDER BY a.id DESC";
		$photos = $wpdb->get_results( $query );
		if ( ! empty( $photos ) ) :
			echo '<div class="um-gallery-album-list">';
			foreach ( $photos as $item ) :
				$photo = um_gallery_setup_photo( $item );
				?>
				<div class="um-gallery-grid-item">
				  <div class="um-gallery-inner">
					<div class="um-gallery-img"><a href="#"><img src="<?php um_gallery_the_image_url(); ?>"></a></div>
					<div class="um-gallery-info">
					  <div class="um-gallery-title"><h2><?php echo $photo->caption; ?></h2><?php /*?><a href="<?php //echo um_gallery()->admin->album_view_url(); ?>"><?php echo $photo->caption; ?></a><?php */?></div>
					  <div class="um-gallery-meta"></div>
					  <div class="um-gallery-action">
						  <a href="#" class="um-gallery-delete-photo" data-item_id="<?php echo $photo->id; ?>" data-type="photo"><span class="dashicons dashicons-trash"></span></a>
						  <a href="#" class="um-gallery-edit-photo" data-ps-options="{bodyClass: 'ps-active'}" data-item_id="<?php echo $photo->id; ?>" data-type="photo"><span class="dashicons dashicons-edit"></span></a>
					  </div>
					</div>
				  </div>
				</div>
				<?php
			endforeach;
			echo '</div>';
		else :
			?>
			<div class="um-gallery-none postbox">
				<div class="inside">
					<?php _e( 'No media found', 'um-gallery-pro' ); ?>
				</div>
			</div>
			<?php
		endif;
	}

	private function publishing_options() {
		global $album;
		$selected_user = $album->user_id;
		?>
		<div id="um-gallery-pro-publishing" class="postbox">
			<h2><?php _e( 'Actions', 'buddypress' ); ?></h2>
			<div class="inside">
				<div class="um-gallery-pro-user-list um-gallery-pro-action-row">
					<label for="user_id"><?php _e( 'Owner', 'um-gallery-pro' ); ?></label>
					<select name="user_id" id="user_id">
					<?php foreach ( $this->get_users_list() as $k => $user_id ) { ?>
						<?php um_fetch_user( $user_id ); ?>
						<option value="<?php echo $user_id; ?>" <?php echo ( $user_id == $selected_user ? ' selected="selected" ' : '' ); ?>><?php echo um_user( 'display_name' ) ?></option>
						<?php um_reset_user(); ?>
					<?php } ?>
					</select>
				</div>
				<div class="um-gallery-pro-button-wrapper"><input type="submit" name="submit_album_admin" value="<?php _e( 'Save Album','um-gallery-pro' ); ?>" class="button button-primary" /></div>
			</div>
		</div>
		<?php
	}

	private function get_users_list() {
		global $wpdb;
		$users = $wpdb->get_col( "SELECT ID FROM {$wpdb->users} ORDER BY display_name" );
		return $users;
	}

	public function addons_tab() {
		$this->load_template( 'addons' );
	}

	public function tools_tab() {
		$this->load_template( 'tools' );
	}
	public function license_fields() {
		$license 	= get_option( $this->um_license_key );
		$status 	= get_option( $this->um_license_status );
		?>
		<?php settings_fields( $this->um_license_key . '_field' ); ?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'License Key' ); ?>
					</th>
					<td>
						<input id="um_license_key" name="<?php echo esc_attr( $this->um_license_key ); ?>"  type="text"  class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
						<label class="description" for="um_license_key"><?php _e( 'Enter your license key' ); ?></label>
					</td>
				</tr>
				<?php if ( false !== $license ) { ?>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php esc_html_e( 'Activate License' ); ?>
						</th>
						<td>
							<?php if ( false !== $status   && 'valid' == $status ) { ?>
								<span style="color:green;"><?php _e( 'Active' ); ?></span>
								<?php wp_nonce_field( 'um_gallery_pro_license_nonce', 'um_gallery_pro_license_nonce' ); ?>
								<input type="submit" class="button-secondary" name="um_gallery_pro_license_deactivate" value="<?php esc_attr_e( 'Deactivate License' ); ?>"/>
							<?php } else {
								wp_nonce_field( 'um_gallery_pro_license_nonce', 'um_gallery_pro_license_nonce' ); ?>
								<input type="submit" class="button-secondary" name="um_gallery_pro_license_activate" value="<?php esc_attr_e( 'Activate License', 'um-gallery-pro' ); ?>"/>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}

	public function plugin_updater() {

		// retrieve our license key from the DB
		$license_key = trim( get_option( $this->um_license_key ) );

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( UM_GALLERY_STORE_URL, UM_GALLERY_LICENSE_PATH, array(
				'version' 	=> UM_GALLERY_PRO_VERSION, // current version number
				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
				'item_name' => UM_GALLERY_ITEM_NAME, 	// name of this plugin
				'item_id'   => UM_GALLERY_PRO_ITEM_ID,
				'author' 	=> 'SuitePlugins', // author of this plugin
			)
		);
	}

	public function register_license_option() {
		register_setting( $this->um_license_key . '_field', $this->um_license_key, array( $this, 'sanitize_license' ) );
	}
	public function sanitize_license( $new ) {
		$old = get_option( $this->um_license_key );
		if ( $old && $old != $new ) {
			delete_option( $this->um_license_status ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/************************************
	* this illustrates how to activate
	* a license key
	*************************************/

	public function activate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['um_gallery_pro_license_activate'] ) ) {


			// run a quick security check
			if ( ! check_admin_referer( 'um_gallery_pro_license_nonce', 'um_gallery_pro_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}
			
			// retrieve the license from the database
			$license = trim( get_option( $this->um_license_key ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'	=> 'activate_license',
				'license' 		=> $license,
				'item_name' 	=> urlencode( UM_GALLERY_ITEM_NAME ), // the name of our product in EDD
				'item_id'       => UM_GALLERY_PRO_ITEM_ID,
				'url'	   		=> home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post( UM_GALLERY_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );


			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'disabled':
						case 'revoked' :

							$message = __( 'Your license key has been disabled.' );
							break;

						case 'missing' :

							$message = __( 'Invalid license.' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), UM_GALLERY_ITEM_NAME );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.' );
							break;

						default :

							$message = __( 'An error occurred, please try again.' );
							break;
					}

					if ( ! empty( $message ) ) {
						$base_url = admin_url( 'admin.php?page=um_gallery_pro_settings&tab=license' );
						$redirect = add_query_arg( array( 'um_gallery_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

						wp_redirect( $redirect );
						exit();
					}
				}
			}


			if ( $license_data && isset( $license_data->license ) ) {
				update_option( $this->um_license_status, $license_data->license );
			}

			wp_redirect( admin_url( 'admin.php?page=um_gallery_pro_settings&tab=license' ) );
			exit;
			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "valid" or "invalid"
			update_option( $this->um_license_status, $license_data->license );

		}
	}


	/***********************************************
	* Illustrates how to deactivate a license key.
	* This will descrease the site count
	***********************************************/

	function deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['um_gallery_pro_license_deactivate'] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'um_gallery_pro_license_nonce', 'um_gallery_pro_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( $this->um_license_key ) );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( UM_GALLERY_ITEM_NAME ), // the name of our product in EDD
				'item_id'    => UM_GALLERY_PRO_ITEM_ID,
				'url'        => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post( UM_GALLERY_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( 'deactivated' == $license_data->license ) {
				delete_option( $this->um_license_status );
			}
		}
	}

	public function license_notice() {
		if ( isset( $_GET['um_gallery_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch( $_GET['um_gallery_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo wp_kses_post( $message ); ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:

					break;

			}
		}
	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed		  Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the UM_GalleryPro_Admin object
 * @since  0.1.0
 * @return UM_GalleryPro_Admin object
 */
function um_gallery_pro_admin() {
	return UM_GalleryPro_Admin::get_instance();
}

// Get it started
um_gallery_pro_admin();
