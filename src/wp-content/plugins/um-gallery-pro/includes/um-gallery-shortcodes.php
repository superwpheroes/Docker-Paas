<?php
class UM_Gallery_Shortcodes {
	/**
	 * [__construct description]
	 */
	public function __construct() {
		add_shortcode( 'um_gallery_albums', array( $this, 'um_gallery_albums' ) );
		add_shortcode( 'um_gallery_photos', array( $this, 'um_gallery_photos' ) );
		add_shortcode( 'um_gallery_recent_photos_grid', array( $this, 'um_gallery_recent_photos' ) );
		add_shortcode( 'um_gallery_wall_activity', array( $this, 'um_gallery_wall_activity' ) );
		add_shortcode( 'um_gallery_photo_count', array( $this, 'um_gallery_photo_count' ) );
		add_shortcode( 'um_gallery_album_count', array( $this, 'um_gallery_album_count' ) );
	}

	public function um_gallery_recent_photos( $atts = array() ) {
		ob_start();
		global $albums, $photo;
		global $images;
		extract(shortcode_atts(array(
			'user_id' => '',
			'id'      => '',
			'amount'  => '10',
			'columns' => '2',
			'curved'  => false,
			), $atts)
		);

		if ( ! $user_id ) {
			$args = um_get_requested_user();
		}

		$uniqid = uniqid();
		//$images = um_gallery_recent_photos( $args );
		$images = um_gallery_recent_photos( array(
				'user_id' => um_get_requested_user(),
				'amount' => $amount,
			)
		);

		if ( 2 == $columns ) {
			$class = 'um--gallery-col-2';
		} elseif ( 3 == $columns ) {
			$class = 'um--gallery-col-3';
		} elseif ( 4 == $columns ) {
			$class = 'um--gallery-col-4';
		} else {
			$class = 'um--gallery-col-2';
		}
		$user_id = um_profile_id();
		$data = array();
		$users = array();
		$profile_id = um_get_requested_user();
		if( $profile_id ){
			um_fetch_user($profile_id);
			$users[$profile_id]  = array(
				'id'     => $profile_id,
				'name'   => um_user('display_name'),
				'link'   => um_user_profile_url(),
				'avatar' => um_user('profile_photo', 50 ),
			);
			um_reset_user();
		}

		if( ! empty( $images ) ) :
			?>
			<div class="um-gallery--recent-photos-wrapper <?php echo $curved ? 'um-gallery--recent-photos-curved' : ''; ?>"  data-gallery-id="<?php echo esc_attr( $uniqid ); ?>">
				<ul class="um-gallery--recent-photos">
			<?php
			foreach ( $images as $item ) {
				$photo                  = um_gallery_setup_photo( $item );
				$data[ $photo->id ]     = $photo;
				$users                  = um_gallery_setup_user( $users, $photo );
					?>
					<li class="<?php echo esc_attr( $class ); ?>">
						<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php  echo esc_attr( um_gallery_get_id() ); ?>" data-title=""  data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>">
							<span style="background-image: url('<?php um_gallery_the_image_url(); ?>');">
								<img src="<?php echo um_gallery()->url( 'assets/images/placeholder.jpg' ); ?>" />
							</span>
						</a>
					</li>
					<?php
			}
			?>
				</ul>
			</div>
			<script type="text/javascript" id="um-gallery-data">
				window['um_gallery_images<?php echo esc_attr( $uniqid ); ?>'] = <?php echo json_encode( $data ); ?>;
				window['um_gallery_users_<?php echo esc_attr( $uniqid ); ?>'] = <?php echo json_encode( $users ); ?>;
			</script>
		<?php
		endif;
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
	/**
	 * [um_gallery_albums description]
	 * @param  array  $atts [description]
	 * @return [type]       [description]
	 */
	public function um_gallery_albums( $atts = array() ) {
		ob_start();
		global $albums;
		global $wpdb;
		extract( shortcode_atts( array(
			'user_id'   => null,
			'username'  => null,
			'id'        => null,
			'amount'    => '10',
			), $atts )
		);
		$user_ids_from_name = array();
		$user_id_lists      = array();
		$sql_where          = array();
		$sql_where[]        = ' 1=1 ';

		// Check if user names were passsed in and get the user IDs.
		if ( ! empty( $username ) ) {
			$user_name_lists    = explode( ',', $username );
			foreach ( $user_name_lists as $uname ) {
				$user_obj = get_user_by( 'login', $uname );
				$user_ids_from_name[] = $user_obj->ID;
			}
		}

		// Chec if user ID was used.
		if ( ! empty( $user_id ) ) {
			$user_id_lists = explode( ',', $user_id );
			if ( ! empty( $user_ids_from_name ) ) {
				$user_id_lists = array_merge( $user_id_lists, $user_ids_from_name );
			}
		} elseif ( ! empty( $user_ids_from_name ) ) {
			$user_id_lists = $user_ids_from_name;
		}
		$user_id_lists = array_unique( $user_id_lists );
		if ( ! empty( $user_id_lists ) ) {
			if ( count( $user_id_lists ) > 1 ) {
				$sql_where[] = ' a.user_id IN (' . implode( ',', $user_id_lists ) . ')';
			} else {
				$sql_where[] = ' a.user_id = "' . $user_id_lists[0] . '" ';
			}
		}
		if ( ! empty( $id ) ) {
			$id_lists = explode( ',', $id );
			if ( count( $id_lists ) > 1 ) {
				$sql_where[] = ' a.id IN (' . implode( ',', $id_lists ) . ')';
			} else {
				$sql_where[] = ' a.id = "' . $id_lists[0] . '" ';
			}
		}
		$query = "SELECT a.*, d.file_name, d.type, COUNT(d.id) AS total_photos, (
			CASE 
				WHEN a.album_privacy IS NULL OR a.album_privacy = ''
				THEN 1
				ELSE a.album_privacy 
			END
		  ) AS privacy FROM {$wpdb->prefix}um_gallery_album AS a LEFT JOIN {$wpdb->prefix}um_gallery AS d ON a.id=d.album_id WHERE ".implode(' AND ', $sql_where)." GROUP BY a.id   ORDER BY a.id DESC LIMIT 0, {$amount}";
		$albums = $wpdb->get_results( $query );
		$albums = um_gallery_privacy_extractor( $albums );
		um_gallery()->template->load_template( 'um-gallery/albums' );
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	/**
	 * Shortcode handler for photow
	 *
	 * @since  1.0.7
	 *
	 * @return mixed
	 */
	public function um_gallery_photos( $atts = array() ) {
		$atts = shortcode_atts( array(
				'category'               => '',
				'exclude_category'       => '',
				'tags'                   => '',
				'exclude_tags'           => '',
				'user_id'                => '',
				'id'                     => '',
				'per_load'               => 12,
				'page'                   => 1,
				'auto_load'              => false,
				'layout'                 => 'masonry',
				'show_pagination_button' => false,
				'sort_by'                => 'recent',
			), $atts, 'um_gallery_photos' );
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
			'uniqid' => uniqid(),
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
		
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	public function um_gallery_wall_activity( $atts = array() ) {
		ob_start();
		global $albums;
		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'um_gallery_wall_activity'
		);
		um_gallery()->template->load_template( 'um-gallery/albums' );
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	public function um_gallery_photo_count( $attrs = array() ) {
		$atts = shortcode_atts( array(
			'user_id' => 0,
		), $atts, 'um_gallery_photos' );
		
		if ( $atts['user_id'] ) {
			$count = um_gallery_get_photos_count_by_user( $atts['user_id'] );
			return $count;
		}

		return 0;
	}

	public function um_gallery_album_count( $attrs = array() ) {
		$atts = shortcode_atts( array(
			'user_id' => 0,
		), $atts, 'um_gallery_photos' );
		
		if ( $atts['user_id'] ) {
			$count = um_gallery_get_album_count_by_user( $atts['user_id'] );
			return $count;
		}

		return 0;
	}
}
