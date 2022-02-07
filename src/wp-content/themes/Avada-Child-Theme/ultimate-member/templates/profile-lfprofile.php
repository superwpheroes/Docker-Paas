<?php /* Template: Profile LF */ ?>

<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?> um-role-<?php echo um_user('role'); ?> ">

	<div class="um-form">

		
		
		<?php if ( um_is_on_edit_profile() ) { 
			 

			?><form method="post" action="" id="um-profileform"><?php } 


			?>

		<?php do_action('um_profile_header_cover_area', $args ); ?>

		<?php

			//afficher la tab album ?
		$nav = UM()->profile()->active_tab();
		$nav = 'gallery';
		$subnav = ( get_query_var('subnav') ) ? get_query_var('subnav') : 'default';

		print "<div class='um-profile-body $nav $nav-$subnav'>";

				// Custom hook to display tabbed content
		do_action("um_profile_content_{$nav}", $args);


         
		print "</div>";

		?>


		<?php
		
		print "<div class='um-profile-footer' id='um-profilephoto'>";

		do_action('um_profile_header', $args ); 

		print "</div>";

		?>
		
		<?php if ( um_is_on_edit_profile() ) { ?></form><?php } ?>

	</div>

	<?php
	if ( um_is_on_edit_profile() ){
		if(is_user_logged_in()){
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;

			// if( function_exists('um_fetch_user') && function_exists('um_user') ){
				// Get current user info
	  			//Get current user role
	  			// $role = um_user('role');
	  			$user_role = UM()->roles()->get_um_user_role($user_id);

				switch($user_role){
					case 'um_entrant':
						$button_url = get_bloginfo('url').'/my-lf-entrant/';
						$found_role = true;
						break;				

					case 'um_member':
						$button_url = get_bloginfo('url').'/my-lf-member/';
						$found_role = true;
						break;

					case 'um_past':
						$button_url = get_bloginfo('url').'/my-lf-past/';
						$found_role = true;
						break;

					default:
					$button_url = '';
					$found_role = false;
				}

				echo '<div class="save-changes-holder"><a class="save-changes-photographer" data-href="'.$button_url.'">Save and Return to my LF</a></div>';


			// }

		}
	}
		

	?>
	
</div>
