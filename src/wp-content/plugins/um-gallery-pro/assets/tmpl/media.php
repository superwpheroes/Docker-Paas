<div id="um-gallery-modal" class="um-gallery-popup" data-id="{{media_id}}" data-gallery-id="{{parent_id}}">
<div class="um-user-gallery-inner">
	<div class="um-user-gallery-left">
		<div class="um-user-gallery-arrow aqm-left-gallery-arrow">
			<a href="#" data-direction="left"><i class="um-faicon-angle-left" aria-hidden="true"></i></a>
		</div>
		<div class="um-user-gallery-arrow aqm-right-gallery-arrow">
			<a href="#" data-direction="right"><i class="um-faicon-angle-right" aria-hidden="true"></i></a>
		</div>
		<div class="um-user-gallery-image-wrap" {{#ifCond type '==' 'photo'}}style="background-image:url('{{image}}');"{{/ifCond}}{{#ifCond type '!==' 'photo'}}style="background-image:url('none');"{{/ifCond}}>
			<div class="image-holder">{{{media_frame}}}</div>
		</div>
		<?php if ( 'on' === um_gallery_pro_get_option( 'um_gallery_fullscreen' ) ) { ?>
		<div class="um-user-gallery-image-options-1"><a href="#" class="um-gallery-full-screener"><i class="um-faicon-expand"></i></a></div>
		<?php } ?>
		<?php if ( is_user_logged_in() ) { ?>
		{{#if is_owner}}
		<div class="um-user-gallery-image-options">
			<span class="um-user-gallery-options">
				<?php //if ( um_gallery()->is_owner() ) { ?> 
				<div class="um-user-gallery-normal"><a href="#" class="um-gallery-quick-edit">Edit Media</a> | <a href="#" class="aqm-delete-gallery-photo">Delete Photo</a>
				</div>
				<div class="um-user-gallery-edit" style="display: none;">
					'Are you sure want to delete this? <a href="#"  class="um-user-gallery-confirm" data-option="yes">Yes</a> | <a href="#" class="um-user-gallery-confirm" data-option="no">No</a>
				</div>
				<?php //} ?>
			</span>
		</div>
		{{/if}}
		<?php } ?>
	</div>
	<div class="um-user-gallery-right">
		<div class="um-user-gallery-right-inner">
			<div class="um-user-gallery-user">
				<div class="um-gallery-header-avatar">
					<a href="{{link}}">
						<img src="{{avatar.url}}" class="{{avatar.class}}" alt="{{avatar.alt}}" with="{{avatar.size}}" height={{avatar.size}}" />
					</a>
				</div>
				<div class="">
					<a href="{{link}}">{{avatar_name}}</a>
				</div>
			</div>
			<div class="um-user-gallery-info">
				<div class="um-user-gallery-caption">
					<div class="um-user-gallery-title" id="um-user-gallery-title">{{caption}}</div>
					<?php if ( um_gallery_pro_addon_enabled( 'category' ) ) { ?>
					<div class="um-user-gallery-meta-row" id="um-user-gallery-category">{{category}}</div>
					<?php } ?>
					<?php if ( um_gallery_pro_addon_enabled( 'tags' ) ) { ?>
					<div class="um-user-gallery-meta-row" id="um-user-gallery-tags">
						{{#each tags}}
						    <span class="um-gallery-tag">{{.}}</span>
						{{/each}}
					</div>
					<?php } ?>
				</div>
				<?php //if ( um_gallery()->is_owner() ) { ?> 
					<?php if ( is_user_logged_in() ) { ?>
					{{#if is_owner}}
					<div class="um-user-gallery-modify">
						<form id="um-gallery-photo-form">
							<input type="hidden" name="action" value="um_gallery_photo_update">
							<input type="hidden" name="id" value="{{media_id}}">
							<input type="hidden" name="parent_id" value="{{parent_id}}">
							<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'um-event-nonce' ); ?>">
							<div class="um-gallery-form-control">
								<div class="um-gallery-form-label">
									<label for="caption"><?php esc_html_e( 'Caption', 'um-gallery-pro' ); ?></label>
								</div>
								<div class="um-gallery-form-field"><textarea name="caption">{{caption}}</textarea></div>
							</div>
							<?php if ( um_gallery_pro_addon_enabled( 'category' ) ) { ?>
							<div class="um-gallery-form-control">
								<div class="um-gallery-form-label">
									<label for="category"><?php esc_html_e( 'Category', 'um-gallery-pro' ); ?></label>
								</div>
								<div class="um-gallery-form-field">
										<?php wp_dropdown_categories( 'show_count=0&name=category&id=um-gallery-cat-picker&hierarchical=1&hide_empty=0&orderby=name&taxonomy=' . um_gallery()->field->category ); ?>
								</div>
							</div>
							<?php } ?>
							<?php if ( um_gallery_pro_addon_enabled( 'tags' ) ) { ?>
							<div class="um-gallery-form-control">
								<div class="um-gallery-form-label">
									<label for="tags"><?php esc_html_e( 'Tags', 'um-gallery-pro' ); ?></label>
								</div>
								<div class="um-gallery-form-field">
									<ul id="um_gallery_tag_list">
										{{#each tags}}
										    <li>{{.}}</li>
										{{/each}}
									</ul>
								</div>
							</div>
							<?php } ?>
							 <div class="um-caption-text">
							 	<input type="submit" id="savePhoto" value="<?php esc_attr_e( 'Save', 'um-gallery-pro' ); ?>" />
							 	<input type="button" id="cancelPhoto" value="<?php esc_attr_e( 'Cancel', 'um-gallery-pro' ); ?>" />
							 </div>
						</form>
					 </div>
					 <div class="um-gallery-caption-edit-wrapper" data-id="{{media_id}}"><a href="#" id="um-gallery-caption-edit" data-id="{{media_id}}"><?php _e( '<i class="um-faicon-pencil"></i> Edit', 'um-gallery-pro' ); ?></a></div>
					 {{/if}}
					 <?php } ?>
				<?php //} ?>
			</div>
			<?php
			if ( um_gallery_pro_addon_enabled( 'comments' ) ) {
			?>
				<div id="um-gallery-comments"></div>
			<?php
			}
			?>
		</div>
	</div>
  </div>
</div>