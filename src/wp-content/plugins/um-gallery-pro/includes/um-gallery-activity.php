<?php
class UM_Gallery_Activity {
	/**
	 * Initiate Constructor
	 */
	public function __construct() {
		$this->hooks();
	}
	/**
	 * Call Hooks
	 */
	public function hooks() {
		add_action( 'um_gallery_album_updated', array( $this, 'add_new_album' ), 12, 9999 );
		add_filter( 'um_activity_global_actions', array( $this, 'um_gallery_activity_action' ), 12, 1 );
		add_filter( 'um-activity-post-content', array( $this, 'setup_post_content' ), 12, 2 );
	}

	public function setup_post_content( $content, $post ) {
		global $images, $photo;
		$album_id = get_post_meta( $post->ID, '__um_gallery_activity_album', true );
		if ( ! $album_id ) {
			return $content;
		}
		$uniqid = uniqid();
		$activity_images = array();
		$images = um_gallery_recent_photos( array(
				'amount'   => 5,
				'album_id' => $album_id,
			)
		);
		ob_start();
		$user_id = um_profile_id();
		$data = array();
		$users = array();
		$profile_id = um_get_requested_user();
		if ( $profile_id ) {
			um_fetch_user( $profile_id );
			$users[ $profile_id ]  = array(
				'id'     => $profile_id,
				'name'   => um_user( 'display_name' ),
				'link'   => um_user_profile_url(),
				'avatar' => um_user( 'profile_photo', 50 ),
			);
			um_reset_user();
		}

		$image_count = count( $images );
		echo 'Image count is ' . $image_count;
		if ( ! empty( $images ) ) : ?>
		<?php
		foreach ( $images as $item ) {
			$photo                  = um_gallery_setup_photo( $item );
			$data[ $photo->id ]     = $photo;
			$users                  = um_gallery_setup_user( $users, $photo );
			$activity_images[] = array(
				'id' => um_gallery_get_id(),
				'url' =>  um_gallery_get_media_url(),
			);
		}
		?>
		<div data-gallery-id="<?php echo esc_attr( $uniqid ); ?>">
			<?php if ( 1 == $image_count ) : ?>
				<div class="im-post">
					<div class="im-pic-full">
						<a href="#" data-source-url="<?php echo esc_url( $activity_images[0]['url'] ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( $activity_images[0]['id'] ); ?>" data-title=""  data-id="<?php echo esc_attr( $activity_images[0]['id'] ); ?>">
							<div class="pic-bxs cxr" style="background-image: url(<?php echo esc_attr( $activity_images[0]['url'] ); ?>);">
								<img src="<?php echo um_gallery()->url( 'assets/images/placeholder.jpg' ); ?>" alt="" class="pic">
								<div class="bgr-ovly"></div>
							</div>
						</a>
					</div>
				</div>
				<script type="text/javascript" id="um-gallery-data">
					window['um_gallery_images_<?php echo esc_attr( $uniqid ); ?>'] = <?php echo json_encode( $data ); ?>;
					window['um_gallery_users_<?php echo esc_attr( $uniqid ); ?>']  = <?php echo json_encode( $users ); ?>;
				</script>
			<?php endif; ?>
			<?php if ( 2 == $image_count ) : ?>
				<div class="im-post">
					<div class="im-pic fst">
						<a href="#">
							<div class="pic-bxs cxr" style="background-image: url(images/p3.jpg);">
								<img src="images/p3.jpg" alt="" class="pic">
								<div class="bgr-ovly"></div>
							</div>
						</a>
					</div>
					<div class="im-pic">
						<a href="#">
							<div class="pic-bxs cxr" style="background-image: url(images/p1.jpg);">
								<img src="images/p1.jpg" alt="" class="pic">
								<div class="bgr-ovly"></div>
							</div>
						</a>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 3 == $image_count ) : ?>
				<div class="im-post">
					<div class="im-pic fst">
						<a href="#">
							<div class="pic-bxs cxr" style="background-image: url(images/p2.jpg);">
								<img src="images/p2.jpg" alt="" class="pic">
								<div class="bgr-ovly"></div>
							</div>
						</a>
					</div>
					<div class="im-pic">
						<a href="#">
							<div class="pic-sxs snd cxr" style="background-image: url(images/p1.jpg);">
								<img src="images/p1.jpg" alt="" class="pic">
								<div class="bgr-ovly"></div>
							</div>
						</a>
						<a href="#">
							<div class="pic-sxs cxr" style="background-image: url(images/p3.jpg);">
								<img src="images/p3.jpg" alt="" class="pic">
								<div class="bgr-ovly"></div>
							</div>
						</a>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 4 == $image_count ) : ?>
			<div class="im-post">
				<div class="im-pic fst">
					<a href="#">
						<div class="pic-sxs snd cxr" style="background-image: url(images/p3.jpg);">
							<img src="images/p3.jpg" alt="" class="pic">
							<div class="bgr-ovly"></div>
						</div>
					</a>
					<a href="#">
						<div class="pic-sxs cxr" style="background-image: url(images/p1.jpg);">
							<img src="images/p1.jpg" alt="" class="pic">
							<div class="bgr-ovly"></div>
						</div>
					</a>
				</div>
				<div class="im-pic">
					<a href="#">
						<div class="pic-sxs snd cxr" style="background-image: url(images/p1.jpg);">
							<img src="images/p1.jpg" alt="" class="pic">
							<div class="bgr-ovly"></div>
						</div>
					</a>
					<a href="#">
						<div class="pic-sxs cxr" style="background-image: url(images/p3.jpg);">
							<img src="images/p3.jpg" alt="" class="pic">
							<div class="bgr-ovly"></div>
						</div>
					</a>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( 4 < $image_count ) : ?>
			<div class="im-post">
				<div class="im-pic fst">
					<a href="#" data-source-url="<?php echo esc_url( $activity_images[0]['url'] ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( $activity_images[0]['id'] ); ?>" data-title=""  data-id="<?php echo esc_attr( $activity_images[0]['id'] ); ?>">
						<div class="pic-sxs snd cxr" style="background-image: url(<?php echo esc_attr( $activity_images[0]['url'] ); ?>);">
							<img src="<?php echo um_gallery()->url( 'assets/images/placeholder.jpg' ); ?>" alt="" class="pic">
							<div class="bgr-ovly"></div>
						</div>
					</a>
					<a href="#" data-source-url="<?php echo esc_url( $activity_images[1]['url'] ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( $activity_images[1]['id'] ); ?>" data-title=""  data-id="<?php echo esc_attr( $activity_images[1]['id'] ); ?>">
						<div class="pic-sxs cxr" style="background-image: url(<?php echo esc_attr( $activity_images[1]['url'] ); ?>);">
							<img src="<?php echo um_gallery()->url( 'assets/images/placeholder.jpg' ); ?>" alt="" class="pic">
							<div class="bgr-ovly"></div>
						</div>
					</a>
				</div>
				<div class="im-pic">
					<a href="#" data-source-url="<?php echo esc_url( $activity_images[2]['url'] ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( $activity_images[2]['id'] ); ?>" data-title=""  data-id="<?php echo esc_attr( $activity_images[2]['id'] ); ?>">
						<div class="pic-sxs snd cxr" style="background-image: url(<?php echo esc_attr( $activity_images[2]['url'] ); ?>);">
							<img src="<?php echo um_gallery()->url( 'assets/images/placeholder.jpg' ); ?>" alt="" class="pic">
							<div class="bgr-ovly"></div>
						</div>
					</a>
					<a href="#" data-source-url="<?php echo esc_url( $activity_images[3]['url'] ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( $activity_images[3]['id'] ); ?>" data-title=""  data-id="<?php echo esc_attr( $activity_images[3]['id'] ); ?>">
						<div class="pic-sxs cxr" style="background-image: url(<?php echo esc_attr( $activity_images[3]['url'] ); ?>);">
							<img src="<?php echo um_gallery()->url( 'assets/images/placeholder.jpg' ); ?>" alt="" class="pic">
							<div class="bgr-ovly"><h4>4+ more</h4></div>
						</div>
					</a>
				</div>
			</div>
			<script type="text/javascript" id="um-gallery-data">
					window['um_gallery_images_<?php echo esc_attr( $uniqid ); ?>'] = <?php echo json_encode( $data ); ?>;
					window['um_gallery_users_<?php echo esc_attr( $uniqid ); ?>']  = <?php echo json_encode( $users ); ?>;
			</script>
			<?php endif; ?>
			</div>
		<?php
		endif;
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	public function add_new_album( $results = array() ) {
		global $album;

		if ( ! um_gallery()->template->album_allowed ) {
			//return false;
		}
		if ( ! class_exists( 'UM_Activity_API' ) ) {
			return false;
		}

		if ( empty( $results['new'] ) ) {
			return false;
		}
		$album = um_gallery_album_by_id( $results['id'] );
		$user_id 		= $album->user_id;
		um_fetch_user( $user_id );
		$author_name 	= um_user( 'display_name' );
		$author_profile = um_user_profile_url();
		$user_photo 	= get_avatar( $user_id, 24 );

		$post_excerpt = '<span class="post-excerpt">' . $album->album_description . '</span>';

		$args = array(
			'related_id'    	=> $album->id,
			'template'      	=> 'um-gallery-album',
			'wall_id'       	=> $user_id,
			'author'        	=> $user_id,
			'author_name'   	=> $author_name,
			'author_profile'	=> $author_profile,
			'user_photo'    	=> $user_photo,
			'related_id'    	=> $user_id,
			'custom_path'   	=> um_gallery()->plugin_dir . 'templates/um-gallery/extra/activity-album.php',
			'post_title' 		=> '<span class="post-title">' . $album->album_name . '</span>',
			'post_url' 			=> um_gallery_album_url(),
			'post_excerpt' 		=> $post_excerpt,
		);
		$id = UM()->Activity_API()->api()->save(
			$args
		);
	}
	/**
	 * [um_gallery_activity_action description]
	 * @param  array
	 * @return array
	 */
	public function um_gallery_activity_action( $actions = array() ) {
		$actions['um-gallery-album'] = __( 'New Album Added', 'um-gallery-pro' );
		return $actions;
	}
}
