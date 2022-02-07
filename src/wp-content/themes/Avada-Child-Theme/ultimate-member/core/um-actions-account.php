<?php

/** Test Tab 1 **/
add_filter('um_account_page_default_tabs_hook', 'my_custom_tab_in_um', 100 );
function my_custom_tab_in_um( $tabs ) {
    // echo '<pre>'.print_r($tabs,1).'</pre>';

    $tabs[100] = array();

    $tabs[110]['general-info']['icon'] = 'um-faicon-user';
    $tabs[110]['general-info']['title'] = 'Account';
    $tabs[110]['general-info']['custom'] = true;

    $tabs[150]['social']['icon'] = 'um-faicon-link';
    $tabs[150]['social']['title'] = 'Profile Social Links';
    $tabs[150]['social']['custom'] = true;
    return $tabs;
}


add_action('um_account_tab__general-info', 'um_account_tab__general_info');
function um_account_tab__general_info( $info ) {
    extract( $info );

    $output = UM()->account()->get_tab_output('general-info');
    if ( $output ) { echo $output; }
}

add_filter('um_account_content_hook_general-info', 'um_account_content_hook_general_info');
function um_account_content_hook_general_info( $output ){
    ob_start();

    $user = wp_get_current_user();
    // echo '<pre>'.print_r($user,1).'</pre>';
    $user_id = $user->ID;

    $t = get_user_meta($user_id);
    // echo '<pre>'.print_r($t,1).'</pre>';

    ?>



    <div class="um-account-heading uimob340-hide uimob500-hide"><i class="um-faicon-user"></i>General</div>

    <div class="um-field um-field-general_username um-field-text" data-key="general_username">
        <div class="um-field-label">
            <label for="general_username">Username</label>
            <div class="um-clear"></div>
        </div>
        <div class="um-field-area">
            <input autocomplete="off" class="um-form-field valid " type="text" name="general_username" id="general_username"
            value="<?php echo $user->user_login; ?>"  data-key="general_username" disabled="disabled" />
        </div>
    </div>

    <div class="um-field um-field-general_firstname um-field-text" data-key="general_firstname">
        <div class="um-field-label">
            <label for="general_firstname">First Name</label>
            <div class="um-clear"></div>
        </div>
        <div class="um-field-area">
            <input autocomplete="off" class="um-form-field valid " type="text" name="general_firstname" id="general_firstname"
            value="<?php echo get_user_meta($user_id,'first_name',true); ?>" data-key="general_firstname" />
        </div>
    </div>

    <div class="um-field um-field-general_lastname um-field-text" data-key="general_lastname">
        <div class="um-field-label">
            <label for="general_lastname">Last Name</label>
            <div class="um-clear"></div>
        </div>
        <div class="um-field-area">
            <input autocomplete="off" class="um-form-field valid " type="text" name="general_lastname" id="general_lastname"
            value="<?php echo get_user_meta($user_id,'last_name',true); ?>"  data-key="general_lastname" />
        </div>
    </div>

    <div class="um-field um-field-general_email um-field-text" data-key="general_email">
        <div class="um-field-label">
            <label for="general_email">Email Address</label>
            <div class="um-clear"></div>
        </div>
        <div class="um-field-area">
            <input autocomplete="off" class="um-form-field valid " type="text" name="general_email" id="general_email"
            value="<?php echo $user->user_email; ?>"  data-key="general_email" />
        </div>
    </div>

    <div class="um-col-alt um-col-alt-b">
        <div class="um-left">
            <a id="um_account_general_submit" value="" class="um-button" data-userid="<?php echo $user_id;?>" data-current-email="<?php echo $user->user_email;?>">Update Account</a>
        </div>
        <div class="um-clear"></div>
    </div>
    <?php wp_nonce_field( 'ns_update_user_general', 'ns_update_general_nonce_field' ); ?>
    <?php

    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}




/* make our new tab hookable */

add_action('um_account_tab__social', 'um_account_tab__social');
function um_account_tab__social( $info ) {
    extract( $info );

    $output = UM()->account()->get_tab_output('social');
    if ( $output ) { echo $output; }
}



/* Finally we add some content in the tab */

add_filter('um_account_content_hook_social', 'um_account_content_hook_social');
function um_account_content_hook_social( $output ){
    ob_start();

    $user = wp_get_current_user();
    $user_id = $user->ID;

    ?>



    <div class="um-account-heading uimob340-hide uimob500-hide"><i class="um-faicon-link"></i>Social Links</div>

    <div class="um-field um-field-social_facebook_username um-field-text" data-key="social_facebook_username">
        <div class="um-field-label">
            <label for="social_facebook_username">Facebook</label>
            <div class="um-clear"></div>
        </div>
        <div class="um-field-area">
            <input autocomplete="off" class="um-form-field valid " type="text" name="social_facebook_username" id="social_facebook_username"
                   value="<?php echo get_user_meta($user_id,'social_facebook_username',true); ?>" placeholder="Facebook username"  data-key="social_facebook_username" />
        </div>
    </div>

    <div class="um-field um-field-social_instagram_username um-field-text" data-key="social_instagram_username">
        <div class="um-field-label">
            <label for="social_instagram_username">Instagram</label>
            <div class="um-clear"></div>
        </div>
        <div class="um-field-area">
            <input autocomplete="off" class="um-form-field valid " type="text" name="social_instagram_username" id="social_instagram_username"
            value="<?php echo get_user_meta($user_id,'social_instagram_username',true); ?>" placeholder="Instagram username"  data-key="social_instagram_username" />
        </div>
    </div>

    <div class="um-field um-field-social_website um-field-text" data-key="social_website_custom">
        <div class="um-field-label">
            <label for="social_website_custom">Website URL</label>
            <div class="um-clear"></div>
        </div>
        <div class="um-field-area">
            <input autocomplete="off" class="um-form-field valid " type="text" name="social_website_custom" id="social_website_custom"
            value="<?php echo get_user_meta($user_id,'social_website_custom',true); ?>" placeholder="http://www.example.com"  data-key="social_website_custom" />
        </div>
    </div>

    <div class="um-col-alt um-col-alt-b">
        <div class="um-left">
            <a id="um_account_social_submit" value="" class="um-button" data-userid="<?php echo $user_id;?>">Update Account</a>
        </div>
        <div class="um-clear"></div>
    </div>
    <?php wp_nonce_field( 'ns_update_user_social', 'ns_update_social_nonce_field' ); ?>
    <?php

    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}


/**
 * Display account photo and username
 **/
add_action('init', 'remove_some_actions', 99);
function remove_some_actions(){
    remove_action('um_account_user_photo_hook', 'um_account_user_photo_hook');
}

add_action('um_account_user_photo_hook', 'ns_account_user_photo_hook');
function ns_account_user_photo_hook( $args ) {
    extract( $args );

    ?>

    <div class="um-account-meta radius-<?php echo um_get_option('profile_photocorner'); ?>">

        <div class="um-account-meta-img uimob800-hide"><a href="<?php echo um_user_profile_url(); ?>"><?php echo get_avatar( um_user('ID'), 120); ?></a></div>

        <?php if ( UM()->mobile()->isMobile() ) { ?>

        <div class="um-account-meta-img-b uimob800-show" title="<?php echo um_user('display_name'); ?>"><a href="<?php echo um_user_profile_url(); ?>"><?php echo get_avatar( um_user('ID'), 120); ?></a></div>

        <?php } else { ?>

        <div class="um-account-meta-img-b uimob800-show um-tip-w" title="<?php echo um_user('display_name'); ?>"><a href="<?php echo um_user_profile_url(); ?>"><?php echo get_avatar( um_user('ID'), 120); ?></a></div>

        <?php } ?>

        <div class="um-account-name uimob800-hide">
            <a href="<?php echo um_user_profile_url(); ?>"><?php echo um_user('display_name', 'html'); ?></a>
            <div class="um-account-profile-link"><a href="<?php echo um_user_profile_url(); ?>" class="um-link"><?php _e('Edit profile','ultimatemember'); ?></a></div>
        </div>

    </div>

    <?php

}
