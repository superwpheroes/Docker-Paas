<?php
global $photo;
$user_id = um_profile_id();
$data    = array();
$users   = array();
?>
<div class="um-gallery-item-wrapper um-gallery-grid um-gallery-container"  data-per_page="<?php echo absint( $amount ); ?>" data-page="1" data-query_args="<?php echo esc_attr( json_encode( $args['query_args'] ) ); ?>" data-load-more="<?php echo esc_attr( $auto_load ); ?>"  data-gallery-id="<?php echo esc_attr( $uniqid ); ?>">
<?php
if ( ! empty( $images ) ) :
	foreach ( $images as $item ) {
			$photo                  = um_gallery_setup_photo( $item, true );
			$data[ $photo->id ]     = $photo;
			$users                  = um_gallery_setup_user( $users, $photo );
			$avatar                 = um_gallery_get_user_details('avatar', '', $users );
		?>
		<div class="um-gallery-item um-gallery-col-1-4" id="um-photo-<?php echo esc_attr( um_gallery_get_id() ); ?>">
			<div class="um-gallery-inner">
				<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" data-title=""  data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><img src="<?php um_gallery_the_image_url(); ?>" />
				</a>
				<?php if ( um_gallery()->is_owner() ): ?>
				<div class="um-gallery-mask">
					<a href="#" class="um-gallery-delete-item" data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-trash"></i></a>
				</div>
				<?php endif; ?>
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
