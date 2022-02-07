<?php /* Template: Profile LF */ ?>


<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?> um-role-<?php echo um_user('role'); ?> ">

	<div class="um-form">
	

		<?php 
		// No Need 
		do_action('um_profile_before_header', $args ); ?>
		
		<?php if ( um_is_on_edit_profile() ) { ?><form method="post" action=""><?php } ?>
		
			
			<div class="um-cover <?php if ( um_profile('cover_photo') || ( $default_cover && $default_cover['url'] ) ) echo 'has-cover'; ?>" data-user_id="<?php echo um_profile_id(); ?>" data-ratio="<?php echo $args['cover_ratio']; ?>">

				<?php

					if ( $ultimatemember->fields->editing ) {

						$items = array(
									'<a href="#" class="um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width">'.__('Change cover photo','ultimatemember').'</a>',
									'<a href="#" class="um-reset-cover-photo" data-user_id="'.um_profile_id().'">'.__('Remove','ultimatemember').'</a>',
									'<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimatemember').'</a>',
						);

						echo $ultimatemember->menu->new_ui( 'bc', 'div.um-cover', 'click', $items );

					}
				?>

				<?php $ultimatemember->fields->add_hidden_field( 'cover_photo' ); ?>

				<div class="um-cover-e">
				<img src="http://life-framer.com/wp-content/uploads/2016/09/Black-line-1.png" width="6" height="58" style="width: 7px!important;
    position: absolute;
    left: 50%;
    top: 70%;height: 250px;" class="alignnone size-full wp-image-13904" />
				<div class="um-name-cover">

							<a style="color:#000;" href="<?php echo um_user_profile_url(); ?>" title="<?php echo um_user('display_name'); ?>"><?php echo um_user('display_name', 'html'); ?></a>

							<?php do_action('um_after_profile_name_inline', $args ); ?>

						</div>



					<?php if ( um_profile('cover_photo') ) { ?>

					<?php

					if( $ultimatemember->mobile->isMobile() ){
						if ( $ultimatemember->mobile->isTablet() ) {
							echo um_user('cover_photo', 1000);
						} else {
							echo um_user('cover_photo', 300);
						}
					} else {
						echo um_user('cover_photo', 1000);
					}

					?>

					<?php } elseif ( $default_cover && $default_cover['url'] ) {

						$default_cover = $default_cover['url'];

						echo '<img src="'. $default_cover . '" alt="" />';

					} else {

						if ( !isset( $ultimatemember->user->cannot_edit ) ) { ?>

						<a href="#" class="um-cover-add um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width"><span class="um-cover-add-i"><i class="um-icon-plus um-tip-n" title="<?php _e('Upload a cover photo','ultimatemember'); ?>"></i></span></a>

					<?php }

					} ?>
					<div class="um-profile-edit um-profile-headericon um-trigger-menu-on-click">

			<a href="#" class="um-profile-edit-a"><i class="um-faicon-cog"></i></a>

					
		<div class="um-dropdown" data-element="div.um-profile-edit" data-position="bc" data-trigger="click" style="top: 50px; width: 200px; left: -87px; right: auto; text-align: center;">
			<div class="um-dropdown-b">
				<div class="um-dropdown-arr" style="top: -17px; left: 87px; right: auto;"><i class="um-icon-arrow-up-b"></i></div>
				<ul>
										
					<li><a href="http://life-framer.com/u/amaury.guillais/?profiletab=main&amp;um_action=edit" class="real_url">Edit Profile</a></li>
					
										
					<li><a href="http://life-framer.com/account/" class="real_url">My Account</a></li>
					
										
					<li><a href="http://life-framer.com/logout/" class="real_url">Logout</a></li>
					
										
					<li><a href="#" class="um-dropdown-hide">Cancel</a></li>
					
									</ul>
			</div>
		</div>
					
		
		</div>

				</div>

			</div>
			
			<?php
			
			//afficher la tab album ?
			$nav = $ultimatemember->profile->active_tab;
			$subnav = ( get_query_var('subnav') ) ? get_query_var('subnav') : 'default';
				
			print "<div class='um-profile-body $nav $nav-$subnav'>";
				
				// Custom hook to display tabbed content
				do_action("um_profile_content_{$nav}", $args);
				do_action("um_profile_content_{$nav}_{$subnav}", $args);
				
			print "</div>";
				
			?>

			<?php
				//all editable fields
			print "<div class='um-profile-footer'>";
				
				do_action('um_profile_header', $args ); 
				
			print "</div>";
				
			?>
		
		<?php if ( um_is_on_edit_profile() ) { ?></form><?php } ?>
	
	</div>
	
</div>