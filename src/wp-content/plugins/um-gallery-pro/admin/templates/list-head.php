<div class="wrap">
	<h2><?php _e( 'Albums', 'um-gallery' ); ?>
	<?php /*?><a href="" class="page-title-action">
	<?php _e('Add New Album', 'um-gallery-pro'); ?>
	</a><?php */?>
	</h2>
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<label for="user-selector-top" class="screen-reader-text">Select user</label>
			<select name="action" id="um-gallery-user-select">
				<option value=""><?php _e( '-Select user-', 'um-gallery-pro' ); ?></option>
			<?php
			$users = um_gallery_get_users();
			if ( ! empty( $users ) ) :
				foreach ( $users as $u => $user_id ) :
					um_fetch_user( $user_id );
			?>
			<option value="<?php echo $user_id; ?>"><?php echo um_user( 'display_name' ) ?></option>
			<?php um_reset_user(); endforeach; endif; ?>
			</select>
			<input type="submit" id="doaction" class="button action" value="Filter">
		</div>
		<br class="clear">
	</div>
