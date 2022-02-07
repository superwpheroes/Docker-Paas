<?php
if ( empty( $images ) ) {
	return;
}
global $photo;
$user_id = um_profile_id();
$data = array();
$users = array();
?>
<style type="text/css">
	.page-load-status {
	  display: none; /* hidden by default */
	  padding-top: 20px;
	  border-top: 1px solid #DDD;
	  text-align: center;
	  color: #777;
	}
</style>
<div class="um-gallery-item-wrapper um-gallery-masonry um-gallery-container" data-per_page="<?php echo absint( $amount ); ?>" data-page="1" data-query_args="<?php echo esc_attr( json_encode( $args['query_args'] ) ); ?>" data-masonry="true" data-load-more="<?php echo esc_attr( $auto_load ); ?>" data-gallery-id="<?php echo esc_attr( $uniqid ); ?>">
<?php
if ( ! empty( $images ) ) :
	foreach ( $images as $item ) {
			$photo                  = um_gallery_setup_photo( $item, true );
			$data[ $photo->id ]     = $photo;
			$users                  = um_gallery_setup_user( $users, $photo );
			$avatar                 = um_gallery_get_user_details('avatar', '', $users );
		?>
		<div class="um-gallery-item" id="um-photo-<?php echo esc_attr( um_gallery_get_id() ); ?>">
			<div class="um-gallery-inner">
				<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" data-title=""  data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><img src="<?php um_gallery_the_image_url( um_gallery_get_id(), 'full' ); ?>" />
				</a>
				<div class="um-gallery-overlay">
					<div class="um-gallery-img-actions">
						<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" data-title=""  data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-expand" aria-hidden="true"></i></a>
					</div>
					<div class="um-gallery-img-info">
						<a target="_blank" href="<?php echo um_gallery_get_user_details('link', '', $users ); ?>"><img src="<?php  echo esc_url( $avatar['url'] )?>" alt="<?php  echo esc_attr( $avatar['alt'] )?>" class="<?php  echo esc_attr( $avatar['class'] )?>" /><?php echo um_gallery_get_user_details('name', '', $users ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
endif;
?>
</div>
<script type="text/javascript" id="um-gallery-data">
	window['um_gallery_images_<?php echo esc_attr( $uniqid ); ?>'] = <?php echo json_encode( $data ); ?>;
	window['um_gallery_users_<?php echo esc_attr( $uniqid ); ?>']  = <?php echo json_encode( $users ); ?>;
</script>
