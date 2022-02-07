<?php
/**
 * Avada Studio
 *
 * @package Avada-Builder
 * @since 3.5
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * AWB Studio class.
 *
 * @since 3.5
 */
class AWB_Studio_Import {

	/**
	 * The one, true instance of this object.
	 *
	 * @static
	 * @access private
	 * @since 3.0
	 * @var object
	 */
	private static $instance;

	/**
	 * The studio data.
	 *
	 * @access public
	 * @var mixed
	 */
	public $data = null;

	/**
	 * URL to fetch from.
	 *
	 * @access private
	 * @var boolean
	 */
	private $studio_url = 'https://avada.studio';

	/**
	 * Class constructor.
	 *
	 * @since 3.0
	 * @access private
	 */
	private function __construct() {

		if ( ! class_exists( 'AWB_Studio' ) || ! AWB_Studio::is_studio_enabled() ) {
			return;
		}

		// Downloads and imports icons package.
		add_filter( 'awb_studio_post_imported', [ $this, 'import_icons_package' ] );

		// Import Studio Media from Builder (both live and backend).
		add_action( 'wp_ajax_awb_studio_import_media', [ $this, 'ajax_import_media' ] );
	}

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @static
	 * @access public
	 * @since 3.0
	 */
	public static function get_instance() {

		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null === self::$instance ) {
			self::$instance = new AWB_Studio_Import();
		}
		return self::$instance;
	}

	/**
	 * Get the data for ajax requests.
	 *
	 * @access public
	 * @since 3.0
	 * @return void
	 */
	public function get_ajax_data() {
		check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

		echo wp_json_encode( $this->get_data() );
		wp_die();
	}

	/**
	 * Fetches studio content from REST API endpoint.
	 * Used to import studio content directly into to the page content.
	 *
	 * @access public
	 * @since 3.5
	 * @return array
	 */
	public function get_studio_content() {
		$studio_data = AWB_Studio()->get_data();
		$layout_id   = (int) $_POST['fusion_layout_id']; // phpcs:ignore WordPress.Security
		$category    = isset( $_POST['category'] ) ? (string) esc_attr( $_POST['category'] ) : false; // phpcs:ignore WordPress.Security
		$layout_data = [
			'post_content' => '',
		];

		if ( $category ) {
			if ( ! isset( $studio_data[ $category ] ) ) {
				echo wp_json_encode( $layout_data );
				wp_die();
			}
			$layout = $studio_data[ $category ][ 'item-' . $layout_id ];
		} else {
			if ( ! isset( $studio_data['fusion_template'] ) ) {
				echo wp_json_encode( $layout_data );
				wp_die();
			}
			$layout = $studio_data['fusion_template'][ 'item-' . $layout_id ];
		}

		// No layout found.
		if ( ! is_array( $layout ) ) {
			return $layout_data;
		}

		// Fetch studio object data.
		$response = wp_remote_get( $this->studio_url . '/wp-json/wp/v2/' . $layout['post_type'] . '/' . $layout_id . '/' );

		// TODO: better error handling.
		if ( is_wp_error( $response ) ) {
			return $layout_data;
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		$post_meta = isset( $response_body['post_meta'] ) ? $response_body['post_meta'] : '';

		if ( $post_meta && isset( $post_meta['_fusion'] ) ) {
			$remove_keys = [ 'studio_replace_params', 'exclude_form_studio' ];

			// Remove internal studio options.
			foreach ( $remove_keys as $key ) {
				if ( isset( $post_meta['_fusion'][ $key ] ) ) {
					unset( $post_meta['_fusion'][ $key ] );
				}
			}

			if ( empty( $post_meta['_fusion'] ) ) {
				unset( $post_meta['_fusion'] );
			}
		}

		return [
			'post_id'      => absint( $_POST['post_id'] ), // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			'post_content' => isset( $response_body['content']['raw'] ) ? $response_body['content']['raw'] : '',
			'avada_media'  => $response_body['avada_media'],
			'custom_css'   => isset( $response_body['custom_css'] ) ? $response_body['custom_css'] : '',
			'post_meta'    => $post_meta,
		];
	}

	/**
	 * Imports needed studio post assets.
	 *
	 * @access public
	 * @since 3.5
	 * @param array $layout Holds content and import assets data.
	 * @return array
	 */
	public function process_studio_content( $layout ) {

		// Post content set.
		$post_content = $layout['post_content'];

		$layout_data  = [];
		$off_canvases = [];

		if ( ! isset( $layout['post_id'] ) ) {
			$layout['post_id'] = null;
		}

		// Check for other media to be imported.
		if ( isset( $layout['avada_media'] ) && ! empty( $layout['avada_media'] ) ) {

			// Import images if they are set.
			if ( isset( $layout['avada_media']['images'] ) && ! empty( $layout['avada_media']['images'] ) && current_user_can( 'upload_files' ) ) {
				foreach ( (array) $layout['avada_media']['images'] as $image_url => $replacements ) {
					$existing_image = $this->find_existing_media( $image_url );
					if ( $existing_image ) {
						$image_id = $existing_image;
					} else {

						// We don't already have it, need to load it.
						$image_id = media_sideload_image( $image_url, $layout['post_id'], null, 'id' ); // phpcs:ignore WordPress.Security

						if ( ! is_wp_error( $image_id ) ) {
							// Add flag to prevent duplicate imports.
							$this->add_media_meta( $image_id, $image_url );
						}
					}

					if ( ! is_wp_error( $image_id ) ) {
						foreach ( (array) $replacements as $param_name => $old_value ) {
							// Get ID if its mixed with size.
							$old_id    = (int) $old_value;
							$new_value = str_replace( $old_id, $image_id, $old_value );

							// Replace the old image ID with the new one.
							$post_content = str_replace( $param_name . '="' . $old_value . '"', $param_name . '="' . $new_value . '"', $post_content );
						}
						$new_url = wp_get_attachment_url( $image_id );
					} else {
						foreach ( (array) $replacements as $param_name => $old_value ) {

							// Replace the old image ID with the empty value.
							$post_content = str_replace( $param_name . '="' . $old_value . '"', $param_name . '=""', $post_content );
						}

						$new_url = '';
					}

					// Replace the URL as well.
					$post_content = str_replace( $image_url, $new_url, $post_content );
				}
			}

			// Import videos if they are set.
			if ( isset( $layout['avada_media']['videos'] ) && ! empty( $layout['avada_media']['videos'] ) && current_user_can( 'upload_files' ) ) {
				foreach ( $layout['avada_media']['videos'] as $video_url => $active ) {

					$new_video_url = $this->import_video( $video_url );

					// If import failed $new_video_url will be empty string.
					$post_content = str_replace( $video_url, $new_video_url, $post_content );
				}
			}

			// Import menus if they are set.
			if ( isset( $layout['avada_media']['menus'] ) && ! empty( $layout['avada_media']['menus'] ) && current_user_can( 'edit_theme_options' ) ) {
				foreach ( $layout['avada_media']['menus'] as $menu_slug => $active ) {
					if ( $active ) {
						$new_menu = $this->import_menu( $menu_slug );
					}
					// Can use new menu ID here is we want but slug is unchanged anyway.
				}
			}

			// Import forms if they are set.
			if ( isset( $layout['avada_media']['forms'] ) && ! empty( $layout['avada_media']['forms'] ) && current_user_can( 'edit_theme_options' ) ) {
				foreach ( $layout['avada_media']['forms'] as $form_post_id => $active ) {

					$post_details     = $this->import_post( $form_post_id, 'fusion_form' );
					$new_form_post_id = $post_details['post_id'];

					if ( $new_form_post_id ) {
						$post_content = str_replace( 'form_post_id="' . $form_post_id . '"', 'form_post_id="' . $new_form_post_id . '"', $post_content );
					}
				}
			}

			// Import referenced off canvases if set.
			if ( isset( $layout['avada_media']['off_canvases'] ) && ! empty( $layout['avada_media']['off_canvases'] ) && current_user_can( 'edit_theme_options' ) && class_exists( 'AWB_Off_Canvas' ) && false !== AWB_Off_Canvas::is_enabled() ) {
				foreach ( $layout['avada_media']['off_canvases'] as $off_canvas_id => $active ) {

					$post_details                   = $this->import_post( $off_canvas_id, 'awb_off_canvas' );
					$new_off_canvas_id              = $post_details['post_id'];
					$off_canvases[ $off_canvas_id ] = $new_off_canvas_id;

					// Update dynamic data references.
					if ( false !== strpos( $post_content, 'b2ZmX2NhbnZhc' ) && false !== strpos( $post_content, 'dynamic_params' ) ) {
						preg_match_all( '/(?<=dynamic_params=")(.*?)(?=\")/', $post_content, $matches );
						if ( ! empty( $matches ) ) {
							foreach ( (array) $matches[0] as $match ) {
								if ( false !== strpos( $match, 'b2ZmX2NhbnZhc' ) ) {
									$dynamic_params = json_decode( base64_decode( $match ), true );
									if ( is_array( $dynamic_params ) ) {
										foreach ( $dynamic_params as $id => $data ) {

											if ( isset( $data['off_canvas_id'] ) ) {
												$dynamic_params['link']['off_canvas_id'] = isset( $off_canvases[ $dynamic_params['link']['off_canvas_id'] ] ) ? $off_canvases[ $dynamic_params['link']['off_canvas_id'] ] : $dynamic_params['link']['off_canvas_id'];
												$update_contents                         = base64_encode( wp_json_encode( $dynamic_params ) );
											}
										}
										$post_content = str_replace( $match, $update_contents, $post_content );
									}
								}
							}
						}
					}
				}

				// Update menu references.
				$menus = $post_data = isset( $_POST['data']['postData']['avada_media']['menus'] ) ? wp_unslash( $_POST['data']['postData']['avada_media']['menus'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				foreach ( $menus as $menu_slug => $active ) {
					if ( $active ) {
						$this->update_menu_off_canvas_references( $menu_slug, $off_canvases );
					}
				}
			}

			// Import post cards if they are set.
			if ( isset( $layout['avada_media']['post_cards'] ) && ! empty( $layout['avada_media']['post_cards'] ) && current_user_can( 'edit_theme_options' ) ) {
				foreach ( $layout['avada_media']['post_cards'] as $post_card_post_id => $active ) {

					$post_details          = $this->import_post( $post_card_post_id, 'fusion_element' );
					$new_post_card_post_id = $post_details['post_id'];

					if ( $new_post_card_post_id ) {
						$post_content = str_replace( 'post_card="' . $post_card_post_id . '"', 'post_card="' . $new_post_card_post_id . '"', $post_content );
					}
				}
			}

			// Import icons if they are set.
			if ( isset( $layout['avada_media']['icons'] ) && ! empty( $layout['avada_media']['icons'] ) && current_user_can( 'upload_files' ) ) {
				foreach ( $layout['avada_media']['icons'] as $icons_post_id => $icons_css_prefix ) {
					$post_details = $this->import_post( $icons_post_id, 'fusion_icons' );

					if ( isset( $post_details['custom_icons'] ) ) {
						if ( ! isset( $layout_data['custom_icons'] ) ) {
							$layout_data['custom_icons'] = [];
						}

						$layout_data['custom_icons'][] = $post_details['custom_icons'];
					}
				}
			}
		}

		// Set content.
		$layout_data['post_content'] = apply_filters( 'content_edit_pre', $post_content, $layout['post_id'] );

		if ( isset( $layout['custom_css'] ) && strlen( $layout['custom_css'] ) ) {
			$layout_data['custom_css'] = $layout['custom_css'];
		}

		return $layout_data;
	}

	/**
	 * Find an media with the post meta.
	 *
	 * @access public
	 * @since 3.5
	 * @param string $media_url The media URL on studio server.
	 * @return mixed
	 */
	public function find_existing_media( $media_url ) {
		global $wpdb;

		return $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT `post_id` FROM `' . $wpdb->postmeta . '`
					WHERE `meta_key` = \'_avada_studio_media\'
						AND `meta_value` = %s
				;',
				md5( $media_url )
			)
		);
	}

	/**
	 * Add a meta flag to attachment.
	 *
	 * @access public
	 * @since 3.0
	 * @param int    $media_id The media ID in the database.
	 * @param string $media_url The media URL on studio server.
	 * @return mixed
	 */
	public function add_media_meta( $media_id, $media_url ) {
		if ( ! $media_id ) {
			return;
		}
		update_post_meta( $media_id, '_avada_studio_media', md5( $media_url ) );
	}

	/**
	 * Import a menu to compliment content.
	 *
	 * @access public
	 * @since 3.0
	 * @param string $menu_slug The menu slug to import.
	 * @return mixed
	 */
	public function import_menu( $menu_slug ) {
		$response = wp_remote_get( $this->studio_url . '/wp-json/studio/menu/' . $menu_slug );

		// Check for error.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data['nav_items'] ) ) {
			return false;
		}

		// Create a new menu.
		$menu_id = wp_create_nav_menu( $data['name'] );
		if ( is_wp_error( $menu_id ) ) {
			return false;
		}

		// Match old IDs to new, for hierarchy.
		$id_matcher   = [];
		$sidebar_data = false;

		foreach ( $data['nav_items'] as $nav_item ) {

			// Replace old ID with new for parent.
			if ( isset( $nav_item['post_meta']['menu-item-menu-item-parent'] ) && '' !== $nav_item['post_meta']['menu-item-menu-item-parent'] ) {
				$parent_id = $nav_item['post_meta']['menu-item-menu-item-parent'];
				if ( isset( $id_matcher[ $parent_id ] ) ) {
					$nav_item['post_meta']['menu-item-parent-id'] = $id_matcher[ $parent_id ];
				}
			}

			// Create menu item.
			$nav_item_id = wp_update_nav_menu_item( $menu_id, 0, $nav_item['post_meta'] );
			$old_item_id = (int) $nav_item['post']['ID'];

			if ( ! is_wp_error( $nav_item_id ) ) {

				// Match old to new ID.
				$id_matcher[ $old_item_id ] = $nav_item_id;

				// Update mega menu meta.
				if ( isset( $nav_item['post_meta']['menu-item-fusion-megamenu'] ) ) {
					update_post_meta( $nav_item_id, '_menu_item_fusion_megamenu', maybe_unserialize( $nav_item['post_meta']['menu-item-fusion-megamenu'] ) );
				}

				// Add meta so we know menu item was imported as studio content.
				update_post_meta( $nav_item_id, '_avada_studio_post', $old_item_id );

				// If we have sidebar data.
				if ( isset( $data['sidebars'] ) && ! empty( $data['sidebars'] ) ) {

					$existing_sidebars = get_option( 'sbg_sidebars', [] );
					$new_sidebars      = $data['sidebars'];
					$import_widgets    = false;
					foreach ( $new_sidebars as $sidebar_id => $sidebar_name ) {
						// New sidebar, add it in.
						if ( ! isset( $existing_sidebars[ $sidebar_id ] ) ) {
							$import_widgets                   = true;
							$existing_sidebars[ $sidebar_id ] = $sidebar_name;
							register_sidebar(
								[
									'name'          => $sidebar_name,
									'id'            => 'avada-custom-sidebar-' . $sidebar_id,
									'before_widget' => '<div id="%1$s" class="widget %2$s">',
									'after_widget'  => '</div>',
									'before_title'  => '<div class="heading"><h4 class="widget-title">',
									'after_title'   => '</h4></div>',
								]
							);
						}
					}

					if ( $import_widgets && function_exists( 'fusion_import_widget_data' ) ) {

						// Update custom option.
						update_option( 'sbg_sidebars', $existing_sidebars );

						// Import the widgets.
						fusion_import_widget_data( wp_json_encode( $data['widgets'] ) );
					}
				}
			}
		}

		return $menu_id;
	}

	/**
	 * Updates menu.
	 *
	 * @access public
	 * @since 3.6
	 * @param string $menu_slug    The menu slug to import.
	 * @param array  $off_canvases Referrenced off canvases in menu.
	 * @return void
	 */
	public function update_menu_off_canvas_references( $menu_slug, $off_canvases ) {

		// Get menu items.
		$nav_items = wp_get_nav_menu_items( $menu_slug );

		if ( is_array( $nav_items ) && ! empty( $nav_items ) ) {
			foreach ( $nav_items as $nav_item ) {
				$meta = maybe_unserialize( get_post_meta( $nav_item->ID, '_menu_item_fusion_megamenu', true ) );

				if ( isset( $meta['special_link'] ) && 'awb-off-canvas-menu-trigger' === $meta['special_link'] && ! empty( $meta['off_canvas_id'] ) && class_exists( 'AWB_Off_Canvas' ) && false !== AWB_Off_Canvas::is_enabled() ) {
					$meta['off_canvas_id'] = isset( $off_canvases[ $meta['off_canvas_id'] ] ) ? $off_canvases[ $meta['off_canvas_id'] ] : $meta['off_canvas_id'];
					update_post_meta( $nav_item->ID, '_menu_item_fusion_megamenu', $meta );
				}
			}
		}
	}

	/**
	 * Find a form with the post meta.
	 *
	 * @access public
	 * @since 3.5
	 * @param int $post_id The form post ID.
	 * @return mixed
	 */
	public function find_existing_post( $post_id ) {
		global $wpdb;

		return $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT `post_id` FROM `' . $wpdb->postmeta . '`
					WHERE `meta_key` = \'_avada_studio_post\'
						AND `meta_value` = %s
				;',
				$post_id
			)
		);
	}

	/**
	 * Import a form to compliment content.
	 *
	 * @access public
	 * @since 3.5
	 * @param int    $studio_post_id   The ID of the post on Studio Site.
	 * @param string $studio_post_type Post Type of the post on Studio Site.
	 * @param bool   $import_media     Should post media be imported with the content or not.
	 * @return mixed
	 */
	public function import_post( $studio_post_id = 0, $studio_post_type = '', $import_media = true ) {
		$existing_post_id = $this->find_existing_post( $studio_post_id );

		// Any additonal data that post might need.
		$data              = [];
		$post_was_imported = false;

		// Post is already imported.
		if ( $existing_post_id ) {
			$imported_post_id  = $existing_post_id;
			$post_was_imported = true;
		} else {

			// TODO: better error handling.
			if ( ! $studio_post_id ) {
				return [ 'post_id' => false ];
			}

			$response = wp_remote_get( $this->studio_url . '/wp-json/wp/v2/' . $studio_post_type . '/' . $studio_post_id . '/' );

			// TODO: better error handling.
			if ( is_wp_error( $response ) ) {
				return [ 'post_id' => false ];
			}

			$post_data = json_decode( wp_remote_retrieve_body( $response ), true );

			$post_title   = isset( $post_data['post_title'] ) ? $post_data['post_title'] : $post_data['title']['rendered'];
			$post_content = isset( $post_data['post_content'] ) ? $post_data['post_content'] : $post_data['content']['raw'];

			$imported_post_id = wp_insert_post(
				[
					'post_title'   => $post_title,
					'post_content' => $post_content,
					'post_type'    => $studio_post_type,
					'post_status'  => 'publish',
				]
			);

			// TODO: better error handling.
			if ( ! $imported_post_id || is_wp_error( $imported_post_id ) ) {
				return [ 'post_id' => false ];
			}

			if ( true === $import_media ) {
				$this->import_post_media( $imported_post_id, $post_content, $post_data['avada_media'] );
			}

			// Set post terms.
			if ( isset( $post_data['terms'] ) && is_array( $post_data['terms'] ) ) {
				foreach ( $post_data['terms'] as $term ) {
					wp_set_object_terms( $imported_post_id, $term['slug'], $term['taxonomy'] );
				}
			}

			// Custom CSS.
			if ( isset( $post_data['custom_css'] ) && strlen( $post_data['custom_css'] ) ) {
				update_post_meta( $imported_post_id, '_fusion_builder_custom_css', $post_data['custom_css'] );
			}

			// Set post meta.
			if ( isset( $post_data['post_meta']['_fusion'] ) && '' !== $post_data['post_meta']['_fusion'] ) {
				update_post_meta( $imported_post_id, '_fusion', $post_data['post_meta']['_fusion'] );
			}

			// Set font meta.
			if ( isset( $post_data['post_meta']['_fusion_google_fonts'] ) && '' !== $post_data['post_meta']['_fusion_google_fonts'] ) {
				update_post_meta( $imported_post_id, '_fusion_google_fonts', $post_data['post_meta']['_fusion_google_fonts'] );
			}

			// Icons speficic stuff.
			if ( 'fusion_icons' === $studio_post_type ) {
				$data['package_url'] = $post_data['avada_media']['package_url'];
			}

			update_post_meta( $imported_post_id, '_avada_studio_post', $studio_post_id );
		}

		$post_details = [
			'post_id'      => $imported_post_id,
			'data'         => $data,
			'was_imported' => $post_was_imported,
			'avada_media'  => isset( $post_data ) && ! empty( $post_data['avada_media'] ) ? $post_data['avada_media'] : [],
		];

		$post_details = apply_filters( 'awb_studio_post_imported', $post_details );

		return $post_details;
	}

	/**
	 * Imports studio post's media and updates post content if needed.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $post_content Post Content.
	 * @param array  $avada_media Avada Media array.
	 * @return void
	 */
	public function import_post_media( $post_id, $post_content, $avada_media ) {

		// Check content and import necessary assets from studio site.
		$processed_post_data = $this->process_studio_content(
			[
				'post_id'      => $post_id,
				'post_content' => $post_content,
				'avada_media'  => $avada_media,
			]
		);

		// Update post content if it was changed.
		if ( $processed_post_data['post_content'] !== $post_content ) {
			wp_update_post(
				[
					'ID'           => $post_id,
					'post_content' => $processed_post_data['post_content'],
				]
			);
		}
	}

	/**
	 * Imports icons package.
	 *
	 * @access public
	 * @since 3.5
	 * @param array $post_details Post details array, returned from import_post function.
	 * @return mixed null|array
	 */
	public function import_icons_package( $post_details ) {

		// Post was already imported (and package processed) or something went wrong, either case zip package can't be imported.
		if ( ! $post_details['post_id'] || ! isset( $post_details['was_imported'] ) || true === $post_details['was_imported'] || ! isset( $post_details['data']['package_url'] ) ) {
			return $post_details;
		}

		// Fetch zip icon package and process it.
		$imported_post_id = $post_details['post_id'];

		// ZIP package URL.
		$package_url = $post_details['data']['package_url'];

		// Fetch icon package and add it to Media Library.
		$file_array         = [];
		$file_array['name'] = wp_basename( $package_url );

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $package_url );

		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return;
		}

		$attachment_data = [
			'post_title' => $file_array['name'],
		];

		$overrides['test_form'] = false;
		$file_data              = wp_handle_sideload( $file_array, $overrides );

		if ( ! isset( $file_data['file'] ) ) {
			return;
		}

		$attachment_id = wp_insert_attachment( $attachment_data, $file_data['file'], $imported_post_id );

		// Flag imported zip package as studio media.
		$this->add_media_meta( $attachment_id, $package_url );

		// Set necessary post meta if attachment ID is passed.
		$icon_set_meta = [
			'attachment_id' => $attachment_id,
		];

		fusion_data()->post_meta( $imported_post_id )->set( 'custom_icon_set', $icon_set_meta );

		// (Re)generate icon files.
		Fusion_Custom_Icon_Set::get_instance()->regenerate_icon_files( $imported_post_id );

		// WIP: begin.
		$meta = fusion_data()->post_meta( $post_details['post_id'] )->get( 'custom_icon_set' );

		if ( '' !== $meta ) {
			$post_details['custom_icons']              = $meta;
			$post_details['custom_icons']['name']      = get_the_title( $post_details['post_id'] );
			$post_details['custom_icons']['post_id']   = $post_details['post_id'];
			$post_details['custom_icons']['css_url']   = fusion_get_custom_icons_css_url( $post_details['post_id'] );
			$post_details['custom_icons']['post_name'] = get_post_field( 'post_name', $post_details['post_id'] );
		}
		// WIP: end.

		return $post_details;
	}

	/**
	 * Import a video to compliment content.
	 *
	 * @access public
	 * @since 3.5
	 * @param string $video_url The video URL.
	 * @return mixed
	 */
	public function import_video( $video_url ) {

		$existing_video = $this->find_existing_media( $video_url );

		if ( $existing_video ) {
			$new_video_url = $existing_video;
		} else {
			$new_video_url      = '';
			$file_array         = [];
			$file_array['name'] = wp_basename( $video_url );

			// Download file to temp location.
			$file_array['tmp_name'] = download_url( $video_url );

			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return;
			}

			if ( ! function_exists( 'media_handle_sideload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/media.php';

				// Needed for wp_read_image_metadata().
				require_once ABSPATH . 'wp-admin/includes/image.php';
			}

			$post_data = [
				'post_title' => $file_array['name'],
			];

			$attachment_id = media_handle_sideload( $file_array, 0, '', $post_data );

			if ( ! is_wp_error( $attachment_id ) ) {
				$new_video_url = wp_get_attachment_url( $attachment_id );
				$this->add_media_meta( $attachment_id, $new_video_url );
			}

			// Remove tmp file.
			if ( file_exists( $file_array['tmp_name'] ) ) {
				unlink( $file_array['tmp_name'] );
			}
		}

		return $new_video_url;
	}

	/**
	 * Ajax callback, used to import Studio media (for example from builder screen).
	 */
	public function ajax_import_media() {

		check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

		$post_data        = isset( $_POST['data']['postData'] ) ? wp_unslash( $_POST['data']['postData'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$media_import_key = isset( $_POST['data']['mediaImportKey'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['mediaImportKey'] ) ) : '';

		if ( $media_import_key ) {
			$layout = $this->process_studio_content(
				[
					'post_id'      => $post_data['post_id'],
					'post_content' => $post_data['post_content'],
					'avada_media'  => [ $media_import_key => $post_data['avada_media'][ $media_import_key ] ],
				]
			);

			$post_data['post_content'] = $layout['post_content'];
		}

		if ( isset( $layout['custom_icons'] ) ) {
			$post_data['custom_icons'] = $layout['custom_icons'];
		}

		echo wp_json_encode( $post_data );
		die();
	}
}

/**
 * Instantiates the AWB_Studio_Import class.
 * Make sure the class is properly set-up.
 *
 * @since object 3.0
 * @return object AWB_Studio_Import
 */
function AWB_Studio_Import() { // phpcs:ignore WordPress.NamingConventions
	return AWB_Studio_Import::get_instance();
}
AWB_Studio_Import();
