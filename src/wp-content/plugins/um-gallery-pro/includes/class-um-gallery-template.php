<?php
if ( ! class_exists( 'UM_Gallery_Pro_Template' ) ) :
	/**
	 *
	 */
	class UM_Gallery_Pro_Template {

		/**
		 * [$gallery description]
		 * @var [type]
		 */
		public $gallery;

		/**
		 * Default gallery tab slug
		 * @var string
		 */
		public $manage_gallery = 'manage_gallery';

		/**
		 * [$add_photos description]
		 * @var [type]
		 */
		public $add_photos;

		/**
		 * @var UM_Gallery_Pro The single instance of the class
		 */
		/**
		 * [__construct description]
		 */
		public function __construct() {
			$this->gallery          = $this->get_gallery_slug();
			$this->add_photos       = $this->get_add_photos_slug();
			$this->album_allowed    = um_gallery_allow_albums();
			$this->quick_upload     = um_gallery_allow_quick_upload();
			$this->hooks();
		}
		/**
		 * [get_gallery_slug description]
		 * @return [type] [description]
		 */
		public function get_gallery_slug() {
			$slug   = um_gallery_pro_get_option( 'um_gallery_tab_slug', 'gallery' );
			return apply_filters( 'sp_gallery_gallery_slug', $slug );
		}

		/**
		 * Get gallery slug
		 */
		public function get_add_photos_slug() {
			return apply_filters('sp_gallery_add_photos_slug', 'add_photos');
		}

		/**
		 * Class hooks
		 *
		 * @return void
		 */
		public function hooks() {
			add_filter( 'um_profile_tabs', array( $this, 'setup_gallery_tabs' ), 12, 1);
			add_action( 'um_profile_content_' . $this->gallery, array( $this, 'gallery_content_page' ) );
			//add_filter( 'um_predefined_fields_hook', array( $this, 'account_settings' ), 12, 1 );
			add_action( 'wp_footer', array( $this, 'add_render_tmpls' ) );
			add_action( 'init', array( $this, 'init_hooks' ) );
		}

		public function init_hooks() {
			if ( function_exists( 'UM' ) ) {
				add_action( 'um_profile_content_' . UM()->options()->get( 'profile_menu_default_tab' ), array( $this, 'gallery_profile_content_page' ), 20 );
			}
		}
		/**
		 * Set profile tab
		 *
		 * @param  array
		 * @return array
		 */
		public function setup_gallery_tabs( $tabs = array() ) {
			if ( is_admin() || um_gallery_can_moderate() ):
				$title  = um_gallery_pro_get_option( 'um_gallery_tab_name', __( 'Gallery', 'um-gallery-pro' ) );
				$icon   = um_gallery_pro_get_option( 'um_gallery_tab_icon', 'um-faicon-camera' );
				$tabs[$this->gallery] = array(
						'name'              => esc_html( $title ),
						'icon'              => esc_attr( $icon ),
						'custom'            => true,
						'subnav_default'    => 0
					);
			endif;
			return $tabs;
		}

		/**
		 * [gallery_profile_content_page description]
		 * @return [type] [description]
		 */
		public function gallery_profile_content_page() {
			global $images;
			$main_tab = UM()->options()->get( 'profile_menu_default_tab' );
			$active_nav = UM()->profile()->active_tab();
			$nav_string = $main_tab == $active_nav ? 'main_' : '';
			if ( 
				'on' === um_gallery_pro_get_option( 'um_gallery_profile' ) &&
				um_gallery_allowed_on_profile() && 
				$main_tab == $active_nav && 
				false == um_gallery_is_edit_mode()
			)
				{
				$amount = um_gallery_pro_get_option( 'um_gallery_main_max_carousel_item_count' );
				if ( ! $amount ) {
					$amount = 10;
				}
				$images = um_gallery_recent_photos(
					array(
						'user_id' => um_get_requested_user(),
						'amount' => $amount,
					)
				);

				$atts = array(
					'user_id'  => um_get_requested_user(),
					'per_load' => $amount,
				);
				$default_args = array(
					'category'               => '',
					'exclude_category'       => '',
					'tags'                   => '',
					'exclude_tags'           => '',
					'user_id'                => '',
					'id'                     => '',
					'per_load'               => 12,
					'page'                   => 1,
					'auto_load'              => false,
					'layout'                 => 'grid',
					'show_pagination_button' => false,
					'sort_by'                => 'recent',
				);

				$atts = wp_parse_args( $atts, $default_args );
				extract( $atts );

				$data = array(
					'images'                 => $images,
					'user_id'                => $user_id,
					'amount'                 => $per_load,
					'auto_load'              => $auto_load,
					'show_pagination_button' => false,
					'query_args'             => $atts,
					'uniqid'                 => uniqid(),
				);
				$layout = um_gallery_pro_get_option( 'um_main_gallery_type' );
				switch ( $layout ) {
					case 'carousel':
						um_gallery()->template->load_template( 'um-gallery/content-carousel', $data  );
						break;
					case 'grid':
						um_gallery()->template->load_template( 'um-gallery/content-grid', $data  );
						break;
					case 'slideshow':
						um_gallery()->template->load_template( 'um-gallery/content-slideshow', $data  );
						break;
					default:
						um_gallery()->template->load_template( 'um-gallery/content-grid', $data  );
						break;
				}
			}
		}

		/**
		 * [gallery_content_page description]
		 * @return [type] [description]
		 */
		public function gallery_content_page() {
			$user_id = um_profile_id();
			global $albums;
			$albums = um_gallery_by_userid( $user_id );
			if ( isset( $_GET['album_id'] ) ) {
				$this->get_profile_single_album_view();
			} else {
				if ( ! $this->album_allowed ):
					$this->get_profile_albums_view();
				else:
					$this->get_profile_photos_view();
				endif;
			}
		}

		public function get_profile_photos_view() {
			global $images;
			$user_id    = um_profile_id();
			
			$album_id   = um_gallery_get_default_album( $user_id );
			if ( ! $album_id && ! empty( $_GET['album_id'] ) ) {
				$album_id = (int) $_GET['album_id'];
			}
			$album      = um_gallery_album_by_id( $album_id );
			$data = array(
				'images' => $images,
				'album'  => $album,
			);

			$amount = um_gallery_pro_get_option( 'um_gallery_profile_count' );
			$atts = array(
				'user_id'  => $user_id,
				'per_load' => $amount,
			);
			$default_args = array(
				'category'               => '',
				'exclude_category'       => '',
				'tags'                   => '',
				'exclude_tags'           => '',
				'user_id'                => '',
				'id'                     => '',
				'per_load'               => 12,
				'page'                   => 1,
				'auto_load'              => false,
				'layout'                 => 'grid',
				'show_pagination_button' => false,
				'sort_by'                => 'recent',
			);

			$atts = wp_parse_args( $atts, $default_args );
			extract($atts);
			//$images     = get_images_by_user_id( $user_id );
			$images     = um_gallery_recent_photos( $atts );

			$data = array(
				'images'                 => $images,
				'album'                  => $album,
				'user_id'                => $user_id,
				'amount'                 => $per_load,
				'auto_load'              => $auto_load,
				'show_pagination_button' => false,
				'query_args'             => $atts,
				'uniqid'                 => uniqid(),
			);
			?>
			<h3>
				<?php if ( um_is_user_himself() ) { ?>
				<a href="#" class="um-gallery-form um-gallery-btn" data-id="<?php echo (int) $album_id; ?>" data-parent_id="<?php echo esc_attr( $data['uniqid'] ); ?>"><i class="um-faicon-plus"></i> <?php echo um_gallery_pro_get_option( 'um_gallery_add_photo_btn', __( 'Add Photo', 'um-gallery-pro' ) ); ?></a>
				<?php } ?>
			</h3>
			<?php if( ! um_gallery()->template->quick_upload ): ?>
			<div class="um-gallery-album-head">
				<h3 class="um-gallery-album-title"><?php echo stripslashes( $album->album_name ); ?></h3>
				<?php if( ! empty( $album->album_description ) ): ?>
				<div class="um-gallery-album-description"><?php echo esc_html( stripslashes( $album->album_description ) ); ?></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php
			switch( $this->gallery_tab_type() ) {
				case 'carousel':
				um_gallery()->template->load_template('um-gallery/content-carousel', $data );
				break;
				case 'grid':
				um_gallery()->template->load_template('um-gallery/content-grid', $data );
				break;
				case 'slideshow':
				um_gallery()->template->load_template('um-gallery/content-slideshow', $data );
				break;
				default:
				um_gallery()->template->load_template('um-gallery/content-grid', $data );
				break;
			}
		}
		/**
		 * Get the single album view
		 *
		 * @return void
		 */
		public function get_profile_single_album_view( $album_id = 0 ) {
			global $images;
			if ( ! $album_id ) {
				$album_id = isset( $_GET['album_id'] ) ? absint( $_GET['album_id'] ) : 0;
			}

			$atts = array(
				'album_id'  => $album_id,
				'layout'    => 'grid',
				'per_load'  => um_gallery_pro_get_option( 'um_gallery_profile_count', 12 ),
			);
			$default_args = array(
				'category'               => '',
				'exclude_category'       => '',
				'tags'                   => '',
				'exclude_tags'           => '',
				'user_id'                => '',
				'album_id'               => '',
				'id'                     => '',
				'per_load'               => 12,
				'page'                   => 1,
				'auto_load'              => false,
				'layout'                 => 'masonry',
				'show_pagination_button' => false,
				'sort_by'                => 'recent',
			);

			// Get photos for album.
			$images = um_gallery_photos_by_album( $album_id, '', $atts );
			$album  = um_gallery_album_by_id( $album_id );
			$data   = array(
				'images' => $images,
				'album'  => $album,
			);


			$atts = wp_parse_args( $atts, $default_args );
			extract( $atts );

			$atts = array_filter( $atts );
			if ( isset( $atts['page'] ) ) {
				unset( $atts['page'] );
			}
			$data = array(
				'images'                 => $images,
				'album'                  => $album,
				'user_id'                => $user_id,
				'amount'                 => $per_load,
				'auto_load'              => $auto_load,
				'show_pagination_button' => false,
				'query_args'             => $atts,
				'uniqid'                 => uniqid(),
			);
			?>

			<div class="um-gallery-album-back">
			<a href="<?php echo um_gallery_profile_url(); ?>" class="um-gallery-btn"><i class="um-faicon-chevron-left"></i> <?php _e('Back to Albums', 'um-gallery-pro'); ?>
			</a>
			<?php if ( um_gallery_is_owner() ) { ?>
				<a href="#" class="um-gallery-form um-gallery-btn um-gallery-right" data-id="<?php echo absint( $album_id ); ?>"  data-parent_id="<?php echo esc_attr( $data['uniqid'] ); ?>"><i class="um-faicon-pencil"></i> <?php _e('Manage Album', 'um-gallery-pro'); ?>
				</a>
			<?php } ?>
			</div>
			<?php if( ! um_gallery()->template->quick_upload ): ?>
			<div class="um-gallery-album-head">
				<h3 class="um-gallery-album-title"><?php echo esc_html( stripslashes( $album->album_name ) ); ?></h3>
				<?php if ( ! empty( $album->album_description ) ): ?>
				<div class="um-gallery-album-description"><?php echo esc_html( stripslashes( $album->album_description ) ); ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
			<?php

			switch( $this->gallery_tab_type() ) {
				case 'carousel':
				um_gallery()->template->load_template('um-gallery/content-carousel', $data );
				break;
				case 'grid':
				um_gallery()->template->load_template('um-gallery/content-grid', $data );
				break;
				case 'slideshow':
				um_gallery()->template->load_template('um-gallery/content-slideshow', $data );
				break;
				default:
				um_gallery()->template->load_template('um-gallery/content-grid', $data );
				break;
			}
		}
		public function get_profile_albums_view() {
			global $albums;
			?>
			<h3>
				<?php _e( 'Albums', 'um-gallery-pro' ); ?>
				<?php if ( um_gallery()->is_owner() ) { ?>
				<a href="#" class="um-gallery-form um-gallery-btn"><i class="um-faicon-folder"></i> <?php _e( 'Add Album', 'um-gallery-pro' ); ?></a>
				<?php } ?>
			</h3>
			<?php
			um_gallery()->template->load_template( 'um-gallery/albums' );
		}

		/**
		 * [load_template description]
		 * @param  [type] $tpl [description]
		 * @return [type]      [description]
		 */
		public function load_template( $tpl = '', $args = array() ) {
			if ( $args && is_array( $args ) ) {
				extract( $args );
			}

			$file =  UM_GALLERY_PATH . 'templates/' . $tpl . '.php';
			$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/' . $tpl . '.php';

			if ( file_exists( $theme_file ) ) {
				$file = $theme_file;
			}

			if ( file_exists( $file ) ) {
				include( $file );
			}
		}

		public function account_settings( $fields = array() ) {
			/*$res = array_slice($array, 0, 3, true) +
	array("my_key" => "my_value") +
	array_slice($array, 3, count($array) - 1, true) ;*/
			$fields['hide_gallery'] = array(
				'title' => __('Hide gallery','ultimatemember'),
				'metakey' => 'um_gallery_privacy',
				'type' => 'radio',
				'label' => __('Hide my profile from directory','ultimatemember'),
				'help' => __('Here you can hide yourself from appearing in public directory','ultimatemember'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'default' => __('No','ultimatemember'),
				'options' => array( __('No','ultimatemember'), __('Yes','ultimatemember') ),
				'account_only' => true,
				'required_opt' => array( 'members_page', 1 ),
			);
			return $fields;
		}

		public function gallery_tab_type() {
			$layout = um_gallery_pro_get_option( 'um_gallery_type', 'grid' );
			return $layout;
		}

		public function get_item_block_html() {
			ob_start();
			?>
			<div class="um-gallery-item um-gallery-col-1-4" id="um-photo-{{id}}">
				<div class="um-gallery-inner">
					<a href="{{media_url}}" data-source-url="{{media_url}}"  class="um-gallery-open-photo" id="um-gallery-item-{{id}}" data-title=""  data-id="{{id}}"><img src="{{media_image_url}}" />
					</a>
					<div class="um-gallery-mask">
						<a href="#" class="um-gallery-delete-item" data-id="{{id}}"><i class="um-faicon-trash"></i></a>
					</div>
				</div>
			</div>
			<?php
			$html = ob_get_contents();
			ob_end_clean();
			return apply_filters( 'um_gallery_item_render_layout', $html );
		}

		public function add_render_tmpls() {
			?>
			<script type="type="text/x-handlebars-template" id="um_gallery_item_block"><?php echo $this->get_item_block_html(); ?></script>
			<script type="type="text/x-handlebars-template" id="um_gallery_media"><?php include_once( UM_GALLERY_PATH . 'assets/tmpl/media.php' ); ?></script>
			<?php
		}
	}
endif;
