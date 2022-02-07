<div class="um-gallery-album-list">
<?php
	global $albums;
	if ( ! empty( $albums ) ) :
		foreach( $albums as $item ) :
		global $album, $photo;
		$album = $photo = $item;
		?>
		<div class="um-gallery-grid-item" id="um-album-<?php echo absint( $album->id ); ?>">
		  <div class="um-gallery-inner">
				<div class="um-gallery-img"><a href="<?php echo um_gallery_album_url(); ?>"><img src="<?php echo um_gallery_get_album_feature_media_url( $album->id ); ?>"></a>
				<?php if ( um_gallery()->is_owner() ): ?>
					<div class="um-gallery-action">
						<a href="#" class="um-gallery-form" data-id="<?php echo $item->id; ?>"><i class="um-faicon-pencil"></i></a>
						<a href="#" class="um-delete-album" data-id="<?php echo $item->id; ?>"><i class="um-faicon-trash"></i></a>
					</div>
				<?php endif; ?>
				</div>
				<div class="um-gallery-info">
					<div class="um-gallery-title"><a href="<?php  echo esc_url( um_gallery_album_url() ); ?>"><?php echo stripslashes( $album->album_name ); ?></a></div>
					<div class="um-gallery-meta"><span class="um-gallery-count"><?php echo um_gallery_photos_count_text(); ?></span></div>
				</div>
			</div>
		</div>
	<?php
		endforeach;
	else:
	?>
	<div class="um-gallery-none">
		<?php esc_html_e( 'No albums found', 'um-gallery-pro' ); ?>
	</div>
	<?php
	endif;
	?>
</div>
