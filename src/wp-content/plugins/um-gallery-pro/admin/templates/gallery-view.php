<?php
if ( ! isset( $_GET['album_id'] ) ) {
	echo __( 'No album selected. Go back and try again', 'um-gallery-pro' );
	return;
}

$album_id = (int) $_GET['album_id'];
global $wpdb, $album;

$query    = "SELECT a.* FROM {$wpdb->prefix}um_gallery_album AS a WHERE a.id='{$album_id}' ORDER BY a.id DESC";
$album    = $wpdb->get_row( $query );
$action   = '';
$tax_name = um_gallery()->field->category;
?>
<div class="wrap">
<h1><?php _e( 'Edit Album', 'um-gallery-pro' ); ?></h1>
<form id="um-gallery-album-view" action="<?php echo esc_url( $action ); ?>" method="post">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-<?php echo ( 1 == get_current_screen()->get_columns() ) ? '1' : '2'; ?>">
			<div id="post-body-content">

				<?php

				// Output the name & description fields.
				$this->name_and_description();

				// Output the sumbit metabox.
				$this->add_photo_button();

				// Galler images be manipulated
				$this->gallery_items( $album_id );
				?>

			</div><!-- #post-body-content -->

			<div id="postbox-container-1" class="postbox-container">

				<?php



				// Output the field attributes metabox.
				$this->publishing_options();

				/**
				 * Fires after XProfile Field sidebar metabox.
				 *
				 * @since 2.2.0
				 *
				 * @param BP_XProfile_Field $this Current XProfile field.
				 */
				do_action( 'um_gallery_pro_album_after_sidebarbox', $this ); ?>

			</div>

			<div id="postbox-container-2" class="postbox-container">

				<?php

				/**
				 * Fires before XProfile Field content metabox.
				 *
				 * @since 2.3.0
				 *
				 * @param BP_XProfile_Field $this Current XProfile field.
				 */
				do_action( 'um_gallery_pro_album_before_contentbox', $this );



				// Output hidden inputs for default field.
				//$this->default_field_hidden_inputs();

				/**
				 * Fires after XProfile Field content metabox.
				 *
				 * @since 2.2.0
				 *
				 * @param BP_XProfile_Field $this Current XProfile field.
				 */
				do_action( 'um_gallery_pro_album_after_contentbox', $this ); ?>

			</div>
		</div><!-- #post-body -->
	</div><!-- #poststuff -->
	<input type="hidden" name="um_save_album_admin" value="1">
	<input type="hidden" name="album_id" value="<?php echo (int) $album_id; ?>">
	<?php wp_nonce_field( 'um_verify_album_admin', 'um_verify_album_admin_field' ); ?>
</form>
</div>
<div id="um-gallery-panel">
	<div class="inner">
		<div class="um-gallery-loading"><img src="<?php echo admin_url( 'images/loading.gif' ); ?>" /></div>
	</div>
</div>
<script id="gallery-view-edit-template" type="text/x-handlebars-template">
<form id="um-gallery-photo-form">
	<input type="hidden" name="id" value="{{id}}">
	<input type="hidden" name="action" value="um_gallery_admin_update_photo">
	{{#ifCond type '==' 'photo'}}
	<div class="um-gallery-image-preview"><img src="{{full_url}}" /></div>
	{{/ifCond}}
	<div class="um-gallery-form-control">
		<div class="um-gallery-form-label">
			<label for="caption"><?php esc_html_e( 'Caption', 'um-gallery-pro' ); ?></label>
		</div>
		<div class="um-gallery-form-field"><textarea name="caption">{{caption}}</textarea></div>
	</div>
	<?php if ( um_gallery_pro_addon_enabled( 'category' ) ) : ?>
	<div class="um-gallery-form-control">
		<div class="um-gallery-form-label">
			<label for="category"><?php esc_html_e( 'Category', 'um-gallery-pro' ); ?></label>
		</div>
		<div class="um-gallery-form-field">
				<?php wp_dropdown_categories( 'show_count=0&name=category&id=um-gallery-cat-picker&hierarchical=1&hide_empty=0&orderby=name&taxonomy=' . $tax_name ); ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if ( um_gallery_pro_addon_enabled( 'tags' ) ) : ?>
	<div class="um-gallery-form-control">
		<div class="um-gallery-form-label">
			<label for="tags"><?php esc_html_e( 'Tags', 'um-gallery-pro' ); ?></label>
		</div>
		<div class="um-gallery-form-field">
			<ul id="um_gallery_tag_list"></ul>
		</div>
	</div>
	<?php endif; ?>
	<div class="um-gallery-form-control">
		<div class="um-gallery-form-field"><input type="submit" class="button button-primary" name="update_item" value="<?php esc_attr_e( 'Update', 'um-gallery-pro' ); ?>"></div>
	</div>
</form>
</script>
