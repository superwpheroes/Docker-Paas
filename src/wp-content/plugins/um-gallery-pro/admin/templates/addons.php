<?php
$addons = array(
	'activity' => array(
		'title'       => __( 'Activity Wall', 'um-gallery-pro' ),
		'description' => __( 'Enable the ability to add to post new albums to wall', 'um-gallery-pro' ),
		'status'      => true,
		'enabled'     => um_gallery_pro_addon_enabled( 'activity' ),
	),
	'category' => array(
		'title'       => __( 'Categories', 'um-gallery-pro' ),
		'description' => __( 'Enable the ability to organize media items by category', 'um-gallery-pro' ),
		'status'      => true,
		'enabled'     => um_gallery_pro_addon_enabled( 'category' ),
	),
	'comments' => array(
		'title'       => __( 'Comments', 'um-gallery-pro' ),
		'description' => __( 'Enable the ability to add Comment to gallery', 'um-gallery-pro' ),
		'status'      => true,
		'enabled'     => um_gallery_pro_addon_enabled( 'comments' ),
	),
	'ratings'  => array(
		'title'       => __( 'Ratings', 'um-gallery-pro' ),
		'description' => __( 'Enable the ability to rate photos', 'um-gallery-pro' ),
		'status'      => false,
		'enabled'     => um_gallery_pro_addon_enabled( 'ratings' ),
	),
	'privacy'  => array(
		'title'       => __( 'Privacy', 'um-gallery-pro' ),
		'description' => __( 'Enable the ability to set media privacy', 'um-gallery-pro' ),
		'status'      => true,
		'enabled'     => um_gallery_pro_addon_enabled( 'privacy' ),
	),
	'tags'     => array(
		'title'       => __( 'Tags', 'um-gallery-pro' ),
		'description' => __( 'Enable the ability to use tags on media items', 'um-gallery-pro' ),
		'status'      => true,
		'enabled'     => um_gallery_pro_addon_enabled( 'tags' ),
	),
	'videos'   => array(
		'title'       => __( 'Videos', 'um-gallery-pro' ),
		'description' => __( 'Enable the ability to add YouTube videos through the gallery', 'um-gallery-pro' ),
		'status'      => true,
		'enabled'     => um_gallery_pro_addon_enabled( 'videos' ),
	),
);
?>
<div class="um-gallery--addons-wrapper">
	<?php foreach ( $addons as $id => $data ) { ?>
	<form method="post" action="">
	<div class="um-gallery--addon-item postbox">
		<div class="inside">
			<h3><?php echo esc_html( $data['title'] ); ?></h3>
			<p><?php echo esc_html( $data['description'] ); ?></p>
			<?php if ( $data['status'] ) { ?>
			<?php if ( false == $data['enabled'] ) { ?>
			<input type="submit" class="button button-primary" value="<?php echo __( 'Enable', 'um-gallery-pro' ); ?>">
			<input type="hidden" name="addon_action" value="enable">
			<?php } else { ?>
			<input type="submit" class="button button-primary" value="<?php echo __( 'Disable', 'um-gallery-pro' ); ?>">
			<input type="hidden" name="addon_action" value="disable">
			<?php } ?>
			<?php } else { ?>
			<div class="um-gallery--addon-item-dev"><?php _e( 'To be developed', 'um-gallery-pro' ); ?></div>
			<?php } ?>
		</div>
	</div>
	<?php wp_nonce_field( 'um_verify_addon_admin', 'um_verify_addon_field' ); ?>
	<input type="hidden" name="addon_id" value="<?php echo esc_attr( $id ); ?>" />
	</form>
	<?php } ?>
</div>
