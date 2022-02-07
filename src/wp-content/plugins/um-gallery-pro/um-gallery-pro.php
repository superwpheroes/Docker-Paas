<?php
/*
Plugin Name: User Gallery for Ultimate Member
Plugin URI: https://suiteplugins.com/downloads/gallery-for-ultimate-members/
Description: Allow your user to upload photos and import video to their Ultimate Member profile
Version: 1.0.9.5.8
Author: SuitePlugins
Author URI: https://suiteplugins.com/
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UM_GALLERY_URL', plugin_dir_url( __FILE__ ) );
define( 'UM_GALLERY_PATH', plugin_dir_path( __FILE__ ) );
define( 'UM_GALLERY_PLUGIN', plugin_basename( __FILE__ ) );
define( 'UM_GALLERY_LICENSE_PATH', __FILE__ );
define( 'UM_GALLERY_STORE_URL', 'https://suiteplugins.com' );
define( 'UM_GALLERY_ITEM_NAME', 'Gallery for Ultimate Member' );
define( 'UM_GALLERY_PRO_VERSION', '1.0.9.5.8' );
define( 'UM_GALLERY_PRO_ITEM_ID', 171 );

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Bail if UM not on.
if ( ! function_exists( 'UM' ) ) {
	return;
}
require_once( UM_GALLERY_PATH . 'vendor/cmb2/cmb2/init.php' );
require_once( UM_GALLERY_PATH . 'includes/um-gallery-admin-list.php' );
require_once( UM_GALLERY_PATH . 'includes/um-gallery-admin.php' );
require_once( UM_GALLERY_PATH . 'includes/class-um-gallery-template.php' );
require_once( UM_GALLERY_PATH . 'includes/class-um-gallery-field.php' );
require_once( UM_GALLERY_PATH . 'includes/um-gallery-ajax.php' );
require_once( UM_GALLERY_PATH . 'includes/um-gallery-shortcodes.php' );
require_once( UM_GALLERY_PATH . 'includes/um-gallery-functions.php' );
require_once( UM_GALLERY_PATH . 'includes/um-gallery-activity.php' );
require_once( UM_GALLERY_PATH . 'includes/um-gallery-comments.php' );
require_once( UM_GALLERY_PATH . 'includes/class-um-gallery-privacy.php' );

// Include widgets
require_once( UM_GALLERY_PATH . 'includes/widgets/class-widget-recent-photos.php' );
/**
 * Check if Class exists
 */
if ( ! class_exists( 'UM_Gallery_Pro' ) ) :
	/**
	 *	Setup Class
	 */
	class UM_Gallery_Pro {

		protected static $_instance = null;
		/**
		 * Main UM_Gallery_Pro Instance
		 *
		 * Ensures only one instance of UM_Gallery_Pro is loaded or can be loaded.
		 *
		 * @static
		 * @return UM_Gallery_Pro - Main instance
		 */

		/**
		 * UM_GalleryPro_Admin object
		 *
		 * @var UM_GalleryPro_Admin|null
		 * @since  1.0.6
		 */
		public $admin = null;

		/**
		 * URL of plugin directory
		 *
		 * @var string
		 * @since  1.0.6
		 */
		protected $url = '';

		/**
		 * Path of plugin directory
		 *
		 * @var string
		 * @since  1.0.6
		 */
		protected $path = '';

		/**
		 * Plugin basename
		 *
		 * @var string
		 * @since  1.0.6
		 */
		protected $basename = '';

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Initiate construct
		 */
		public function __construct() {
			/** Paths *************************************************************/

			// Setup some base path and URL information
			$this->file       		= __FILE__;
			$this->basename   		= apply_filters( 'um_gallery_plugin_basenname', plugin_basename( $this->file ) );
			$this->plugin_dir 		= apply_filters( 'um_gallery_plugin_dir_path',  plugin_dir_path( $this->file ) );
			$this->plugin_url 		= apply_filters( 'um_gallery_plugin_dir_url',   plugin_dir_url( $this->file ) );
			$this->plugin_slug 		= '';
			$upload_dir 			= wp_upload_dir();
			$this->gallery_path 	= $upload_dir['basedir'] . '/um-gallery';
			$this->gallery_url_path = $upload_dir['baseurl'] . '/um-gallery';
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			//$this->includes();
			$this->setup_hooks();
			$this->plugin_classes();
		}
		/**
		*	Contains hooks
		*
		**/
		public function setup_hooks() {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ), 99 );
			//create an action and try to place content below the profile fields
			//add_action( 'init', array( $this, 'plugin_classes' ) );
			add_action( 'cmb2_init', array( $this, 'load_admin_section' ) );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		}

		public function register_widgets() {
			register_widget( 'Widget_Recent_Photos' );
		}
		/**
		 * Initiated plugin classes
		 */
		public function plugin_classes() {
			if ( um_gallery_pro_addon_enabled( 'activity' ) ) {
				$this->activity 	= new UM_Gallery_Activity();
			}
			$this->admin 		= new UM_GalleryPro_Admin();
			$this->field 		= new UM_Gallery_Field();
			$this->comments 	= new UM_Gallery_Comments();
			$this->ajax 		= new UM_Gallery_Pro_AJAX();
			$this->template		= new UM_Gallery_Pro_Template();
			$this->shortcode	= new UM_Gallery_Shortcodes();
			$this->privacy      = new UM_Gallery_Pro_Privacy();
		}

		/**
		 * Load language file
		 *
		 * @since  1.0.0
		 */
		public function load_plugin_textdomain() {

			$loaded = load_plugin_textdomain( 'um-gallery-pro', false, '/languages/' );

			if ( ! $loaded ) {
				$loaded = load_muplugin_textdomain( 'um-gallery-pro', '/languages/' );
			}

			if ( ! $loaded ) {
				$loaded = load_theme_textdomain( 'um-gallery-pro', get_stylesheet_directory() . '/languages/' );
			}

			if ( ! $loaded ) {
				$locale = apply_filters( 'plugin_locale', get_locale(), 'um-gallery-pro' );
				$mofile = dirname( __FILE__ ) . '/languages/um-gallery-pro-' . $locale . '.mo';
				load_textdomain( 'um-gallery-pro', $mofile );
			}
		}

		/**
		 * Include necessary files.
		 *
		 * @since  1.0.0
		 */
		public function includes() {
			require_once( $this->plugin_dir . 'includes/um-gallery-admin-list.php' );
			require_once( $this->plugin_dir . 'includes/um-gallery-admin.php' );
			require_once( $this->plugin_dir . 'includes/class-um-gallery-template.php' );
			require_once( $this->plugin_dir . 'includes/um-gallery-ajax.php' );
			require_once( $this->plugin_dir . 'includes/um-gallery-shortcodes.php' );
			require_once( $this->plugin_dir . 'includes/um-gallery-functions.php' );
			require_once( $this->plugin_dir . 'includes/um-gallery-activity.php' );
		}

		/**
		 * Load admin files
		 * @return  void
		 */
		public function load_admin_section() {

		}

		/**
		 * Check if user has access
		 *
		 * @return boolean [description]
		 */
		public function is_owner() {
			global $album, $photo;
			//logged in ID
			$my_id = get_current_user_id();
			//get profile ID
			$profile_id = um_get_requested_user();
			//if not logged in then return false
			if ( ! $my_id ) {
				return false;
			}
			//if album not empty then we are in album loop
			if ( ! empty( $album ) ) {
				if ( $my_id == $album->user_id ) {
					return true;
				} else {
					return false;
				}
			}
			//check if we are in photos loop
			if ( ! empty( $photo ) ) {
				if ( $my_id == $photo->user_id ) {
					return true;
				} else {
					return false;
				}
			}
			if ( $profile_id == $my_id ) :
				return true;
			else :
				return false;
			endif;
		}

		/**
		 * Add JS and Styles
		 */
		public function add_scripts() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_style(
				'um-gallery-style',
				plugins_url( '/assets/css/um-gallery-pro' . $suffix . '.css' , __FILE__ )
			);
			global $ultimatemember;

			if ( ! empty( $ultimatemember->options['active_color'] ) ) {
					$color = esc_attr( $ultimatemember->options['active_color'] );
					$custom_css = "
					.um-gallery-pro-action-buttons li.active, .um-gallery-pro-action-buttons li:hover{
							border-top-color: {$color};
					}
					.um-gallery-pro-action-buttons a{
						color: {$color};
					}
					.um-gallery-action > a,
					#um-gallery-comments.jquery-comments .highlight-background,
					#um-gallery-comments.jquery-comments ul.navigation li.active:after{
						background-color: {$color} !important;
					}
					a.um-gallery-form.um-gallery-btn, .um-gallery-btn{
						color: {$color};
					}
					.um-gallery-spinner > div {
					  background-color: {$color};
					}
					"
					;
					wp_add_inline_style( 'um-gallery-style', $custom_css );
			}

			wp_enqueue_style(
				'um-gallery-style-carousel',
				plugins_url( 'assets/components/owl.carousel/assets/owl.carousel.min.css' , __FILE__ )
			);

			wp_enqueue_script(
				'um-gallery-carousel',
				plugins_url( 'assets/components/owl.carousel/owl.carousel.min.js' , __FILE__ ),
				array( 'jquery' )
			);

			wp_enqueue_style( 'um-gallery-admin-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css' );
			
			/**
			 * Register new JavaScript file.
			 *
			 * @since r16
			 * @param string $handle Script name
			 * @param string $src Script url
			 * @param array $deps (optional) Array of script names on which this script depends
			 * @param string|bool $ver (optional) Script version (used for cache busting), set to NULL to disable
			 * @param bool (optional) Wether to enqueue the script before </head> or before </body>
			 * @return null
			 */
			wp_register_script( 'um_gallery', um_gallery()->plugin_url . 'assets/js/um-gallery-pro' . $suffix . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-autocomplete', 'jquery-ui-position', 'jquery-masonry', 'masonry' ), UM_GALLERY_PRO_VERSION, true );
			// Localize the script with new data
			$localization = array(
				'site_url' 				=> site_url(),
				'nonce' 				=> wp_create_nonce( 'um-event-nonce' ),
				'ajax_url' 				=> admin_url( 'admin-ajax.php' ),
				'is_owner' 				=> $this->is_owner(),
				'enable_comments'		=> um_gallery_pro_addon_enabled( 'comments' ),
				'save_text' 			=> __( 'Save', 'um-gallery-pro' ),
				'edit_text' 			=> __( '<i class="um-faicon-pencil"></i> Edit Caption', 'um-gallery-pro' ),
				'cancel_text' 			=> __( 'Cancel', 'um-gallery-pro' ),
				'album_id' 				=> um_galllery_get_album_id(),
				'dictDefaultMessage' 	=> '<span class="icon"><i class="um-faicon-picture-o"></i></span>
			<span class="str">' . __( 'Upload your photos', 'um-gallery-pro' ) . '</span>',
				'upload_complete' 		=> __( 'Upload Complete', 'um-gallery-pro' ),
				'no_events_txt' 		=> __( 'No photos found.', 'um-gallery-pro' ),
				'confirm_delete' 		=> __( 'Are you sure you want to delete this?', 'um-gallery-pro' ),
				'layout_mode'			=> um_gallery_pro_get_option( 'um_gallery_single_album', 0 ),
				'show_full_screen'      => 'on' === um_gallery_pro_get_option( 'um_gallery_fullscreen' ) ? true : false,
				'closeModalAfterSave'   => 'on' === um_gallery_pro_get_option( 'close_modal_save' ) ? false : true,
			);
			$localization['user']  = array();
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				um_fetch_user( $user_id );
				$localization['user']  = array(
					'id' 		=> $user_id,
					'name' 		=> um_user( 'display_name' ),
					'link' 		=> um_user_profile_url(),
					'avatar' 	=> um_get_user_avatar_url(),
				);
				um_reset_user();
			}

			// Comment text.
			$localization['comments']['textareaPlaceholderText'] = esc_html__( 'Add a comment', 'um-gallery-pro' );
			$localization['comments']['newestText']              = esc_html__( 'Newest', 'um-gallery-pro' );
			$localization['comments']['oldestText']              = esc_html__( 'Oldest', 'um-gallery-pro' );
			$localization['comments']['popularText']             = esc_html__( 'Popular', 'um-gallery-pro' );
			$localization['comments']['attachmentsText']         = esc_html__( 'Attachments', 'um-gallery-pro' );
			$localization['comments']['sendText']                = esc_html__( 'Send', 'um-gallery-pro' );
			$localization['comments']['replyText']               = esc_html__( 'Reply', 'um-gallery-pro' );
			$localization['comments']['editText']                = esc_html__( 'Edit', 'um-gallery-pro' );
			$localization['comments']['editedText']              = esc_html__( 'Edited', 'um-gallery-pro' );
			$localization['comments']['youText']                 = esc_html__( 'You', 'um-gallery-pro' );
			$localization['comments']['saveText']                = esc_html__( 'Save', 'um-gallery-pro' );
			$localization['comments']['deleteText']              = esc_html__( 'Delete', 'um-gallery-pro' );
			$localization['comments']['viewAllRepliesText']      = esc_html__( 'View all __replyCount__ replies', 'um-gallery-pro' );
			$localization['comments']['hideRepliesText']         = esc_html__( 'Hide replies', 'um-gallery-pro' );
			$localization['comments']['noCommentsText']          = esc_html__( 'No comments', 'um-gallery-pro' );
			$localization['comments']['noAttachmentsText']       = esc_html__( 'No attachments', 'um-gallery-pro' );
			$localization['comments']['attachmentDropText']      = esc_html__( 'Drop files here', 'um-gallery-pro' );
			wp_localize_script( 'um_gallery', 'um_gallery_config', $localization );
			wp_enqueue_script( 'um_gallery' );
		}
		/**
		 * Get image src.
		 * 
		 * @param  integer $user_id
		 * @param  string  $name
		 * @param  string  $size
		 * 
		 * @return false|string
		 */
		public function get_user_image_src( $photo = array(), $size = 'thumbnail', $force = false, $type = 'photo' ) {
			if ( empty( $photo ) ) {
				return um_gallery_default_thumb();
			}

			if ( empty(  $photo->user_id ) ) {
				$user_id = 0;
			} else {
				$user_id = $photo->user_id;
			}
			
			$name = $photo->file_name;

			if ( ! empty( $photo->type ) && 'youtube' == $photo->type ) {				

				preg_match( '/(?:https?:\/\/|www\.|gaming\.|m\.|^)youtu(?:be\.com\/watch\?(?:.*?&(?:amp;)?)?v=|\.be\/)([\w‌​\-]+)(?:&(?:amp;)?[\w\?=]*)?/', $photo->file_name, $matches );


				if ( ! empty( $matches[1] ) ) {
					return '//i.ytimg.com/vi/' . $matches[1] . '/0.jpg?custom=true&w=150&h=1150';
				}
			}

			if ( ! empty( $photo->type ) && 'hudl' == $photo->type ) {
				$request = wp_remote_get( esc_url( $photo->file_name ) );
				if( is_wp_error( $request ) ) {
					return false; // Bail early
				}

				$body = wp_remote_retrieve_body( $request );

				$old_libxml_error = libxml_use_internal_errors(true);
				$doc = new DOMDocument();
				$doc->loadHTML($body);
				
				libxml_use_internal_errors($old_libxml_error);
				$tags = $doc->getElementsByTagName('meta');
				if (! $tags || $tags->length === 0 ) {
					return false;
				}
				$meta_og_img = null;
				foreach( $tags as $meta) {
					//If the property attribute of the meta tag is og:image
					if( $meta->getAttribute('property')=='og:image'){ 
						//Assign the value from content attribute to $meta_og_img
						$meta_og_img = $meta->getAttribute('content');
					}
				}

				if ( $meta_og_img ) {
					return $meta_og_img;
				}
			}
			if ( ! empty( $photo->type ) && 'vimeo' == $photo->type ) {
				$video_id = explode( '/', $photo->file_name );
				$video['id'] = $video_id[3];
				$video_thumbnail = wp_remote_get( 'https://vimeo.com/api/v2/video/' . $video['id'] . '.json' );
				if ( false === $video_thumbnail ) {
					$video_thumbnail = 'null';
				}
				$video_body = json_decode( $video_thumbnail['body'] );
				return $video_body[0]->thumbnail_large;
			}
			$image = $this->gallery_url_path . '/' . $user_id . '/' . $name;

			$use_cropped_image = um_gallery_use_cropped_images();
			if ( $use_cropped_image && ! $force ) {
				return $image;
			}
			if ( 'thumbnail' == $size ) {
				$filetype = wp_check_filetype( $image );
				$basename = basename( $image, '.' . $filetype['ext'] );
				$image_path_url = $this->gallery_url_path . '/' . $user_id . '/' . $basename . '-thumbnail.' . $filetype['ext'];
				$image_path = $this->gallery_path . '/' . $user_id . '/' . $basename . '-thumbnail.' . $filetype['ext'];

				if ( ! file_exists( $image_path ) ) {
					$image_path_url = um_gallery_default_thumb();
				}
				return $image_path_url;
			}
			return $image;
		}

		/**
		 * Get the Media URL.
		 * 
		 * @param  array  $photo Media Object
		 * @return string        [description]
		 */
		function um_gallery_get_media_url( $media = array() ) {
			if ( empty( $media ) ) {
				return '';
			}
			$remote_media_types = um_gallery_get_remote_media_types();
			if ( in_array( $media->type, $remote_media_types ) ) {
				return $media->file_name;
			}

			$image = $this->gallery_url_path . '/' . absint( $media->user_id ) . '/' . esc_attr( $media->file_name );

			return $image;
		}
		/**
		 * Get user image path.
		 *
		 * @param  integer $user_id [description]
		 * @param  string  $name    [description]
		 * @param  string  $size    [description]
		 * @return [type]           [description]
		 */
		public function get_user_image_path( $user_id = 0, $name = '', $size = 'thumbnail' ) {
			if ( empty( $user_id ) || empty( $name ) ) {
				return;
			}

			$image = $this->gallery_path . '/' . $user_id . '/' . $name;
			if ( 'thumbnail' == $size ) {
				$filetype = wp_check_filetype( $image );
				$basename = basename( $image, '.' . $filetype['ext'] );
				return $this->gallery_path . '/' . $user_id . '/' . $basename . '-thumbnail.' . $filetype['ext'];
			}

			return $image;
		}
		/**
		 * Get images by by user ID
		 *
		 * @param  integer $user_id
		 * @return array
		 */
		public function get_images_by_user_id( $user_id = 0 ) {
			global $wpdb;
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}um_gallery WHERE user_id=%d", $user_id ) );
			return $results;
		}

		/**
		 * Magic getter for our object.
		 *
		 * @since  0.1.0
		 * @param string $field
		 * @throws Exception Throws an exception if the field is invalid.
		 * @return mixed
		 */
		public function __get( $field ) {
			switch ( $field ) {
				case 'version':
					return self::VERSION;
				case 'basename':
				case 'url':
				case 'path':
					return $this->$field;
				default:
					throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
			}
		}

		/**
		 * Include a file from the includes directory
		 *
		 * @since  1.0.6
		 * @param  string  $filename Name of the file to be included
		 * @return bool    Result of include call.
		 */
		public static function include_file( $filename ) {
			$file = self::dir( 'includes/' . $filename . '.php' );
			if ( file_exists( $file ) ) {
				return include_once( $file );
			}
			return false;
		}
		/**
		 * This plugin's directory
		 *
		 * @since  1.0.6
		 * @param  string $path (optional) appended path
		 * @return string       Directory and path
		 */
		public static function dir( $path = '' ) {
			static $dir;
			$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
			return $dir . $path;
		}
		/**
		 * This plugin's url
		 *
		 * @since  1.0.6
		 * @param  string $path (optional) appended path
		 * @return string       URL and path
		 */
		public static function url( $path = '' ) {
			static $url;
			$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
			return $url . $path;
		}
	}

if ( ! function_exists( 'um_gallery' ) ) {
		function um_gallery() {
			return UM_Gallery_Pro::instance();
		}
	}
	um_gallery();

endif;

/* Licensing */
if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include( um_gallery()->plugin_dir . '/classes/EDD_SL_Plugin_Updater.php' );
}

/**
 * Create Gallery Upload path and create necessary tables
 */
if ( ! function_exists( '_um_gallery_pro_activate' ) ) {

	function _um_gallery_pro_activate( $network_wide ) {
		global $wpdb;

		// Upload directory.
		$upload_dir = wp_upload_dir();
		$path = $upload_dir['basedir'] . '/um-gallery/';

		// Create directory.
		if ( ! file_exists( $path ) ) {
			wp_mkdir_p( $path );
		}

		// Check if multisite is
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ( $network_wide ) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					um_gallery_pro_db_setup();
				}
				switch_to_blog( $old_blog );
			} else {
				um_gallery_pro_db_setup();
			}
		} else {
			um_gallery_pro_db_setup();
		}
		$version = get_option( 'um_gallery_version' );

		update_option( 'um_gallery_version', '1.0.8.4.3' );
	}
}
register_activation_hook( __FILE__, '_um_gallery_pro_activate' );

add_action( 'wpmu_new_blog', 'um_gallery_pro_new_blog_setup', 10, 6 );

if ( ! function_exists( 'um_gallery_pro_new_blog_setup' ) ) {
	function um_gallery_pro_new_blog_setup( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $wpdb;

		if ( is_plugin_active_for_network( 'um-gallery-pro/um-gallery-pro.php' ) ) {
			$old_blog = $wpdb->blogid;
			switch_to_blog( $blog_id );
			um_gallery_pro_db_setup();
			switch_to_blog( $old_blog );
		}
	}
}
if ( ! function_exists( 'um_gallery_pro_db_setup' ) ) {
	function um_gallery_pro_db_setup() {
		global $wpdb;
		$charset_collate = ! empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
		$table_prefix = $wpdb->prefix;

		//check version and make edits to database
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$sql = "CREATE TABLE IF NOT EXISTS {$table_prefix}um_gallery (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `album_id` bigint(20) NOT NULL,
				  `user_id` bigint(20) NOT NULL,
				  `file_name` varchar(255) NOT NULL,
				  `caption` text NOT NULL,
				  `description` text NOT NULL,
				  `type` varchar(100) NOT NULL,
				  `status` tinyint(2) NOT NULL,
				  `upload_date` DATETIME NULL DEFAULT NULL
			) {$charset_collate};";

		dbDelta( $sql );
		$sql2 = "CREATE TABLE IF NOT EXISTS {$table_prefix}um_gallery_album (
				`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				 `user_id` bigint(20) NOT NULL,
				  `album_name` varchar(255) NOT NULL,
				  `album_caption` text NOT NULL,
				  `album_description` text NOT NULL,
				  `album_status` tinyint(2) NOT NULL,
				  `album_privacy` tinyint(2) NOT NULL,
				  `order` int(11) NOT NULL,
				  `creation_date` DATETIME NULL DEFAULT NULL
			) {$charset_collate};";

		dbDelta( $sql2 );
		$sql3 = "CREATE TABLE IF NOT EXISTS {$table_prefix}um_gallery_comments (
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

		dbDelta( $sql3 );

		/**
		 * Meta Key.
		 *
		 * @since 1.0.4.2
		 */
		$sql4 = "CREATE TABLE IF NOT EXISTS {$table_prefix}um_gallery_meta (
				`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`object_id` bigint(20) NOT NULL,
				`meta_key` varchar(255) NOT NULL,
				`meta_object` varchar(255) NOT NULL,
				`meta_value` text NOT NULL
			) {$charset_collate};";

		dbDelta( $sql4 );

		/**
		 * Favorite.
		 *
		 * @since 1.0.4.2
		 */
		$sql5 = "CREATE TABLE IF NOT EXISTS {$table_prefix}um_gallery_favorites (
				`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`photo_id` bigint(20) NOT NULL,
				`user_id` bigint(20) NOT NULL,
				`favorited_date` DATETIME NULL DEFAULT NULL
			) {$charset_collate};";

		dbDelta( $sql5 );
	}
} // End if().
