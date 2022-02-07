<?php
if ( ! class_exists( 'UM_Gallery_Pro_AJAX' ) ) :
	/**
	 * AJAX Class.
	 */
	class UM_Gallery_Pro_AJAX {
		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->hooks();
		}

		/**
		 * Initiate hooks.
		 *
		 * @since 1.0.0
		 */
		public function hooks() {
			add_action( 'wp_ajax_um_gallery_photo_details', array( $this, 'um_gallery_photo_details' ) );
			add_action( 'wp_ajax_um_gallery_admin_update_photo', array( $this, 'um_gallery_admin_update_photo' ) );
			
			add_action( 'init', array( $this, 'um_gallery_suggest_tabs' ) );
			//add_action( 'wp_ajax_um_gallery_suggest_tabs', array( $this, 'um_gallery_suggest_tabs' ) );
			//add_action( 'wp_ajax_nopriv_um_gallery_suggest_tabs', array( $this, 'um_gallery_suggest_tabs' ) );
			
			add_action( 'wp_ajax_um_gallery_album_update', array( $this, 'um_gallery_album_update' ) );
			add_action( 'wp_ajax_um_gallery_delete_album', array( $this, 'um_gallery_ajax_delete_album' ) );
			add_action( 'wp_ajax_um_gallery_get_album_form', array( $this, 'um_gallery_get_album_form' ) );
			add_action( 'wp_ajax_um_gallery_photo_update', array( $this, 'um_gallery_photo_update' ) );
			add_action( 'wp_ajax_um_gallery_get_album_item', array( $this, 'um_gallery_get_album_item' ) );
			add_action( 'wp_ajax_um_gallery_photo_upload', array( $this, 'um_gallery_photo_upload' ) );
			add_action( 'wp_ajax_um_gallery_add_videos', array( $this, 'um_gallery_add_videos' ) );
			add_action( 'wp_ajax_um_photo_info', array( $this, 'um_gallery_photo_info' ) );
			add_action( 'wp_ajax_nopriv_um_photo_info', array( $this, 'um_gallery_photo_info' ) );
			add_action( 'wp_ajax_sp_gallery_um_delete', array( $this, 'delete_item' ) );
			add_action( 'wp_ajax_um_gallery_fetch_remote_thumbnail', array( $this, 'um_gallery_fetch_remote_thumbnail' ) );

			add_action( 'wp_ajax_um_gallery_get_more_photos', array( $this, 'um_gallery_get_more_photos' ) );
			add_action( 'wp_ajax_nopriv_um_gallery_get_more_photos', array( $this, 'um_gallery_get_more_photos' ) );

			
		}

		public function um_gallery_get_more_photos() {
			unset( $_GET['action'] );

			$page = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 2;

			$vars = $_GET;

			$string = '';
			if ( ! empty( $vars ) ) {
				foreach ( $vars as $key => $value ) {
					$string.= $key . '="' . $value . '" ';
				}
			}

			$atts = shortcode_atts( array(
					'category'               => '',
					'exclude_category'       => '',
					'tags'                   => '',
					'exclude_tags'           => '',
					'user_id'                => '',
					'id'                     => '',
					'album_id'               => 0,
					'per_load'               => 12,
					'page'                   => 1,
					'auto_load'              => false,
					'layout'                 => 'masonry',
					'show_pagination_button' => false,
					'sort_by'                => 'recent',
				), $vars, 'um_gallery_photos' );
			extract($atts);
			
			ob_start();
			global $albums;
			
			if ( ! $user_id ) {
				$args = um_get_requested_user();
			}

			$query_args = array(
				'category' => $category,
				'amount'   => $per_load,
			);
			//$images = um_gallery_recent_photos( $args );
			$images = um_gallery_recent_photos( array(
					'category' => $category,
					'tags'     => $tags,
					'amount'   => $per_load,
					'page'     => $page,
					'user_id'  => $user_id,
					'id'       => $id,
					'album_id' => $album_id,
				)
			);

			$atts = array_filter( $atts );
			if ( isset( $atts['page'] ) ) {
				unset( $atts['page'] );
			}
			$data = array(
				'images'                 => $images,
				'user_id'                => $user_id,
				'amount'                 => $per_load,
				'auto_load'              => $auto_load,
				'show_pagination_button' => false,
				'query_args'             => $atts,
			);
			switch( $layout ) {
				case 'masonry':
					um_gallery()->template->load_template( 'um-gallery/content-masonry', $data );
				break;
				case 'grid':
					um_gallery()->template->load_template( 'um-gallery/content-grid', $data );
				break;
				default:
					um_gallery()->template->load_template( 'um-gallery/content-masonry', $data );
			}
			$html = ob_get_contents();
			ob_end_clean();
				
			global $photo;
			$user_id = um_profile_id();
			$data = array();
			$users = array();
			if ( ! empty( $images ) ) :
				foreach ( $images as $item ) {
					$photo                  = um_gallery_setup_photo( $item, true );
					$data[ $photo->id ]     = $photo;
					$users                  = um_gallery_setup_user( $users, $photo );
					$avatar                 = um_gallery_get_user_details('avatar', '', $users );
				}
			endif;
			$response = array(
				'html'   => $html,
				'images' => $data,
				'users'  => $users,
			);
			wp_send_json( $response );
			//echo do_shortcode( '[um_gallery_photos ' . $string . ']' );
			exit;
		}
		public function um_gallery_suggest_tabs() { 
			if ( isset( $_GET['action'] ) && 'um_gallery_suggest_tabs' == $_GET['action'] ) {
				global $wpdb;
				
				// get names of all taxonomy terms 
				$name = '%' . $wpdb->esc_like( stripslashes( $_GET['term'] ) ) . '%'; //escape for use in LIKE statement
				$sql = "SELECT term.term_id as id, term.name as post_title, term.slug as guid, tax.taxonomy FROM $wpdb->term_taxonomy tax 
						LEFT JOIN $wpdb->terms term ON term.term_id = tax.term_id WHERE 1 = 1 
						AND term.name LIKE %s
						AND tax.taxonomy = 'um_gallery_tag'
						ORDER BY tax.count DESC";

				$sql = $wpdb->prepare($sql, $name);
				$results = $wpdb->get_results( $sql );

				//$results = array();

				if ( count( $results ) > 0 ) {   //check if the result is empty
				//copy the titles to a simple array
					$titles = array();
					foreach( $results as $r ) {
						/*$titles[] =  array(
							'label' => $r->id,
							'value' => $r->post_title,
						);*/
						$titles[] = addslashes($r->post_title);
					}
					echo json_encode( $titles );
					exit;
				} else {
					//$message = "No results found";
					// echo json_encode($message); 
				}
				die();
			}
		}
		public function um_gallery_admin_update_photo() {
			$results = array();
			global $wpdb;
			
			if ( ! current_user_can('manage_options') ) {
				wp_send_json_error();
			}

			$photo_id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			if ( ! $photo_id ) {
				wp_send_json_error();
			}
			
			$wpdb->update(
				$wpdb->prefix . 'um_gallery',
				array(
					'caption'     => ! empty( $_POST['caption'] ) ? wp_kses_post( $_POST['caption'] ) : '',
					'description' => ! empty( $_POST['description'] ) ? wp_kses_post( $_POST['description'] ) : '',
				),
				array(
					'id' => $photo_id,
					),
				array(
					'%s',
					'%s',
				),
				array( '%d' )
			);

			if ( um_gallery_pro_addon_enabled( 'category' ) ) {
				// Set categories
				// An array of IDs of categories we want this post to have.
				$cat_ids = ! empty( $_POST['category'] ) ?  absint( $_POST['category'] ) : null;

				$term_taxonomy_ids = wp_set_object_terms( $photo_id, $cat_ids, um_gallery()->field->category );
			}

			if ( um_gallery_pro_addon_enabled( 'tags' ) ) {
				$tag_ids = ! empty( $_POST['tax_input']['um_gallery_tag'] ) ?  $_POST['tax_input']['um_gallery_tag'] : null;
				$term_taxonomy_ids = wp_set_object_terms( $photo_id, $tag_ids, 'um_gallery_tag' );
			}
			wp_send_json_success( $results );
		}
		public function um_gallery_photo_details() {
			$results = array();
			$photo_id = ! empty( $_GET['photo_id'] ) ? absint( $_GET['photo_id'] ) : 0;
			if ( ! $photo_id ) {
				wp_send_json_error();
			}
			
			// Get photo details
			$results = um_gallery_photo_by_id( $photo_id );
			
			$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids' );
			$results->category = wp_get_object_terms( $photo_id,  'um_gallery_category', $args );
			$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names' );
			$results->tags = wp_get_object_terms( $photo_id,  'um_gallery_tag', $args );

			wp_send_json_success( $results );
		}
		public function um_gallery_add_videos() {
			$results = array();
			$album_id = (int) $_POST['album_id'];
			$user_id = get_current_user_id();
			global $wpdb;
			if ( ! empty( $_POST['videos'] ) ) {
				foreach ( $_POST['videos'] as $k => $video_url ) {
					$video_type = um_gallery_get_video_type( $video_url );
					$wpdb->insert(
						$wpdb->prefix . 'um_gallery',
						array(
							'album_id' 		=> $album_id,
							'file_name' 	=> esc_url( $video_url ),
							'upload_date'	=> date( 'Y-m-d H:i:s' ),
							'type'			=> esc_attr( $video_type ),
							'user_id' 		=> $user_id,
							'status' 		=> 1,
						),
						array(
							'%d',
							'%s',
							'%s',
							'%s',
							'%d',
							'%d',
						)
					);
					$last_id = $wpdb->insert_id;
					if ( $wpdb->last_error ) {
						$results['error'] = $wpdb->last_error;
					} else {
						$images_var = array();
						$images = um_gallery_photos_by_album( $album_id );
						if ( ! empty( $images ) ) {
							foreach ( $images as $item ) {
								global $photo;
								$image = um_gallery_setup_photo( $item );
								$images_var[ $image->id ] = array(
									'id' 			=> $image->id,
									'user_id' 		=> $image->user_id,
									'caption' 		=> $image->caption,
									'type'          => $image->type,
									'description' 	=> esc_html( $image->description ),
								);
							}
						}
						$image_src = um_gallery()->get_user_image_src( $user_id, $video_url, 'none' );
						$thumb     = um_gallery()->get_user_image_src( $user_id, $video_url );
						$results   = array(
							'id'			 => $last_id,
							'user_id'		 => absint( $user_id ),
							'video_url'		 => esc_url( $video_url ),
							'video_type'	 => esc_attr( $video_type ),
							'album_id'		 => absint( $album_id ),
							'image_src'		 => $image_src,
							'thumb'			 => $thumb,
							'gallery_images' => $images_var,
						);
						do_action( 'um_gallery_photo_updated', $results );
					}
				} // End foreach().
			} // End if().
			wp_send_json_success( $results );
		}

		public function um_gallery_get_album_item() {
			global $album, $photo;
			$album_id = (int) $_GET['album_id'];
			$album    = um_gallery_album_by_id( $album_id );
			$album    = $photo = $album;
			?>
			<div class="um-gallery-grid-item" id="um-album-<?php echo absint( $album->id ); ?>">
			  <div class="um-gallery-inner">
				<div class="um-gallery-img"><a href="<?php  echo um_gallery_album_url(); ?>"><img src="<?php echo um_gallery_get_album_feature_media_url( $album->id ); ?>"></a>
				<?php if ( um_gallery()->is_owner() ) : ?>
				  <div class="um-gallery-action">
					<a href="#" class="um-gallery-form" data-id="<?php echo absint( $album->id ); ?>"><i class="um-faicon-pencil"></i></a>
					<a href="#" class="um-delete-album" data-id="<?php echo absint( $album->id ); ?>"><i class="um-faicon-trash"></i></a>
				  </div>
				<?php endif; ?>
				</div>
				<div class="um-gallery-info">
				  <div class="um-gallery-title"><a href="<?php  echo um_gallery_album_url(); ?>"><?php echo $album->album_name; ?></a></div>
				  <div class="um-gallery-meta"><span class="um-gallery-count"><?php echo um_gallery_photos_count_text(); ?></span></div>

				</div>
			  </div>
			</div>
			<?php
			exit;
		}
		/**
		 * Save Album with Photos
		 *
		 * @return [type] [description]
		 */
		function um_gallery_album_update() {
			$results = array();
			$album_id = 0;
			global $wpdb;
			$user_id           = get_current_user_id();
			$album_name        = ! empty( $_POST['album_name'] ) ? sanitize_text_field( $_POST['album_name'] ) : um_gallery_get_default_album_name( $user_id );
			$album_description = ! empty( $_POST['album_description'] ) ? wp_kses_post( $_POST['album_description'] ) : '';
			$privacy           = ! empty( $_POST['album_privacy'] ) ? sanitize_text_field( $_POST['album_privacy'] ) : 'public';
			$privacy_state     = um_gallery_privacy_states( $privacy );
			if ( empty( $_POST['id'] ) ) {
				$wpdb->insert(
					$wpdb->prefix . 'um_gallery_album',
					array(
						'album_name'        => $album_name,
						'album_description' => $album_description,
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
				$id = (int) $_POST['id'];
				$wpdb->update(
					$wpdb->prefix . 'um_gallery_album',
					array(
						'album_name'        => $album_name,
						'album_description' => $album_description,
						'album_privacy'     => $privacy_state,
					),
					array(
						'id' => $id,
						),
					array(
						'%s',
						'%s',
						'%d',
					),
					array( '%d' )
				);
				$album_id = $id;
				$results['new'] = false;
			} // End if().
			$results['id'] = $album_id;
			$results['user_id'] = $user_id;
			do_action( 'um_gallery_album_updated', $results );
			wp_send_json( $results );
		}

		/**
		 * [um_gallery_photo_upload description]
		 * @return [type] [description]
		 */
		function um_gallery_photo_upload() {
			$results = array();
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			if ( ! empty( $_FILES ) ) :
				$user_id = get_current_user_id();
				if ( is_user_logged_in() ) {
					$path = um_gallery()->gallery_path . '/' . $user_id . '/';
					if ( ! file_exists( $path ) ) {
						wp_mkdir_p( $path );
					}

					//$file = um_gallery_fix_image_orientation( $_FILES );
					$file       = $_FILES['file'];
					$album_id   = (int) $_POST['album_id'];
					$tmp_file   = $file['tmp_name'];
					$name       = sanitize_text_field( $file['name'] );
					$filename   = wp_unique_filename( $path, $name, false );
					$targetFile = $path . $filename;  //5

					if ( move_uploaded_file( $tmp_file, $targetFile ) ) {

						$image    = wp_get_image_editor( $targetFile );
						$filetype = wp_check_filetype( $targetFile );
						$basename = basename( $targetFile, '.' . $filetype['ext'] );
						if ( ! is_wp_error( $image ) ) {
							$image->resize( 150, 150, true );
							$image->save( $path . $basename . '-thumbnail.' . $filetype['ext'] );

							$image->resize( 500, 640, true );
							$image->save( $path . $basename . '-medium.' . $filetype['ext'] );
						}
						global $wpdb;
						$wpdb->insert(
							$wpdb->prefix . 'um_gallery',
							array(
								'album_id'    => $album_id,
								'file_name'   => $filename,
								'upload_date' => date( 'Y-m-d H:i:s' ),
								'user_id'     => $user_id,
								'status'      => 1,
							),
							array(
								'%d',
								'%s',
								'%s',
								'%d',
								'%d',
							)
						);
						$last_id = $wpdb->insert_id;
					}
				} // End if().
			endif;

			$images_var = array();
			$images = um_gallery_photos_by_album( $album_id );
			if ( ! empty( $images ) ) {
				foreach ( $images as $item ) {
					global $photo;
					$image = um_gallery_setup_photo( $item );
					$images_var[ $image->id ] = $image;
				}
			}
			$image_src = $images_var[ $last_id ]->full_url;
			$thumb     = $images_var[ $last_id ]->thumbnail_url;
			$results   = array(
				'id'			 => $last_id,
				'user_id'		 => $user_id,
				'album_id'		 => $album_id,
				'image_src'		 => $image_src,
				'thumb'			 => $thumb,
				'gallery_images' => $images_var,
			);
			do_action( 'um_gallery_photo_updated', $results );
			wp_send_json( $results );
		}

		/**
		 * Update photo update
		 *
		 * @return JSON success.
		 */
		function um_gallery_photo_update() {
			$results = array();
			global $wpdb;
			$id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			$parent_id = ! empty( $_POST['parent_id'] ) ? sanitize_text_field( $_POST['parent_id'] ) : '';
			$wpdb->update(
				$wpdb->prefix . 'um_gallery',
				array(
					'caption' => ! empty( $_POST['caption'] ) ? wp_kses_post( $_POST['caption'] ) : wp_kses_post( $_POST['default_caption'] ),
					'description' => ! empty( $_POST['description'] ) ? wp_kses_post( $_POST['description'] ) : '',
				),
				array(
					'id' => $id,
					),
				array(
					'%s',
					'%s',
				),
				array( '%d' )
			);

			if ( um_gallery_pro_addon_enabled( 'category' ) ) {
				// Set categories
				// An array of IDs of categories we want this post to have.
				$cat_ids = ! empty( $_POST['category'] ) ?  absint( $_POST['category'] ) : null;

				$term_taxonomy_ids = wp_set_object_terms( $id, $cat_ids, um_gallery()->field->category );
			}
			if ( um_gallery_pro_addon_enabled( 'tags' ) ) {
				$tag_ids = ! empty( $_POST['tax_input']['um_gallery_tag'] ) ?  $_POST['tax_input']['um_gallery_tag'] : null;
				$term_taxonomy_ids = wp_set_object_terms( $id, $tag_ids, 'um_gallery_tag' );
			}

			$album_id = $wpdb->get_var( $wpdb->prepare( "SELECT album_id FROM {$wpdb->prefix}um_gallery WHERE id=%d", $id ) );
			$atts = array(
				'album_id'  => $album_id,
				'layout'    => 'grid',
				'per_load'  => '-1',
			);
			$images   = um_gallery_photos_by_album( $album_id, '', $atts );
			if ( ! empty( $images ) ) {
				foreach ( $images as $item ) {
					global $photo;
					$photo                 = um_gallery_setup_photo( $item, true );
					$results[ $photo->id ] = $photo;
				}
			}
			
			do_action( 'um_gallery_photo_updated', $results );
			wp_send_json( $results );
		}

		/**
		 * [um_gallery_get_album_form description]
		 * @return [type] [description]
		 */
		function um_gallery_get_album_form() {
			global $album_id, $album, $parent_id;
			if ( isset( $_GET['album_id'] ) ) {
				$album_id = absint( $_GET['album_id'] );
			}
			$parent_id = ! empty( $_GET['parent_id'] ) ? sanitize_text_field( $_GET['parent_id'] ) : '';
			//get album data
			$album = um_gallery_album_by_id( $album_id );
			um_gallery()->template->load_template( 'um-gallery/manage/album-form' );
			exit;
		}
		/**
		 * [um_gallery_ajax_delete_album description]
		 * @return [type] [description]
		 */
		function um_gallery_ajax_delete_album() {
			$results = array();
			$album_id = absint( $_POST['id'] );
			um_gallery_delete_album( $album_id );
			wp_send_json( $results );
		}
		/**
		 * [um_gallery_photo_info description]
		 * @return [type] [description]
		 */
		function um_gallery_photo_info() {

		}

		/**
		 * Delete gallery item
		 *
		 * @since  1.0.0
		 *
		 * @return JSON
		 */
		public function delete_item() {
			$results  = array();
			$id       = absint( $_POST['id'] );
			global $wpdb;
			um_gallery_delete_photo( $id );
			$album_id = absint( $_POST['album_id'] );
			$images   = um_gallery_photos_by_album( $album_id );
			if ( ! empty( $images ) ) {
				foreach ( $images as $item ) {
					global $photo;
					$image = um_gallery_setup_photo( $item );
					$results[ $image->id ] = array(
						'user_id'     => $image->user_id,
						'caption'     => $image->caption,
						'description' => esc_html( $image->description ),
					);
				}
			}
			wp_send_json( $results );
		}

		public function um_gallery_fetch_remote_thumbnail() {
			$results  = array();
			$request = wp_remote_get( esc_url( $_GET['videoUrl'] ) );
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
				$results['thumbnail'] = $meta_og_img;
			}
			wp_send_json( $results );
		}
	}
endif;
