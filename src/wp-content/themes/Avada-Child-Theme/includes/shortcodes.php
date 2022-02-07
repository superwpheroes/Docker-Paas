<?php

/* New design for the new flow */

/* [fancy_title] $content [/fancy_title] */
function fancy_title_fc($atts = [], $content = null) {
    ob_start();
    ?> 

    <div class="section-fancy-title"><h4><?php echo $content;?></h4></div>

    <?php
    return ob_get_clean();
}

add_shortcode('fancy_title','fancy_title_fc');

/* [fancy_button href=""] $content [/fancy_button] */
function fancy_button_fc($atts = [], $content = null) {
    $atts = shortcode_atts( array(
        'href' => '',
    ), $atts);
    ob_start();
    ?> 

    <a class="fancy-button" href="<?php echo $atts['href'];?>"><?php echo $content;?></a>

    <?php
    return ob_get_clean();
}

add_shortcode('fancy_button','fancy_button_fc');


/* ========= My LF ========= */

/* [my_lf_button type=""] $content [/my_lf_button] */
function my_lf_button_fc($atts = [], $content = null) {
    $atts = shortcode_atts( array(
        'href'  => '',
        'text'  => '',
        'class' => '',
    ), $atts);
    ob_start();
    ?>

    <a class="my-lf-button <?php echo 'my-lf-'.strtolower($content);?> <?php echo $atts['class'];?>" href="<?php echo $atts['href'];?>">
        <span class="action-title"><?php echo $content;?></span>
        <span class="action-description"><?php echo $atts['text'];?></span>
            
        </a>

    <?php
    return ob_get_clean();
}

add_shortcode('my_lf_button','my_lf_button_fc');



/* My LF new designs  */

/* UM custom shortcode - get profile picture */
add_shortcode('lf_profile_picture','lf_profile_picture_fc');
function lf_profile_picture_fc(){
    ob_start();
    echo '<a class="mylf-um-user-img" href="'.um_edit_profile_url().'&change=profile_img#um-profilephoto">';
        echo '<img src="'.um_get_user_avatar_url().'">';
    echo '</a>';
    return ob_get_clean();
}


/* UM custom shortcode - Profile-> Display name */
add_shortcode('lf_profile_display_name','lf_profile_display_name_fc');
function lf_profile_display_name_fc(){
    ob_start();
    $user_id = um_user('ID');
    echo '<h4>'.um_get_display_name( $user_id ).'</h4>';
    return ob_get_clean();
}

/* UM custom shortcode - get edit profile url */
add_shortcode('lf_edit_profile_url','lf_edit_profile_url_fc');
function lf_edit_profile_url_fc( $atts ){
	ob_start();
	echo '<a href="'.get_bloginfo('url').'/account/general-info/" class="edit-account">'.$atts['title'].'</a>';
	return ob_get_clean();
}

/* Shortcode to display:
    - Images left to submit
    - Active themes and deadline
    - Submit / Buy more credit  button 
*/
add_shortcode('lf_themes_and_submit', 'lf_themes_and_submit_fc');
function lf_themes_and_submit_fc( $atts ){
    ob_start();
    $user_id = um_user('ID');
    $user_data = get_user_by('id', $user_id);

    /* Set correct timezone */
    date_default_timezone_set('America/Los_Angeles');
    /* For entrants credit can be used 4 months after the payment */
    $user_role = UM()->roles()->get_um_user_role($user_id);
    if( $user_role == 'um_entrant' ){
        $limit_date = date('Y-m-d H:i:s', strtotime(' -4 months'));
        $img_left = get_images_left_to_submit($user_data->user_email, $user_data->user_login, 'um_entrant', $limit_date);
        $extra_images = get_user_meta($user_id,'extra_entry_imgs',true);
        if(!isset($extra_images) || empty($extra_images)){
            $extra_images = 0;
        }
        $img_left += $extra_images;
        
        if($img_left>0){
            echo '<p><b>You have '. $img_left .' image'.($img_left=='1'?'':'s').' left to submit to the following competitions:</b></p>';
        }
        else{
            echo '<p><b>You don\'t have any image left to submit -</b> <a class="decor-underline" href="#enter-pricing-table">Get more credits or upgrade to membership now.</a></p>';
        }
    }
    elseif( $user_role == 'um_member' ){
        echo '<p><b>Submit to the following competitions:</b></p>';
    }
    elseif($user_role == 'um_past'){
        echo '<p><b>You don\'t have any image left to submit -</b> <a class="decor-underline" href="#enter-pricing-table">Get more credits or upgrade to membership now.</a></p>';
    }

    $lf_entries = get_active_LF_entries();

    if(!empty( $lf_entries )){
        echo '<ul class="lf-active-entries">';
        foreach($lf_entries as $lf_entry){
            echo '<li class="flex-elem"><span class="lf-square-icon"></span><b style="padding-right: 5px;">'. $lf_entry->name .'</b> - Deadline: ' .date('dS \of F  (H:i',strtotime($lf_entry->end_members)) .' PDT)</li>';
        }
        echo '</ul>';
    }

    $user_role = UM()->roles()->get_um_user_role($user_id);
    if( $user_role == 'um_entrant' ){
        $submit_url = get_bloginfo('url').'/submit-entrant';
    }
    elseif($user_role == 'um_member'){
        $submit_url =  get_bloginfo('url').'/submit-member';
    }
    else{
        $submit_url ='';
    }

    if( $user_role == 'um_entrant' ){
        if($img_left>0){
            echo '<a class="my-lf-button lf-btn-action" href="'. $submit_url.'"><span>SUBMIT YOUR IMAGE'.($img_left=='1'?'':'S').'</span></a>';
        }
        else{
            echo '<a href="#enter-pricing-table" class="my-lf-button lf-btn-action" href=""><span>PURCHASE MORE ENTRIES</span></a>';
        }
    }
    elseif( $user_role == 'um_member' ){
        echo '<a class="my-lf-button lf-btn-action" href="'. $submit_url.'"><span>SUBMIT YOUR IMAGES</span></a>';
    }
    elseif( $user_role == 'um_past' ){
        echo '<a href="#enter-pricing-table" class="my-lf-button lf-btn-action" href=""><span>PURCHASE MORE ENTRIES</span></a>';
    }
    return ob_get_clean();
}


/* Shortcode that outpust the submitted entries */
add_shortcode('lf_submitted_entries', 'lf_submitted_entries_fc');
function lf_submitted_entries_fc( $atts ){
    ob_start();

    if(isset($atts['limit']) && !empty($atts['limit']) ){
        $limit = ' LIMIT '.$atts['limit'];
    }
    else{
        $limit = ' ';
    }
    global $wpdb;
    $current_user = wp_get_current_user();
	$submitted_images = $wpdb->get_results("
        SELECT 
            lf_entry.time,
            lf_entry.theme_id,
            lf_entry.date_time,
            lf_photos.path,
            lf_photos.theme_entry_id
        FROM {$wpdb->prefix}lf_entry AS lf_entry
        INNER JOIN {$wpdb->prefix}lf_photos AS lf_photos
        ON lf_photos.entry_id =  lf_entry.id 
        WHERE 
            lf_entry.wp_user='$current_user->user_login' AND 
            lf_entry.date_time>0
        ORDER BY
            lf_entry.time DESC,
            lf_photos.theme_entry_id ASC,
            lf_photos.id ASC {$limit}
    ", ARRAY_A);

    echo '<p><b class="flex-elem flex-elem-middle">';    
    if(isset($atts['title']) && !empty($atts['title']) ){
        echo '<span class="lf-square-icon"></span>';
        echo '<span>'.$atts['title'].' – </span>';
    }
    if(isset($atts['btn-title']) && !empty($atts['btn-title']) ){
        $user_id = um_user('ID');
        $user_role = UM()->roles()->get_um_user_role($user_id);
        if( $user_role == 'um_entrant' ){
            $submit_url = get_bloginfo('url').'/submit-entrant#pastuploads';
        }
        elseif($user_role == 'um_member'){
            $submit_url =  get_bloginfo('url').'/submit-member#pastuploads';
        }
        else{
            $submit_url ='';
        }

        
        echo ' <a href="'.$submit_url.'" class="textred" style="padding-left: 5px;">'.$atts['btn-title'].'</a>';
    }
    echo '</b></p>';

    if($submitted_images){
        echo '<div class="lf-entries">';
        $upload_dir = wp_upload_dir();
		$upload_baseurl = $upload_dir['baseurl'];
		$upload_basedir = $upload_dir['basedir'];
        foreach($submitted_images as $key=> $result){
            $theme_entry_id = $result['theme_entry_id'];
            if(file_exists($upload_basedir.''.$result['path'])){
				$get_image = wp_get_image_editor( $upload_basedir.''.$result['path']);
				$square_img_class = '';
				if ( ! is_wp_error( $get_image ) ) {
					$get_image_size = $get_image->get_size();
					if($get_image_size['width'] == $get_image_size['height']){
						$square_img_class = 'square-img';
					}

				}
				echo '<div class="submittion-image">';
				echo '<div class="submittion-image-holder">';
				echo '<img src="'.$upload_baseurl.''.$result['path'].'" class="'.$square_img_class.'">';
				echo '</div><!--end submision image holder-->';
				echo '</div><!--end submision image-->';
			}
        }
        echo '</div>';
    }
    else{
        echo '<div class="empty-lf-entries">';
        echo '<p>You haven\'t submitted any images yet</p>';
        echo '</div>';
    }
    return ob_get_clean();
}


/* Shortcode that outputs Build your profile section 
    - Build your profile button with target to user profile url
    - Small description


*/
add_shortcode('lf_build_your_profile', 'lf_build_your_profile_fc');
function lf_build_your_profile_fc( $atts ){
    ob_start();
    // um_fetch_user( $user->ID );
    $profile_url = um_user_profile_url();
    $edit_url = um_edit_profile_url();
    echo '<a href="'.$edit_url.'" class="my-lf-button lf-btn-action bg-black"><span>'. $atts['btn-title'] .'</span></a>';

    echo '<p>'.$atts['below-btn'].'</p>';
    echo '<p class="flex-elem flex-elem-middle mb0"><span class="lf-square-icon"></span>Your public profile is:</p>';
    echo '<a href="'.$edit_url.'">'.str_replace('https://','',$profile_url).'</a>';

    return ob_get_clean();
}


add_shortcode('lf_member_btn', 'lf_member_btn_fc');
function lf_member_btn_fc( $atts ){
    ob_start();
    $user_id = um_user('ID');
    $user_role = UM()->roles()->get_um_user_role($user_id);
    if($user_role=='um_member'){
        echo '<a href="'.$atts['member-url'].'" class="bg-darkgrey lf-btn text-white"><span>'.$atts['member-txt'].'</span></a>';
    }
    else{
        echo '<a href="'.$atts['default-url'].'" class="bg-darkgrey lf-btn text-white"><span>'.$atts['default-txt'].'</span></a>';
    }
    return ob_get_clean();
}

add_filter('https_ssl_verify', '__return_false');

add_shortcode('lf_profile_preview', 'lf_profile_preview_fc');
function lf_profile_preview_fc(){
    ob_start(); ?>
        <div id="profile-iframe-container">
            <div id="profile-iframe-holder">
                <div id="profile-iframe" data-src="<?php echo um_user_profile_url();?>"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    <?php return ob_get_clean();
}