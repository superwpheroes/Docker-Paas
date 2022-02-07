<?php
global $photo;
$user_id = um_profile_id();
$data = array();
$users = array();
$user_id = um_profile_id();
?>
<div id="um-gallery-carousel" class="owl-carousel um-gallery-carousel"  data-gallery-id="<?php echo esc_attr( $uniqid ); ?>">
  <?php
	if ( ! empty( $images ) ) :
	foreach ( $images as $item ) {
		$photo                  = um_gallery_setup_photo( $item );
		$data[ $photo->id ]     = $photo;
		$users                  = um_gallery_setup_user( $users, $photo );
		?>
		<div class="um-gallery-inner item um-gallery-item" id="um-photo-<?php echo esc_attr( um_gallery_get_id() ); ?>">
			<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>"  data-lightbox="example-set" data-title="" id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" class="um-gallery-open-photo" data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><img src="<?php um_gallery_the_image_url(); ?>" />
			</a>
			<?php if ( um_gallery()->is_owner() ) : ?>
			<div class="um-gallery-mask">
				<a href="#" class="um-gallery-delete-item" data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-trash"></i></a>
				<?php /*?><a href="#" class="um-manual-trigger"  data-parent=".um-edit-form" data-child=".um-btn-auto-width" data-id="<?php echo $image->id; ?>"><i class="um-faicon-pencil"></i></a><?php */?>
			</div>
			<?php endif; ?>
		</div>
<?php
	}
	endif;
	?>
</div>
<?php
	// Is main tab.
	$main_tab = UM()->options()->get( 'profile_menu_default_tab' );
	$active_nav = UM()->profile()->active_tab();
	$nav_string = $main_tab == $active_nav ? 'main_' : '';

	//Carousel Options from admin
	$autoplay = um_gallery_pro_get_option( 'um_gallery_' . $nav_string . 'seconds_count' );
	$autoplay = $autoplay ? $autoplay * 1000 : 'false';

	if ( 'off' == um_gallery_pro_get_option( 'um_gallery_' . $nav_string . 'autoplay' ) ) {
		$autoplay = 'false';
	}

	$carousel_item_count = um_gallery_pro_get_option( 'um_gallery_' . $nav_string . 'carousel_item_count' );
	$carousel_item_count = ( ! $carousel_item_count ) ? 10 : $carousel_item_count;

	$seconds_count = um_gallery_pro_get_option( 'um_gallery_' . $nav_string . 'seconds_count' );
	$pagination    = um_gallery_pro_get_option( 'um_gallery_' . $nav_string . 'pagination' );	
	$pagination    = ( 'on' == $pagination ) ? 'true' : 'false';

	$autoheight = um_gallery_pro_get_option( 'um_gallery_' . $nav_string . 'autoheight' );
	$autoheight = ( 'on' == $autoheight ) ? 'true' : 'false';
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#um-gallery-carousel').owlCarousel({
		items : <?php echo esc_attr( $carousel_item_count ); ?>,
		autoPlay: <?php echo esc_attr( $autoplay ); ?>,
		pagination :  <?php echo esc_attr( $pagination ); ?>,
		autoHeight : <?php echo esc_attr( $autoheight ); ?>
	});
});
</script>
<script type="text/javascript" id="um-gallery-data">
	window['um_gallery_images_<?php echo esc_attr( $uniqid ); ?>'] = <?php echo wp_kses_post( json_encode( $data ) ); ?>;
	window['um_gallery_users_<?php echo esc_attr( $uniqid ); ?>'] = <?php echo wp_kses_post( json_encode( $users ) ); ?>;
</script>
