<?php
global $photo, $wpdb;
$user_id = um_profile_id();
$data    = array();
$users   = array();


$sortableInit = '';

if ( um_gallery()->is_owner() ){
		$sortableInit = 'data-is-sortable';
	}
?>

<?php

/* LF - Echo information about album - album title & album description */
$album_id = um_gallery_get_default_album( $user_id );
if ( ! $album_id && ! empty( $_GET['album_id'] ) ) {
	$album_id = (int) $_GET['album_id'];
}
$album = um_gallery_album_by_id( $album_id );
if($album){ ?>
	<div class="um-gallery-album-head">
		<h3 class="um-gallery-album-title"><?php echo stripcslashes($album->album_name); ?></h3>
		<?php if( ! empty( $album->album_description ) ): ?>
			<div class="um-gallery-album-description"><?php echo stripcslashes(nl2br( $album->album_description )); ?></div>
		<?php endif; ?>
	</div>

<?php
}

/* LF - Get userdata */
	$PhotographerName = um_user('display_name');
	$PhotographerName = str_replace(' ', '-', $PhotographerName); 

	/* Get avada option regarding lightbox theme */
	$avada_options = get_option('avada_theme_options');
	if($avada_options['lightbox_skin']){
		$lightbox_skin = $avada_options['lightbox_skin'];
	}
	else{
		$lightbox_skin ='light';
	}
	
?>


<div class="sortable-loading" style="display: none;">
	<div class="bubblingG">
		<span id="bubblingG_1">
		</span>
		<span id="bubblingG_2">
		</span>
		<span id="bubblingG_3">
		</span>
	</div>
</div>
<?php 

// echo '<pre style="Text-align:left">'.print_r($images, 1). '</pre>'; 

$get_images = $wpdb->get_results(
	$wpdb->prepare("
		SELECT *
		FROM {$wpdb->prefix}um_gallery
		WHERE user_id=%d
		ORDER BY `menu_order`
		ASC",
		$user_id
	)
);

// echo '<pre style="Text-align:left">'.print_r($get_images, 1).'</pre>';

// 	/* LF Sort Images by menu_order */
// 	function cmp($a, $b) {
// 		return strcmp($a->menu_order, $b->menu_order);
// 	}

// 	usort($images, "cmp");

// 	echo '<pre style="Text-align:left">'.print_r($images, 1).'</pre>';


?>

<div class="um-gallery-item-wrapper um-gallery-grid um-gallery-container" id="photographer-album" data-lightbox="<?php echo $lightbox_skin;?>" <?php echo $sortableInit; ?> data-per_page="<?php echo absint( $amount ); ?>" data-page="1" data-query_args="<?php echo esc_attr( json_encode( $args['query_args'] ) ); ?>" data-load-more="true">
<?php

	
	
if ( ! empty( $get_images ) ) :



	foreach ( $get_images as $item ) {
	// echo '<pre style="Text-align:left">'.print_r($item, 1). '</pre>';

			$photo                  = um_gallery_setup_photo( $item, true );
			$data[ $photo->id ]     = $photo;
			$users                  = um_gallery_setup_user( $users, $photo );
			$avatar                 = um_gallery_get_user_details('avatar', '', $users );

			/* LF */
			// $caption = isset($data[$photo->id]['caption']) ? $data[$photo->id]['caption'] : '';
		?>
		<div class="um-gallery-item um-gallery-col-1-4 ui-sortable-handle" id="um-photo-<?php echo esc_attr( um_gallery_get_id() ); ?>" data-ns-sort='{"image_id": <?php echo $item->id; ?>, "user_id": <?php echo $user_id;?>, "menu_order": <?php echo $item->menu_order;?>}'>
			<div class="um-gallery-inner">
				<a href="<?php echo esc_url( um_gallery_get_media_url() ); ?>"  data-ns-rel="iLightbox[gallery-1]"  id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" data-title=""  data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>">
					<img src="<?php um_gallery_the_image_url(); ?>" alt="<?php echo $PhotographerName;?>" height=""/>
				</a>
				<?php if ( um_gallery()->is_owner() ): ?>
					<!-- LF actions -->
					<div class="ns-gallery-actions">
						<?php //echo $item->menu_order ;?>
						<a href="#" class="ns_um-gallery-delete-item" data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-trash"></i></a>
						<a href="#" class="ns_um-gallery-caption-edit" data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-pencil"></i> <span>Edit Caption</span></a>
						<div class="ns-edit-wrapper">
							<input type="text" class="ns-caption" value="<?php //echo $caption;?>"/>
						</div>
					</div>
					<!-- <div class="um-gallery-mask"> -->
						<!-- <a href="#" class="um-gallery-delete-item" data-id="<?php //echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-trash"></i></a> -->
					<!-- </div> -->
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
endif;
?>
</div>
<script type="text/javascript" id="um-gallery-data">
	var um_gallery_images = <?php echo json_encode( $data ); ?>;
	var um_gallery_users  = <?php echo json_encode( $users ); ?>;
</script>

<?php 

?>
<h3>
	<!-- LF Add Edit Series-->
	<?php if ( um_is_user_himself() ) { ?>
	<a href="#" class="um-gallery-form um-gallery-btn add-edit-series" data-id="<?php echo (int) $album_id; ?>"><i class="um-faicon-plus"></i> <?php echo um_gallery_pro_get_option( 'um_gallery_add_photo_btn', __( 'Add Photo', 'um-gallery-pro' ) ); ?></a>
	<?php } ?>
</h3>