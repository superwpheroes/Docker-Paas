<?php
/**
 * UM custom actions profile
 *
 *
 */
/*
 * Add custom profile_header and profile_header_cover_area hooks
 * On action hook after WordPress have loaded everything, remove- and add new action hook for the Submit Button on UltimateMember Login form
 */

if( session_id() == '' ){ session_start(); }




require_once get_stylesheet_directory().'/ultimate-member/core/um-actions-account.php';






function lf_profile_header($args) {
    remove_action('um_profile_header', 'um_profile_header', 9);
    add_action('um_profile_header', 'my_um_profile_header', 9);
}
add_action('init', 'lf_profile_header');







function lf_profile_header_cover_area($args) {
    remove_action('um_profile_header_cover_area', 'um_profile_header_cover_area', 9);
    add_action('um_profile_header_cover_area', 'my_um_profile_header_cover_area', 9);
}
add_action('init', 'lf_profile_header_cover_area');







function lf_add_edit_icon($args) {

    remove_action('um_pre_header_editprofile', 'um_add_edit_icon');
    add_action('um_pre_header_editprofile', 'my_um_add_edit_icon');
}
add_action('init', 'lf_add_edit_icon');







/* * *
 * **   @um_add_edit_icon
 * * */

function my_um_add_edit_icon($args) {
    global $ultimatemember;
    $output = '';

    if (!is_user_logged_in())
        return; // not allowed for guests

    if (isset($ultimatemember->user->cannot_edit) && $ultimatemember->user->cannot_edit == 1)
        return; // do not proceed if user cannot edit

    if ($ultimatemember->fields->editing != true) {

        wp_redirect(um_edit_profile_url());
        exit;
        ?>

        <!--<div class="um-profile-headericon">
            <h3 data-fontsize="16" data-lineheight="24">
                <a href="--><?php /* echo um_edit_profile_url(); */?> <!--" class="um-profile-edit-a um-profile-btn" style="border-bottom:1px solid #f05858!important;">About you</a>
            </h3>
        </div>-->

        <?php
    }
}

/*     * *
 * **   @profile header cover
 * * */

function my_um_profile_header_cover_area($args) {
    global $ultimatemember;

    if ($args['cover_enabled'] == 1) {

        $default_cover = um_get_option('default_cover');

        $overlay = '<span class="um-cover-overlay">
                <span class="um-cover-overlay-s">
                    <ins>
                        <i class="um-faicon-picture-o"></i>
                        <span class="um-cover-overlay-t">' . __('Change your cover photo', 'ultimatemember') . '</span>
                    </ins>
                </span>
            </span>';
        ?>

        <div class="um-cover <?php if (um_profile('cover_photo') || ( $default_cover && $default_cover['url'] )) echo 'has-cover'; ?>" data-user_id="<?php echo um_profile_id(); ?>" data-ratio="<?php echo $args['cover_ratio']; ?>">

            <?php
            if ($ultimatemember->fields->editing) {

                $items = array(
                    '<a href="#" class="um-manual-trigger um-manual-coverphoto" data-parent=".um-cover" data-child=".um-btn-auto-width">' . __('Change cover photo', 'ultimatemember') . '</a>',
                    '<a href="#" class="um-reset-cover-photo" data-user_id="' . um_profile_id() . '">' . __('Remove', 'ultimatemember') . '</a>',
                    '<a href="#" class="um-dropdown-hide">' . __('Cancel', 'ultimatemember') . '</a>',
                );

                echo $ultimatemember->menu->new_ui('bc', 'div.um-cover', 'click', $items);
            }
            ?>

            <?php $ultimatemember->fields->add_hidden_field('cover_photo'); ?>

            <?php echo $overlay; ?>

            <div class="um-cover-e">

                <?php if (um_profile('cover_photo')) { ?>

                    <?php
                    if ($ultimatemember->mobile->isMobile()) {
                        if ($ultimatemember->mobile->isTablet()) {
                            echo um_user('cover_photo', 1000);
                        } else {
                            echo um_user('cover_photo', 300);
                        }
                    } else {
                        echo um_user('cover_photo', 1000);
                    }
                    ?>

                    <?php
                } elseif ($default_cover && $default_cover['url']) {

                    $default_cover = $default_cover['url'];

                    echo '<img src="' . $default_cover . '" alt="" />';
                } else {

                    if (!isset($ultimatemember->user->cannot_edit)) {
                        ?>

                        <a href="#" class="um-cover-add um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width"><span class="um-cover-add-i"><i class="um-icon-plus um-tip-n" title="<?php _e('Upload a cover photo', 'ultimatemember'); ?>"></i></span></a>

                    <?php }
                }
                ?>

            </div>
        </div>

        <div class="profile-header-cover">
            <div class="profile-header-cover__caption profile-header-cover__caption--big"><a style="color:#000;" href="<?php echo um_user_profile_url(); ?>" title="<?php echo um_user('display_name'); ?>"><?php echo um_user('display_name', 'html'); ?></a></div>
            <div class="profile-header-cover__line">
                <img src="<?php echo home_url();?>/wp-content/uploads/2016/09/Black-line-1.png" width="6" height="" class="alignnone size-full wp-image-13904"/>
            </div>
            <div class="profile-header-cover__caption profile-header-cover__caption--small profile-header-cover__caption--underline">Collection</div>

        </div>



        <?php
    }
}

/*         * *
 * **   @profile header
 * * */

function my_um_profile_header($args) {
    global $ultimatemember;

    $classes = null;

    if (!$args['cover_enabled']) {
        $classes .= ' no-cover';
    }

    $default_size = str_replace('px', '', $args['photosize']);

    $overlay = '<span class="um-profile-photo-overlay">
            <span class="um-profile-photo-overlay-s">
                <ins>
                    <i class="um-faicon-camera"></i>
                </ins>
            </span>
        </span>';
    ?>

    <?php do_action('um_pre_header_editprofile', $args); ?>

    <div class="profile-header">




        <div class="profile-header__photo um-profile-photo" data-user_id="<?php echo um_profile_id(); ?>">
            <img src="<?php echo home_url();?>/wp-content/uploads/2016/09/Black-line-2.png" alt="white-line-1" width="6" height="58" style="margin: auto!important; padding-top: 1rem;" class="alignnone size-full wp-image-13904" />

            <a class="um-profile-photo-img" title="<?php echo um_user('display_name'); ?>"><?php echo $overlay . get_avatar(um_user('ID'), $default_size); ?></a>

            <?php
            if (!isset($ultimatemember->user->cannot_edit)) {

                $ultimatemember->fields->add_hidden_field('profile_photo');

                if (!um_profile('profile_photo')) { // has profile photo
                    $items = array(
                        '<a href="#" class="um-manual-trigger" data-parent=".um-profile-photo" data-child=".um-btn-auto-width">' . __('Upload photo', 'ultimatemember') . '</a>',
                        '<a href="#" class="um-dropdown-hide">' . __('Cancel', 'ultimatemember') . '</a>',
                    );

                    $items = apply_filters('um_user_photo_menu_view', $items);

                    echo $ultimatemember->menu->new_ui('bc', 'div.um-profile-photo', 'click', $items);
                } else if ($ultimatemember->fields->editing == true) {

                    $items = array(
                        '<a href="#" class="um-manual-trigger" data-parent=".um-profile-photo" data-child=".um-btn-auto-width">' . __('Change photo', 'ultimatemember') . '</a>',
                        '<a href="#" class="um-reset-profile-photo" data-user_id="' . um_profile_id() . '" data-default_src="' . um_get_default_avatar_uri() . '">' . __('Remove photo', 'ultimatemember') . '</a>',
                        '<a href="#" class="um-dropdown-hide">' . __('Cancel', 'ultimatemember') . '</a>',
                    );

                    $items = apply_filters('um_user_photo_menu_edit', $items);

                    echo $ultimatemember->menu->new_ui('bc', 'div.um-profile-photo', 'click', $items);
                }
            }
            ?>

        </div>

        <div class="profile-header__meta um-profile-meta">

            <div class="um-main-meta">
                <div class="sub-artist">Photographer</div>

                <?php if ($args['show_name']) { ?>
                    <div class="um-name">

                        <?php echo um_user('display_name', 'html'); ?>

                        <?php do_action('um_after_profile_name_inline', $args); ?>
                    </div>
                <?php } ?>

                <div class="um-clear"></div>

                <?php do_action('um_after_profile_header_name_args', $args); ?>
                <?php do_action('um_after_profile_header_name'); ?>

            </div>

            <?php if (isset($args['metafields']) && !empty($args['metafields'])) { ?>
                <div class="um-meta aaa">

                    <?php
                    $profile_metas = $args['metafields'];

                    foreach($profile_metas as $meta_key) {
                        $meta_key = str_replace('social_', '', $meta_key);
                        $meta_value = get_user_meta(um_user('ID'), 'social_'.$meta_key, true);
                        if($meta_value !== ''){
                            $title = ucfirst(str_replace('social_', '', $meta_key));
                            if (strpos($meta_value,'http://') === false && strpos($meta_value,'https://') === false) {
                                $meta_value = 'http://'.$meta_value;
                            }
                            echo '<a href="'.$meta_value.'" title="'.$title.'" target="_blank" rel="nofollow" class="social-user-link">'. $title . '</a>';
                        }
                    }

                    // echo $ultimatemember->profile->show_meta($args['metafields']);
                    ?>
                </div>
            <?php } ?>

            <?php if ($ultimatemember->fields->viewing == true && um_user('description') && $args['show_bio']) { ?>

                <div class="um-meta-text" style="text-align: left;">
                    <?php
                    $description = get_user_meta(um_user('ID'), 'description', true);


                    if (um_get_option('profile_show_html_bio')) :

                        echo make_clickable(wpautop(wp_kses_post($description))); ?>

                    <?php else : ?>

                        <?php echo nl2br($description); ?>

                    <?php endif; ?>
                </div>

            <?php } else if ($ultimatemember->fields->editing == true && $args['show_bio']) { ?>

                <div class="um-meta-text">
                    <textarea id="um-meta-bio" data-user-id="<?php echo um_user('ID');?>" data-character-limit="<?php echo um_get_option('profile_bio_maxchars'); ?>" placeholder="<?php _e('Tell us a bit about yourself...', 'ultimatemember'); ?>" name="<?php echo 'description-' . $args['form_id']; ?>" id="<?php echo 'description-' . $args['form_id']; ?>"><?php if (um_user('description')) { echo get_user_meta(um_user('ID'),  'description', true );} ?></textarea>
                    <span class="um-meta-bio-character um-right"><span class="um-bio-limit"><?php echo um_get_option('profile_bio_maxchars'); ?></span></span>
                    <span data-ns-res style="font-size: 12px;"></span>
                    <?php
                    if ($ultimatemember->fields->is_error('description')) {
                        echo $ultimatemember->fields->field_error($ultimatemember->fields->show_error('description'), true);
                    }
                    ?>
                </div>

            <?php } ?>

            <div class="um-profile-status <?php echo um_user('account_status'); ?>">
                <span><?php printf(__('This user account status is %s', 'ultimatemember'), um_user('account_status_name')); ?></span>
            </div>

            <?php do_action('um_after_header_meta', um_user('ID'), $args); ?>

        </div>

        <div class="um-clear"></div>

        <?php
        if ($ultimatemember->fields->is_error('profile_photo')) {
            echo $ultimatemember->fields->field_error($ultimatemember->fields->show_error('profile_photo'), 'force_show');
        }
        ?>

        <?php do_action('um_after_header_info', um_user('ID'), $args); ?>

    </div>

    <?php
}








// * Load external scripts
function theme_gsap_script() {
    wp_enqueue_script('pubsub-js', '/wp-content/themes/Avada-Child-Theme/assets/js/pubsub.js', array(), false, true);
    wp_enqueue_script('sortable-js', '/wp-content/themes/Avada-Child-Theme/assets/js/jquery-ui.min.js', array(), false, true);
    wp_enqueue_script('lazysizes-js', '/wp-content/themes/Avada-Child-Theme/assets/js/lazysizes.js', array(), false, true);
    wp_enqueue_script('wow', '//cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js', array(), false, true);
    wp_enqueue_script('tweenlite', '//cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenLite.min.js', array(), false, true);
    wp_enqueue_script('cssplugin', '//cdnjs.cloudflare.com/ajax/libs/gsap/latest/plugins/CSSPlugin.min.js', array(), false, true);
    wp_enqueue_script('draggable', '//cdnjs.cloudflare.com/ajax/libs/gsap/latest/utils/Draggable.min.js', array(), false, true);
    // Buy plugin???
    // wp_enqueue_script('throw', 'http://www.jaybirdsport.com/static/js/greensock/plugins/ThrowPropsPlugin.min.js', array(), false, true);
    wp_enqueue_script('jquery.gallery', '/wp-content/themes/Avada-Child-Theme/assets/js/jquery.gallery.js', array(), false, true);
    /* load some custom js */
    wp_enqueue_script('custom-menu', '/wp-content/themes/Avada-Child-Theme/assets/js/custom-menu.js', array(), false, true);
    wp_enqueue_script('imghover', '/wp-content/themes/Avada-Child-Theme/assets/js/imghover.js', array(), false, true);

    wp_enqueue_script('custom-js', '/wp-content/themes/Avada-Child-Theme/assets/js/custom-js.js', array(), false, true);
    /* bootstrap fix
     * somehow the avada theme calls it twice
     * one min one not min
     */
    wp_deregister_script( 'bootstrap' );
    wp_dequeue_script( 'bootstrap' );

}
add_action('wp_enqueue_scripts', 'theme_gsap_script');







function theme_enqueue_styles() {
    wp_enqueue_style('avada-parent-stylesheet', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('animate-stylesheet', '//cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ,'avada-parent-stylesheet') );
    wp_enqueue_style('child-responsive-style', get_stylesheet_directory_uri() . '/assets/css/wph-responsive.css', array( 'child-style') );

}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');







function avada_lang_setup() {
    $lang = get_stylesheet_directory() . '/languages';
    load_child_theme_textdomain('Avada', $lang);
}
add_action('after_setup_theme', 'avada_lang_setup');






/*
 *  shortcode img heading href
 *  Hover a heading show image
 */
add_image_size('imghover', 550);
function img_hover_heading($atts, $content = null){
    $img_src = wp_get_attachment_image_src($atts['img'], 'medium');
    if($atts['url'] == 'x'){
        // default value replace with
        $atts['url'] = "javascript:;";
    }
    return '<a class="imghover" href="'.$atts['url'].'">'
    .'<span class="state">'.$atts['span'].'</span>'
    .'<span class="heading" style="background-image: url(\''.$img_src[0].'\')">'.$content.'</span>'
    . wp_get_attachment_image($atts['img'], 'imghover')
    .'</a>';
}
add_shortcode( 'imghover', 'img_hover_heading' );






function ignore_imghover($list){
    $list[] = 'imghover';
    return $list;
}
add_filter( 'no_texturize_shortcodes', 'ignore_imghover' );








//save "about you" section
add_action( 'wp_ajax_ns_update_about_you', 'ns_update_about_section' );
add_action( 'wp_ajax_nopriv_ns_update_about_you', 'ns_update_about_section' );
function ns_update_about_section(){

    $user_id = sanitize_text_field($_POST['user_id']);
    $description = $_POST['description'];

    update_user_meta($user_id, 'description', $description);

    echo 'Successfully updated!';

    die;
}







//update collection image positions
add_action( 'wp_ajax_ns_order_user_collection_images', 'ns_order_user_collection_images' );
add_action( 'wp_ajax_nopriv_ns_order_user_collection_images', 'ns_order_user_collection_images' );
function ns_order_user_collection_images(){

    global $wpdb;

    $items = $_POST['items'];


    if(is_array($items) && count($items) > 0){

        foreach($items as $item){

            $image_id   = $item['image_id'];
            $user_id    = $item['user_id'];
            $menu_order = $item['menu_order'];

            $update = $wpdb->update(
                $wpdb->prefix.'um_gallery',
                array(
                    'menu_order' => $menu_order,
                ),
                array(
                    'id' => $image_id,
                    'user_id' => $user_id,
                ),
                array('%d'),
                array('%d', '%d')
            );

            echo '<pre>'.print_r($update, 1).'</pre>';
        }
    }
    die;
}







//collection shortcode
add_shortcode('ns_ultimatemember', 'ns_ultimatemember');
function ns_ultimatemember($atts = array(), $content = ""){

    ob_start();

    include get_stylesheet_directory().'/includes/output-shortcode-ns-ultimatemember.php';

    $output = ob_get_clean();

    return $output;
}







add_action( 'wp_ajax_swph_get_members', 'swph_get_members' );
add_action( 'wp_ajax_nopriv_swph_get_members', 'swph_get_members' );
// get members
function swph_get_members($atts="0", $jatts = "0", $piece = "0") {
    global $ultimatemember;


    extract( shortcode_atts( array(
        'users' => null,
        'tag'   => null,
        'stringsearch' => null,
    ), $atts ) );

    $args = array(
        'meta_key' => 'account_status',
        'meta_value' => 'approved',
    );


// "pagination"

//$pieces = $limit * $piece;

    $admin = new WP_User_Query( array( 'role' => 'Administrator' ) );
    $admin = $admin->results['0']->ID;
    $users = str_replace("build",$admin, $users);
    $asst = get_stylesheet_directory_uri();

    $users = explode(',', $users);


    if(!is_null($users)){
        $args['include'] = $users;
        $args['orderby'] = 'include';
        $args['order'] = 'ASC';
    }

    $search_list    = array();

    $user_query = new WP_User_Query( $args );
    $content = "";
// User Loop
    if (!empty($user_query->results)) {
        foreach ( $user_query->results as $user ) {
            $terms              = wp_get_object_terms( $user->ID, 'tags' );
            $terms_list         = array();
            $user_meta          = get_user_meta($user->ID);
            $data_search_list   = array();


//        $search_list[] = $user_meta['first_name'][0];
//        $search_list[] = $user_meta['last_name'][0];
            $search_list[] = $user->display_name;

            $data_search_list[] = $user_meta['first_name'][0];
            $data_search_list[] = $user_meta['last_name'][0];
            $data_search_list[] = $user->display_name;


            $data_search_list = implode(',' , $data_search_list );




            if ( ! empty( $terms ) ) {
                foreach($terms as $term){
                    $terms_list[] = $term->slug;
                }
            }

            $terms_list = implode(',' , $terms_list);

            if ($user->ID == $admin) {
                $content .= "<div class='col-sm-4 col-user' data-search='".$data_search_list."'  data-terms='".$terms_list."'>";
                $content .= "<div class='ns-collection__box'>";

                $content .= "<a href='" . $homeurl . "/profile-features' title='titlu'>";
                $content .= "<img class='lazyload' data-src='" . $asst . "/assets/img/profile-features.jpg' alt=''>";
                $content .= "</a>";

                $content .= "<div class='um-member-card no-photo'>";

                $content .= "<div class='um-member-name a'>";
                $content .= "<a href='" . $homeurl . "/profile-features' title=''>Share Your Work</a>";
                $content .= "</div>";
                $content .= "</div>";
                $content .= "</div>";
                $content .= "</div>";
            }
            else {
                $image = get_user_meta($user->ID, 'cover_photo', true);
                $ext = '.' . pathinfo($image, PATHINFO_EXTENSION);
                //check if cover exists
                if ( file_exists( $ultimatemember->files->upload_basedir . $user->ID . '/'.$image ) ) {
                    $resized = image_make_intermediate_size( $ultimatemember->files->upload_basedir . $user->ID . '/'.$image, 450, 300, true );
                    if( is_ssl() ){
                        $ultimatemember->files->upload_baseurl = str_replace("http://", "https://",  $ultimatemember->files->upload_baseurl );
                    }
                    $base_url = $ultimatemember->files->upload_baseurl . $user->ID . '/';
                    if($resized !== false){
                        $uri = $base_url .$resized['file'];
                    }
                }
                else {
                    $def_uri = um_get_default_cover_uri();
                    $base_def_uri = substr($def_uri, 0, strrpos($def_uri, '/')+1);
                    $path = parse_url($def_uri);
                    $resize_default = isset($path['path']) ? image_make_intermediate_size( $_SERVER['DOCUMENT_ROOT'].$path['path'], 450, 300, true ) : um_get_default_cover_uri();
                    $uri = $resize_default !== false ? $base_def_uri.$resize_default['file'] : '';
                }

                $homeurl = home_url();
                $permalink = new UM_Permalinks;
                $user_slug = $permalink->profile_slug($user->display_name, $user->first_name, $user->last_name);
                $content .= "<div class='col-sm-4 col-user' data-search='".$data_search_list."' data-terms='".$terms_list."'>";
                $content .= "<div id='".$user->ID."' class='ns-collection__box'>";

                $content .= "<a href='" . $homeurl . "/photographer/" . $user_slug . "/' title='" . $user->display_name."'>";
                $content .= "<img class='lazyload' data-src='" . $uri . "' alt=''>";
                $content .= "</a>";

                $content .= "<div class='um-member-card no-photo'>";

                $content .= "<div class='um-member-name a'>";
                $content .= "<a href='" . $homeurl . "/photographer/" . $user_slug . "/' title='" . $user->display_name . "'>" . $user->display_name . "</a>";
                $content .= "</div>";
                $content .= "</div>";
                $content .= "</div>";
                $content .= "</div>";
            }
        }


    }
    if (isset($action)  && $action != 'wpsh_collection_filter' && $action != 'wpsh_collection_filter') {
        echo $content;
        die();
    }
    else {

        if($content == ''){
            echo "<div class='fusion-row um-form'>";
            echo "<div class='no-results'>Sorry, no results were found..</div>";
            echo "</div>";
            die();
        }
        $search_list = json_encode($search_list);
        $content .= "<div id='data-search' data-search='".$search_list."'></div>";
        return $content;
    }
}







// collection new shortcode
add_shortcode('swph_ultimatemember', 'swph_ultimatemember');
function swph_ultimatemember($atts = array(), $content = ""){




    //if tag filter is clicked get users with those tags and remake the users
    if( isset( $atts['tag'] ) && !empty( $atts['tag'] ) ){


        //get users with tag only
        $term = get_term_by('slug', $atts['tag'] , 'tags');
        $users = get_objects_in_term($term->term_id, 'tags');

        //assign users with tags only
        $atts['users'] = implode(',' , $users);


    }


    //if user is using search , remake the users query
    if( isset( $atts['stringsearch'] ) && !empty( $atts['stringsearch'] )){


        if($atts['users']){
            $user_ids = explode(",", $atts['users']);
            $args = array(
                'meta_query'        => array(
                    'relation'  => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key'     => 'first_name',
                            'value'   => $atts['stringsearch'],
                            'compare' => 'LIKE'
                        ),
                        array(
                            'key'     => 'last_name',
                            'value'   => $atts['stringsearch'],
                            'compare' => 'LIKE'
                        ),
                        array(
                            'key'     => 'nickname',
                            'value'   => $atts['stringsearch'],
                            'compare' => 'LIKE'
                        ),
                    ),
                    array(
                        'key'     => 'account_status',
                        'value'   => 'approved',
                        'compare' => '='
                    ),
                ),
            );

            if(!is_null($user_ids)){
                $args['include'] = $user_ids;
                $args['orderby'] = 'include';
                $args['order'] = 'ASC';
            }

            $user_query = new WP_User_Query( $args );

            if($user_query->results){

                //new user array that have the search string ( name , nickname)
                $matching_search_users = [];

                //get user key and id and remake users array
                foreach($user_query->results as $key => $value){
                    $matching_search_users[$key] = $value->ID;
                }

                //implode users array and assign it to user atts
                $atts['users'] = implode(',' , $matching_search_users);

            }
        }
    }


    $output = swph_get_members($atts);
    $userno = explode(",", $atts['users']);
    $userno = count($userno);




    $output = "<div class='ns-collection um-form'><div class='row'>" . $output;

    $limit = $atts['limit'];
    //show load more button if more then 21 users
    if($userno > $limit){
        $output .= "<div class='butrow row text-center'><button id='loadmore' data-page='2' data-limit='".$limit."'   class='fusion-button button-flat fusion-button-round button-large button-custom home_cta'>Load more...</button></div>";
    }
    $output .= "</div></div>";


    return $output;
}







//image box shortcode
function box_image_shortcode($atts = array(), $content = ""){

    ob_start();

    include get_stylesheet_directory().'/includes/output-shortcode-ns-box-image.php';

    $output = ob_get_clean();

    return $output;
}
add_shortcode('ns_box_image', 'box_image_shortcode');







//user profile
function ns_user_profile_shortcode($atts = array(), $content = ""){

    ob_start();

    // include get_stylesheet_directory().'/includes/output-shortcode-ns-user-profile.php';
    $public_profile = str_replace('?profiletab=main&um_action=edit','',um_edit_profile_url());

    echo '<a href="'.$public_profile.'">'.$public_profile.'</a>';

    $output = ob_get_clean();

    return $output;
}
add_shortcode('ns_user_profile', 'ns_user_profile_shortcode');





//mandatory field
function ns_mandatory_field_shortcode($atts = array(), $content = ""){

    $output = '<span class="mandatory_field">* mandatory field</span>';

    return $output;
}
add_shortcode('ns_mandatory_field', 'ns_mandatory_field_shortcode');






//save "about you" section
function ns_update_user_social_links(){
// if this fails, check_admin_referer() will automatically print a "failed" page and die.
    if ( ! empty( $_POST ) && wp_verify_nonce( $_POST['_ns_update_social_security'], 'ns_update_user_social' )  ) {
        // process form data
        $user_id = $_POST['user_id'];

        if ( isset( $_POST["social_website"] ) && ($_POST['social_website']!='') && ( (!filter_var($_POST['social_website'], FILTER_VALIDATE_URL)) || (!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i',$_POST['social_website'],$matches))) ) {
            $result['website_error'] = 'Your Website URL has not a valid format (ex. http://www.example.com)';
        }
        else{
            update_user_meta($user_id,'social_website',$_POST["social_website"]);
            $result['website_error'] = '';
        }


        if ( isset( $_POST['social_facebook'] ) && ($_POST['social_facebook']!='') && ( (!filter_var($_POST['social_facebook'], FILTER_VALIDATE_URL)) || (!preg_match('#https?\://(?:www\.)?facebook\.com/(\d+|[A-Za-z0-9\.]+)/?#',$_POST['social_facebook'],$matches))) ) {
            $result['facebook_error'] = 'Your Facebook URL has not a valid format (ex. http://www.facebook.com/username)';
        }
        else{
            update_user_meta($user_id,'social_facebook',$_POST["social_facebook"]);
            $result['facebook_error'] = '';
        }


        if ( isset( $_POST['social_instagram'] ) && ($_POST['social_instagram']!='') && ( (!filter_var($_POST['social_instagram'], FILTER_VALIDATE_URL)) || (!preg_match('#https?\://(?:www\.)?instagram\.com/(\d+|[A-Za-z0-9\.]+)/?#',$_POST['social_instagram'],$matches))) ) {
            $result['instagram_error'] = 'Your Instagram URL has not a valid format (ex. http://www.instagram.com/username)';
        }
        else{
            update_user_meta($user_id,'social_instagram',$_POST["social_instagram"]);
            $result['instagram_error'] = '';
        }

    }


    $result = json_encode($result);
    echo $result;
    exit;
}
add_action( 'wp_ajax_ns_update_user_social_links', 'ns_update_user_social_links' );
add_action( 'wp_ajax_nopriv_ns_update_user_social_links', 'ns_update_user_social_links' );






if( isset($_GET['update_users']) && ($_GET['update_users'] == 'update_old_statuses') ) {
    // add_action('template_redirect','update_old_statuses');
}






function update_old_statuses(){

    $row = 1;

    $upload_dir = wp_upload_dir();
    $file = $upload_dir['basedir'].'/list-members.csv';

    $csv = file_get_contents($file);

    $emails_array = explode("\n",$csv);

    $ids_array = array();


    foreach($emails_array as $email){
        $user = get_user_by( 'email', $email );
        if($user){
            $ids_array[] = $user->ID;
        }
    }

    // echo '<pre>'.print_r($emails_array,1).'</pre>';
    // echo '<pre>'.print_r($ids_array,1).'</pre>';

    $args = array(
        'exclude' => $ids_array,
        // 'number' => 5
    );
    $user_query = new WP_User_Query( $args );

    // Get the results
    $users = $user_query->get_results();

    // Check for results
    if (!empty($users)) {
        echo '<ol>';
        // loop through each author
        foreach ($users as $key => $user)
        {
            // get all the user's data
            $user_id = $user->ID;
            echo '<li>';
            echo 'User email: '  .$user->user_email;
            $role = strtolower( get_user_meta($user_id, 'role', true) );
            if( ($role =='member') || ($role =='entrant') ){
                update_user_meta($user_id,'role','past');
                $new_role = get_user_meta($user_id,'role',true);
                echo ' - Role changed from <b>'.$role .'</b> to <b>'. $new_role .'</b>';
            }
            else{
                echo ' - Role didn\'t changed, current role: '.$role;
            }
            echo '</li>';
        }
        echo '</ol>';
    } else {
        echo 'No users found';
    }

    die();

};






// Add Shortcode
function domylfpop_shortcode() {
    ob_start();
    ?>
    <?php
    if (
    is_user_logged_in()){
        $current_user = wp_get_current_user();
        $name = $current_user->display_name ;
        $userid = $current_user->ID;
        $email = $current_user->user_email;
        ?>
        <input type="hidden" name="url" value="<?php echo um_edit_profile_url();?>" class="url_user">
        <input type="hidden" name="name" value="<?php echo $name;?>" class="user_name">
        <input type="hidden" name="userid" value="<?php echo $userid;?>" class="user_id">
        <input type="hidden" name="email" value="<?php echo $email;?>" class="user_email">
        <div class="modal fade" id="mylf-popmail" tabindex="-1" role="dialog" aria-labelledby="mylf-popmail" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Feedback</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="initial_text_feed">Are you sure you want to request feedback now? All feedback requests are final.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary sendmail-feedback">Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    return ob_get_clean();

}
add_shortcode( 'mylfpopmail', 'domylfpop_shortcode' );







function domylfpop_feature_shortcode() {
    ob_start();
    ?>
    <?php
    if (
    is_user_logged_in()){
        $current_user = wp_get_current_user();
        $name = $current_user->display_name ;
        $userid = $current_user->ID;
        $email = $current_user->user_email;
        ?>
        <input type="hidden" name="url" value="<?php echo um_edit_profile_url();?>" class="url_user">
        <input type="hidden" name="name" value="<?php echo $name;?>" class="user_name">
        <input type="hidden" name="userid" value="<?php echo $userid;?>" class="user_id">
        <input type="hidden" name="email" value="<?php echo $email;?>" class="user_email">
        <div class="modal fade" id="mylf-popfeaturemail" tabindex="-1" role="dialog" aria-labelledby="mylf-popfeaturemail" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Feature</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="initial_text_feat">Are you sure you want to request to be featured now? All feature requests are final.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary sendmail-feature">Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    return ob_get_clean();

}
add_shortcode( 'mylfpopfeaturemail', 'domylfpop_feature_shortcode' );







function domylfpop_series_shortcode() {
    ob_start();
    ?>
    <?php
    if (
    is_user_logged_in()){
        $current_user = wp_get_current_user();
        $name = $current_user->display_name ;
        $userid = $current_user->ID;
        $email = $current_user->user_email;
        ?>
        <input type="hidden" name="url" value="<?php echo um_edit_profile_url();?>" class="url_user">
        <input type="hidden" name="name" value="<?php echo $name;?>" class="user_name">
        <input type="hidden" name="userid" value="<?php echo $userid;?>" class="user_id">
        <input type="hidden" name="email" value="<?php echo $email;?>" class="user_email">
        <div class="modal fade" id="mylf-popseriesmail" tabindex="-1" role="dialog" aria-labelledby="mylf-popseriesmail" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Series Award</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="initial_text_series">xYou are about to submit your profile and associated body of work to the Series Award. All submissions are final so please make sure that your profile is 100 percent complete and includes:<br><br>
                            - a series of 5 to 20 images<br>
                            - a series description<br>
                            - your biography/artist statement<br>
                            - your full name and surname (you can edit this along with your website and social media links on your account page)<br>
                            - a banner and profile photo of your choice<br>
                            <br>
                            Please note the Series Award is not a monthly competition but a yearly one and will not be judged until July 2018, after the end of this edition of Life Framer edition IV.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary sendmail-series">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    return ob_get_clean();

}
add_shortcode( 'mylfpopseriesmail', 'domylfpop_series_shortcode' );






// Add Shortcode
function do_request_feedback() {
    ob_start();
    $user_id = get_current_user_id();
    $usermta = get_user_meta($user_id, 'user_mail_sent', true);
    if($usermta  == 1 || validateDate($usermta))
    {
        ?>
        <div class="fusion-aligncenter"><i>Thanks for submitting your Profile for feedback. Your request has been safely received, and we will return our critique to you by email. This may take some time, but please rest assured we have not forgotten.</i></div>
        <?php
    }
    else {
        ?>
        <div class="fusion-button-wrapper fusion-aligncenter">
            <a href="#" data-toggle="modal" data-target="#mylf-popmail" class="fusion-button button-flat fusion-button-round button-large button-custom button-3 home_cta request-feedback" target="_self"><span class="fusion-button-text">REQUEST FEEDBACK</span></a>
        </div>
        <div class="fusion-aligncenter txtfeedrqs"></div>
        <?php
    }

    return ob_get_clean();
}
add_shortcode( 'request-feedback', 'do_request_feedback' );







function do_request_feature() {
    ob_start();
    $user_id = get_current_user_id();
    $usermta = get_user_meta($user_id, 'feature_mail_sent', true);
    if($usermta  == 1 || validateDate($usermta))
    {
        ?>
        <div class="fusion-aligncenter"><i>Thank you very much for submitting your Life Framer Profile. We enjoy reviewing every Profile, and while we can't include every single one in The Collection it is still a portfolio for you to use, and we encourage you to share the link on your social media and with your friends and contacts. If your Profile is selected for The Collection, we will of course let you know.</i></div>
        <?php
    } else {
        ?>
        <div class="fusion-button-wrapper fusion-aligncenter">
            <a href="#" data-toggle="modal" data-target="#mylf-popfeaturemail" class="fusion-button button-flat fusion-button-round button-large button-custom button-3 home_cta request-feature" target="_self"><span class="fusion-button-text">REQUEST TO GET FEATURED</span></a>
        </div>
        <div class="fusion-aligncenter txtfeatrqs"></div>
        <?php
    }

    return ob_get_clean();
}
add_shortcode( 'request-feature', 'do_request_feature' );







function do_request_series() {
    ob_start();
    $user_id = get_current_user_id();
    $usermta = get_user_meta($user_id, 'series_mail_sent', true);
    if($usermta  == 1 || validateDate($usermta))
    {
        ?>
        <div class="fusion-aligncenter"><i>Thank you very much for your submission to the Series Award. Your submission found us well... The winner will be selected by our guest judge shortly after the end of the award and you can expect and announcement before mid-July 2018. We will contact the grand winner and honorary mentions shortly before the announcement. Thank you and good luck!</i></div>
        <?php
    } else {
        ?>
        <div class="fusion-button-wrapper fusion-aligncenter">
            <a href="#" data-toggle="modal" data-target="#mylf-popseriesmail" class="fusion-button button-flat fusion-button-round button-large button-custom button-3 home_cta request-series" target="_self"><span class="fusion-button-text">ENTER SERIES AWARD</span></a>
        </div>
        <div class="fusion-aligncenter txtseriesrqs"></div>
        <?php
    }

    return ob_get_clean();
}
add_shortcode( 'request-series', 'do_request_series' );

function send_pop_email(){
    $to = get_bloginfo('admin_email');
    //$to = 'andrei.kantor@gmail.com';
    $subject = 'Feedback Requested';
    $message = 'User URL: ' . $_POST["url_user"] .'<br/>'
        . 'Username: ' . $_POST["user_name"] .'<br/>'
        . 'User ID: ' . $_POST["user_id"] .'<br/>'
        . 'User Email: ' . $_POST["user_email"] .'<br/>';
    $when = date('d-M-Y');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sendmail = wp_mail( $to, $subject, $message);
    if ($sendmail){
        echo 'success';
        update_user_meta( $_POST["user_id"], 'user_mail_sent', $when );
    }
    else {
        echo 'error';
    }
    die();
}
add_action( 'wp_ajax_send_pop_email', 'send_pop_email' );
add_action( 'wp_ajax_nopriv_send_pop_email', 'send_pop_email' );

function send_feature_pop_email(){
    $to = get_bloginfo('admin_email');
    //$to = 'andrei.kantor@gmail.com';
    $subject = 'Feature Request';
    $message = 'User URL: ' . $_POST["url_user"] .'<br/>'
        . 'Username: ' . $_POST["user_name"] .'<br/>'
        . 'User ID: ' . $_POST["user_id"] .'<br/>'
        . 'User Email: ' . $_POST["user_email"] .'<br/>';
    $when = date('d-M-Y');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sendmail = wp_mail( $to, $subject, $message);
    if ($sendmail){
        echo 'success';
        update_user_meta( $_POST["user_id"], 'feature_mail_sent', $when );
    }
    else {
        echo 'error';
    }
    die();
}
add_action( 'wp_ajax_send_feature_pop_email', 'send_feature_pop_email' );
add_action( 'wp_ajax_nopriv_send_feature_pop_email', 'send_feature_pop_email' );

function send_series_pop_email(){
    $to = get_bloginfo('admin_email');
    //$to = 'andrei.kantor@gmail.com';
    $subject = 'Series Award Request';
    $message = 'User URL: ' . $_POST["url_user"] .'<br/>'
        . 'Username: ' . $_POST["user_name"] .'<br/>'
        . 'User ID: ' . $_POST["user_id"] .'<br/>'
        . 'User Email: ' . $_POST["user_email"] .'<br/>';
    $when = date('d-M-Y');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sendmail = wp_mail( $to, $subject, $message);
    if ($sendmail){
        echo 'success';
        update_user_meta( $_POST["user_id"], 'series_mail_sent', $when );
    }
    else {
        echo 'error';
    }
    die();
}
add_action( 'wp_ajax_send_series_pop_email', 'send_series_pop_email' );
add_action( 'wp_ajax_nopriv_send_series_pop_email', 'send_series_pop_email' );

function mail_get_included() {
    ob_start();
    ?>
    <?php

    if ( is_user_logged_in() ){
        $current_user = wp_get_current_user();
        $name = $current_user->display_name ;
        $userid = $current_user->ID;
        $email = $current_user->user_email;
        ?>
        <input type="hidden" name="url" value="<?php echo um_edit_profile_url();?>" class="url_user">
        <input type="hidden" name="name" value="<?php echo $name;?>" class="user_name">
        <input type="hidden" name="userid" value="<?php echo $userid;?>" class="user_id">
        <input type="hidden" name="email" value="<?php echo $email;?>" class="user_email">
        <?php
    }
    else{
        die();
    } ?>
    <div class="fusion-button-wrapper fusion-aligncenter">
        <a href="#" data-toggle="modal" data-target="#get-included-popmail" class="fusion-button button-flat fusion-button-round button-large button-custom button-3 home_cta get_included" target="_self"><span class="fusion-button-text">GET INCLUDED</span></a>
    </div>
    <div class="modal fade" id="get-included-popmail" tabindex="-1" role="dialog" aria-labelledby="get-included-popmail" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Collection</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text_getincluded">Get included in the collection? We will review your profile and let you know if it gets included in the collection. It may take us a few days to review so please bear with us.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary get_included_mail">Yes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode( 'get_included', 'mail_get_included' );







function send_pop_email_get_included(){
    $to = get_bloginfo('admin_email');
    //$to = 'andrei.kantor@gmail.com';
    $subject = 'Get Included Request';
    $message = 'User URL: ' . $_POST["url_user"] .'<br/>'
        . 'Username: ' . $_POST["user_name"] .'<br/>'
        . 'User ID: ' . $_POST["user_id"] .'<br/>'
        . 'User Email: ' . $_POST["user_email"] .'<br/>';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sendmail = wp_mail( $to, $subject, $message, $headers, $attachments );
    if ($sendmail){
        echo 'success';
    }
    else {
        echo 'error';
    }
    die();
}
add_action( 'wp_ajax_send_pop_email_get_included', 'send_pop_email_get_included' );
add_action( 'wp_ajax_nopriv_send_pop_email_get_included', 'send_pop_email_get_included' );







function edit_profile() {
    ob_start();
    ?>
    <div class="fusion-button-wrapper fusion-aligncenter">
        <a href="<?php echo um_edit_profile_url();?>" class="fusion-button button-flat fusion-button-round button-large button-custom button-3 home_cta" target="_self"><span class="fusion-button-text">Edit Profile</span></a>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode( 'go_edit_profile', 'edit_profile' );







function custom_user_profile_fields($user){
    if(is_object($user)) {
        $user_mail_sent = esc_attr( get_the_author_meta( 'user_mail_sent', $user->ID ) );
        $feature_mail_sent  = esc_attr( get_the_author_meta( 'feature_mail_sent', $user->ID ) );
        $series_mail_sent  = esc_attr( get_the_author_meta( 'series_mail_sent', $user->ID ) );
    }
    else {
        $user_mail_sent = null;
        $feature_mail_sent = null;
        $series_mail_sent = null;
    }
    ?>
    <h3>Clear the 'Request Feedback'</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_mail_sent">Feedback Request Email sent</label></th>
            <td>
                <input type="text" class="regular-text" name="user_mail_sent" value="<?php echo $user_mail_sent; ?>" id="user_mail_sent" /><br />
                <span class="description">0: User can ask for feedback. 1: User already asked for feedback.</span>
            </td>
        </tr>
    </table>
    <h3>Clear the 'Feature Request'</h3>
    <table class="form-table">
        <tr>
            <th><label for="feature_mail_sent">Featured Request Email sent</label></th>
            <td>
                <input type="text" class="regular-text" name="feature_mail_sent" value="<?php echo $feature_mail_sent; ?>" id="feature_mail_sent" /><br />
                <span class="description">0: User can ask to be featured. 1: User already asked to be featured.</span>
            </td>
        </tr>
    </table>
    <h3>Clear the 'Series Award Request'</h3>
    <table class="form-table">
        <tr>
            <th><label for="series_mail_sent">Series Award Email sent</label></th>
            <td>
                <input type="text" class="regular-text" name="series_mail_sent" value="<?php echo $series_mail_sent; ?>" id="series_mail_sent" /><br />
                <span class="description">0: User can ask to be in the Series Award. 1: User already asked to be in the Series Award.</span>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'custom_user_profile_fields' );
add_action( 'edit_user_profile', 'custom_user_profile_fields' );
add_action( "user_new_form", "custom_user_profile_fields" );






function save_custom_user_profile_fields($user_id){
    # again do this only if you can
    if(!current_user_can('manage_options'))
        return false;

    # save my custom field
    update_user_meta($user_id, 'user_mail_sent', $_POST['user_mail_sent']);
    update_user_meta($user_id, 'feature_mail_sent', $_POST['feature_mail_sent']);
    update_user_meta($user_id, 'series_mail_sent', $_POST['series_mail_sent']);
}
add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');







function um_change_profile_cover_photo_label_custom( $args ){
    global $ultimatemember;
    $cover_min_w = um_get_option('cover_min_width');
    $max_size =  $ultimatemember->files->format_bytes( $args['cover_photo']['max_size'] );
    list( $file_size, $unit ) = explode(' ', $max_size );

    if( $file_size >= 999999999  ){

    }else{
        $args['cover_photo']['upload_text'] .= '<small class=\'um-max-filesize\'>('.__('max','ultimatemember').': '.$file_size.$unit.'; '. $cover_min_w .'px wide minimum)</small><small class=\'do-not-close-window\'>(Upload may take a little while for the larger files - please do not close the window)</small>';
    }
    // $args['cover_photo']['upload_text'] .= '<p class="do-not-close-window-"><small>(Upload may take a little while for the larger files - please do not close the window)</small></p>';
    return $args;
}
remove_filter('um_predefined_fields_hook','um_change_profile_cover_photo_label',10,1);
add_filter('um_predefined_fields_hook','um_change_profile_cover_photo_label_custom',10,1);






function um_change_profile_photo_label_custom( $args ){
    global $ultimatemember;
    $max_size =  $ultimatemember->files->format_bytes( $args['profile_photo']['max_size'] );
    list( $file_size, $unit ) = explode(' ', $max_size );

    if( $file_size >= 999999999  ){

    }else{
        $args['profile_photo']['upload_text'] .= '<small class=\'um-max-filesize\'>(190px minumum width; 2MB file size maximum)</small><small class=\'do-not-close-window\'>(Upload may take a little while for the larger files - please do not close the window)</small>';
    }
    return $args;
}
remove_filter('um_predefined_fields_hook','um_change_profile_photo_label',10,1);
add_filter('um_predefined_fields_hook','um_change_profile_photo_label_custom',10,1);






//ultimate member custom validation
function website_custom_validation( $args ) {
    global $ultimatemember;

    if ( isset( $args['social_website'] ) && ($args['social_website']!='') && ( (!filter_var($args['social_website'], FILTER_VALIDATE_URL))  ) ) {
        $ultimatemember->form->add_error( 'social_website', 'Your Website URL has not a valid format (ex. http://www.example.com)' );
    }

    if ( isset( $args['social_facebook'] ) && ($args['social_facebook']!='') && ( (!filter_var($args['social_facebook'], FILTER_VALIDATE_URL)) || (!preg_match('#https?\://(?:www\.)?facebook\.com/(\d+|[A-Za-z0-9\.]+)/?#',$args['social_facebook'],$matches))) ) {
        $ultimatemember->form->add_error( 'social_facebook', 'Your Facebook URL has not a valid format (ex. http://www.facebook.com/username)' );
    }

    if ( isset( $args['social_instagram'] ) && ($args['social_instagram']!='') && ( (!filter_var($args['social_instagram'], FILTER_VALIDATE_URL)) || (!preg_match('#https?\://(?:www\.)?instagram\.com/(\d+|[A-Za-z0-9\.]+)/?#',$args['social_instagram'],$matches))) ) {
        $ultimatemember->form->add_error( 'social_instagram', 'Your Instagram URL has not a valid format (ex. http://www.instagram.com/username)' );
    }

}
add_action('um_submit_form_errors_hook_','website_custom_validation', 999, 1);







// Remove versions from files
function swph_remove_script_version( $src ){
    $parts = explode( '?ver', $src );
    return $parts[0];
}
add_filter( 'script_loader_src', 'swph_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'swph_remove_script_version', 15, 1 );






function validateDate($date) {
    $d = DateTime::createFromFormat('d-M-Y', $date);
    return $d && $d->format('d-M-Y') === $date;
}





// Dashboard widget to export spreadsheet
add_action('wp_dashboard_setup', 'swph_custom_dashboard_widgets');
function swph_custom_dashboard_widgets() {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('swph_excel', 'User requests', 'swph_dashboard_excel');
}







//date sort
function date_sort($a, $b) {
    $t1 = strtotime($a['when']);
    $t2 = strtotime($b['when']);
    return $t1 - $t2;
}






//swph dashboard excel
function swph_dashboard_excel() {
    include("includes/PHPExcel/PHPExcel.php");
    $url = get_bloginfo('wpurl');
////////////////////////////////////////////////////////////////////////////////////
    $feedback = get_users(
        array(
            'meta_key' => "user_mail_sent",
            'meta_value' => "0",
            'meta_compare' => ">",
            'fields' => array ( "user_nicename", "user_email", "ID" )
        )
    );
    if (isset($feedback) && !empty($feedback)) {
        foreach ($feedback as $user) {
            $whendate = get_user_meta($user->ID, 'user_mail_sent', true);
            $newfeed['when'] = $whendate;
            $lastname = get_user_meta($user->ID, "last_name");
            $firstname = get_user_meta($user->ID, "first_name");
            $newfeed['name'] = $firstname['0'].$lastname['0'];
            $newfeed['email'] = $user->user_email;
            $newfeed['id'] = $user->ID;
            $newfeed['url'] = $url . "/photographer/" . $user->user_nicename;
            $feedbacks[] = $newfeed;
        }
    }

    usort($feedbacks, "date_sort");
    echo "<br><h2>Feedback requested</h2>";
    echo "<a href='" . $url . "/wp-admin/feedback_requests.xlsx'>Feedback request list</a><br>";
    $feedbacksheet = new PHPExcel();
    $feedbacksheet->setActiveSheetIndex(0);
    $feedbackwork = $feedbacksheet->getActiveSheet();
    $feedbackwork->getColumnDimension('A')->setAutoSize(true);
    $feedbackwork->getColumnDimension('B')->setAutoSize(true);
    $feedbackwork->getColumnDimension('C')->setAutoSize(true);
    $feedbackwork->getColumnDimension('D')->setAutoSize(true);
    $feedbackwork->getColumnDimension('E')->setAutoSize(true);
    $x = 0;
    foreach ($feedbacks as $value) {
        $x++;
        $feedbackwork->SetCellValueByColumnAndRow(0, $x, $value['when']);
        $feedbackwork->SetCellValueByColumnAndRow(1, $x, $value['name']);
        $feedbackwork->SetCellValueByColumnAndRow(2, $x, $value['email']);
        $feedbackwork->SetCellValueByColumnAndRow(3, $x, $value['id']);
        $feedbackwork->SetCellValueByColumnAndRow(4, $x, $value['url']);
        $feedbackwork->getHyperlink('B'.$x)->setUrl($value['url']);
    }
    $writer = new PHPExcel_Writer_Excel2007($feedbacksheet);
    $writer->save('feedback_requests.xlsx');

////////////////////////////////////////////////////////////////////////////////////
    $feature = get_users(
        array(
            'meta_key' => "feature_mail_sent",
            'meta_value' => "0",
            'meta_compare' => ">",
            'fields' => array ( "user_nicename", "user_email", "ID" )
        )
    );
    if (isset($feature) && !empty($feature)) {
        foreach ($feature as $user) {
            $whendate = get_user_meta($user->ID, 'feature_mail_sent', true);
            $newfeat['when'] = $whendate;
            $lastname = get_user_meta($user->ID, "last_name");
            $firstname = get_user_meta($user->ID, "first_name");
            $newfeat['name'] = $firstname['0'].$lastname['0'];
            $newfeat['email'] = $user->user_email;
            $newfeat['id'] = $user->ID;
            $newfeat['url'] = $url . "/photographer/" . $user->user_nicename;
            $features[] = $newfeat;

        }
    }
    usort($features, "date_sort");
    echo "<br><h2>Featured requested</h2>";
    echo "<a href='" . $url . "/wp-admin/featured_requests.xlsx'>Feature request list</a><br>";
    $featuredsheet = new PHPExcel();
    $featuredsheet->setActiveSheetIndex(0);
    $featuredwork = $featuredsheet->getActiveSheet();
    $featuredwork->getColumnDimension('A')->setAutoSize(true);
    $featuredwork->getColumnDimension('B')->setAutoSize(true);
    $featuredwork->getColumnDimension('C')->setAutoSize(true);
    $featuredwork->getColumnDimension('D')->setAutoSize(true);
    $featuredwork->getColumnDimension('E')->setAutoSize(true);
    $x = 0;
    foreach ($features as $value) {
        $x++;
        $featuredwork->SetCellValueByColumnAndRow(0, $x, $value['when']);
        $featuredwork->SetCellValueByColumnAndRow(1, $x, $value['name']);
        $featuredwork->SetCellValueByColumnAndRow(2, $x, $value['email']);
        $featuredwork->SetCellValueByColumnAndRow(3, $x, $value['id']);
        $featuredwork->SetCellValueByColumnAndRow(4, $x, $value['url']);
        $featuredwork->getHyperlink('B'.$x)->setUrl($value['url']);
    }
    $writer = new PHPExcel_Writer_Excel2007($featuredsheet);
    $writer->save('featured_requests.xlsx');
    ////////////////////////////////////////////////////////////////////////////////////
    $series = get_users(
        array(
            'meta_key' => "series_mail_sent",
            'meta_value' => "0",
            'meta_compare' => ">",
            'fields' => array ( "user_nicename", "user_email", "ID" )
        )
    );
    if (isset($series) && !empty($series)) {
        foreach ($series as $user) {
            $whendate = get_user_meta($user->ID, 'series_mail_sent', true);
            $newser['when'] = $whendate;
            $lastname = get_user_meta($user->ID, "last_name");
            $firstname = get_user_meta($user->ID, "first_name");
            $newser['name'] = $firstname['0'].$lastname['0'];
            $newser['email'] = $user->user_email;
            $newser['id'] = $user->ID;
            $newser['url'] = $url . "/photographer/" . $user->user_nicename;
            $seriess[] = $newser;
        }
    }
    usort($seriess, "date_sort");
    echo "<br><h2>Series Award requested</h2>";
    echo "<a href='" . $url . "/wp-admin/series_award_requests.xlsx'>Series Award request list</a><br>";
    $seriessheet = new PHPExcel();
    $seriessheet->setActiveSheetIndex(0);
    $serieswork = $seriessheet->getActiveSheet();
    $serieswork->getColumnDimension('A')->setAutoSize(true);
    $serieswork->getColumnDimension('B')->setAutoSize(true);
    $serieswork->getColumnDimension('C')->setAutoSize(true);
    $serieswork->getColumnDimension('D')->setAutoSize(true);
    $serieswork->getColumnDimension('E')->setAutoSize(true);
    $x = 0;
    foreach ($seriess as $value) {
        $x++;
        $serieswork->SetCellValueByColumnAndRow(0, $x, $value['when']);
        $serieswork->SetCellValueByColumnAndRow(1, $x, $value['name']);
        $serieswork->SetCellValueByColumnAndRow(2, $x, $value['email']);
        $serieswork->SetCellValueByColumnAndRow(3, $x, $value['id']);
        $serieswork->SetCellValueByColumnAndRow(4, $x, $value['url']);
        $serieswork->getHyperlink('B'.$x)->setUrl($value['url']);
    }
    $writer = new PHPExcel_Writer_Excel2007($seriessheet);
    $writer->save('series_award_requests.xlsx');
}






//fix registration
function um_fix_registration_save( $user_id ) {

    /* Entrant */
    if ( isset( $_POST['first_name-8383'] ) )
        update_user_meta($user_id, 'first_name', $_POST['first_name-8383']);

    if ( isset( $_POST['last_name-8383'] ) )
        update_user_meta($user_id, 'last_name', $_POST['last_name-8383']);


    /* Member */
    if ( isset( $_POST['first_name-8172'] ) )
        update_user_meta($user_id, 'first_name', $_POST['first_name-8172']);

    if ( isset( $_POST['last_name-8172'] ) )
        update_user_meta($user_id, 'last_name', $_POST['last_name-8172']);

}
add_action( 'user_register', 'um_fix_registration_save', 10, 1 );






//test img
function test_img(){
    $path = um_gallery()->gallery_path . '/6988/';
    $filename = 'a.jpg';
    // $filename = 'sg0000-6.jpg';

    $targetFile =  $path. $filename;
    echo $targetFile;
    echo '<br>';

    $test = wp_get_image_editor( $targetFile);

    var_dump($test);

    die();

    echo '<pre>'.print_r($test,1).'</pre>';

    die();

    $image = wp_get_image_editor( $targetFile );

    if ( ! is_wp_error( $image ) ) {
        $image->stream();
    }

    echo '<pre>'.print_r($image,1).'</pre>';
    die();

    $image_original = wp_get_image_editor( $targetFile );
    $filetype = wp_check_filetype($targetFile);
    $basename = basename($targetFile, '.'.$filetype['ext']);

    if ( ! is_wp_error( $image ) ) {

        if( get_option( 'image_quality_settings' ) ) {
            $image->set_quality(get_option( 'image_quality_settings' ));
        }
        $size = $image->get_size();
        if($size['width'] == $size['height']){
            $image->resize( 280, 280, false );
        }
        else{
            $image->resize( 360, 360, false );
        }
        $image->save( $path . $basename.'-thumbnail.'.$filetype['ext'] );

    }

    die();
}






function defer_parsing_of_js ( $url ) {
    if ( FALSE === strpos( $url, '.js' ) ) return $url;
    if ( strpos( $url, 'jquery.js' ) ) return $url;
    return "$url' defer ";
}
add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );






// Register Custom Post Type
function lf_slider() {
    $labels = array(
        'name'                  => 'LF Slider images',
        'singular_name'         => 'LF Slider image',
        'menu_name'             => 'LF Slider',
        'name_admin_bar'        => 'LF Slider',
        'archives'              => 'Item Archives',
        'attributes'            => 'Item Attributes',
        'parent_item_colon'     => 'Parent Item:',
        'all_items'             => 'All LF Slider images',
        'add_new_item'          => 'Add New LF Slider Image',
        'add_new'               => 'Add New',
        'new_item'              => 'New Image',
        'edit_item'             => 'Edit Image',
        'update_item'           => 'Update Image',
        'view_item'             => 'View Image',
        'view_items'            => 'View Images',
        'search_items'          => 'Search Images',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into item',
        'uploaded_to_this_item' => 'Uploaded to this item',
        'items_list'            => 'Items list',
        'items_list_navigation' => 'Items list navigation',
        'filter_items_list'     => 'Filter items list',
    );
    $args = array(
        'label'                 => 'LF Slider image',
        'description'           => 'Image slider for the home page',
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'lf_slider_image', $args );

}
add_action( 'init', 'lf_slider', 0 );







// Add Shortcode
function lf_slider_shortcode( $atts , $content = null ) {
    $query = new WP_Query(array(
        'post_type' => 'lf_slider_image',
        'posts_per_page' => 999999,
        'post_status' => 'publish'
    ));

    echo '<div id="cycler">';
    echo "<div class='slidecontainer'>";
    echo '<div class="slider_text"><p>';
    echo str_replace(",", "<br>", $content);
    echo '</p></div></div>';
    $x = 0;
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $active = ' ';
        $smallimg = get_post_meta($post_id, 'second_featured_img');
        $smallimg = wp_get_attachment_image_src($smallimg['0'], 'full');
        if ($x == 0) {
            $active = "active";
        }
        $x++;
        $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), "full");
        echo "<div class='holdslide ".$active."'>";
        echo "<div class='slbig slideimg' style='background-image:url(".$thumbnail['0'].")'></div>";
        echo "<div class='slsmall slideimg' style='background-image:url(".$smallimg['0'].")'></div>";
        echo "</div>";
    }
    echo '</div>';
    wp_reset_query();
}
add_shortcode( 'lfslider', 'lf_slider_shortcode' );







function misha_include_myuploadscript() {
    /*
     * I recommend to add additional conditions just to not to load the scipts on each page
     * like:
     * if ( !in_array('post-new.php','post.php') ) return;
     */
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/assets/js/admin-custom-js.js', array('jquery'), null, false );
}

add_action( 'admin_enqueue_scripts', 'misha_include_myuploadscript' );






/*
 * @param string $name Name of option or name of post custom field.
 * @param string $value Optional Attachment ID
 * @return string HTML of the Upload Button
 */
function misha_image_uploader_field( $name, $value = '') {

    $image = ' button">Upload image';
    $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
    $display = 'none'; // display state ot the "Remove image" button

    if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {

        // $image_attributes[0] - image URL
        // $image_attributes[1] - image width
        // $image_attributes[2] - image height

        $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
        $display = 'inline-block';

    }

    return '
    <div>
        <a href="#" class="misha_upload_image_button' . $image . '</a>
        <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
        <a href="#" class="misha_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
    </div>';

}






/*
 * Add a meta box
 */
function misha_meta_box_add() {
    add_meta_box('mishadiv', // meta box ID
        'More settings', // meta box title
        'misha_print_box', // callback function that prints the meta box HTML
        'lf_slider_image', // post type where to add it
        'normal', // priority
        'high' ); // position
}
add_action( 'admin_menu', 'misha_meta_box_add' );






/*
 * Meta Box HTML
 */
function misha_print_box( $post ) {
    $meta_key = 'second_featured_img';
    echo misha_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
}






// * Save Meta Box data
function misha_save( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;

    $meta_key = 'second_featured_img';

    update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );

    // if you would like to attach the uploaded image to this post, uncomment the line:
    // wp_update_post( array( 'ID' => $_POST[$meta_key], 'post_parent' => $post_id ) );

    return $post_id;
}
add_action('save_post', 'misha_save');







//checks if user has at least 7 photos uploaded
if(!function_exists('user_has_at_least_7_photos')) {

    function user_has_at_least_7_photos($user_id)
    {

        if (class_exists('UM_Gallery_Pro')) {

            //loop albums
            if ($albums = um_gallery_by_userid($user_id)) {
                foreach ($albums as $album) {
                    //user has 6 photos and named the album
                    if ($album->total_photos > 5 && $album->album_name && $album->album_name != 'Untitled Album') {
                        return true;
                    }
                }
            }

            return false;

        }

        return false;

    }

}






//checks if user has at least 7 photos uploaded has uploaded cover photo and  has created gallery
if(!function_exists('user_has_completed_profile')) {

    function user_has_completed_profile()
    {

        //exit if um plugin not installed
        if (class_exists('UM_Gallery_Pro')) {

            //get user id and meta
            $u_id = get_current_user_id();
            $u_meta = get_user_meta($u_id);

            //user has completed profile
            if (!empty($u_meta['description'][0]) && !empty($u_meta['cover_photo'][0]) && sizeof(um_gallery_by_userid($u_id)) > 0 && user_has_at_least_7_photos($u_id)) {
                return true;
            }

            return false;
        }

        return false;

    }

}






//hide red edit banner on session when close button is clicked
function hide_edit_banner(){

    $_SESSION["hide_red_edit_banner"] = "true";

    echo "true";
    die();

}
add_action( 'wp_ajax_hide_edit_banner', 'hide_edit_banner' );
add_action( 'wp_ajax_nopriv_hide_edit_banner', 'hide_edit_banner' );






//filters output on collection page
function swph_ultimatememberfilterheader(){

    $output = '';
    $tags = get_terms(
        array(
            'taxonomy' => 'tags',
            'hide_empty' => false,
        )
    );


    if($tags) :

        $output .= '<div class="collection-filters">';
        $output .= '<div class="collection-filters-header">';
        $output .= '<span class="pull-right collection-search-container"><input name="collection_search" id="collection_search" type="text" placeholder="Search" /></span>';
        $output .= '<span class="pull-left collection-tags-toggle">Tags <i class="fa fa-caret-down"></i></span>';
        $output .= '</div>';
        $output .= '<div class="collection-filters-tags">';

        $output .= '<div class="collection-tag"><input name="user_tag" id="all" value="all" type="radio"/><label for="all" class="collection-filter-label collection-filter-all">All</label></div>';
        foreach($tags as $tag) :
            $output .= '<div class="collection-tag"><input name="user_tag" id="'.$tag->slug.'" value="'.$tag->slug.'" type="radio"/><label for="'.$tag->slug.'" class="collection-filter-label">'.$tag->name.'</label></div>';
        endforeach;

        $output .= '</div>';
        $output .= '</div>';

    endif;

    return $output;

}
add_shortcode('swph_ultimatememberfilterheader', 'swph_ultimatememberfilterheader');






//collection filter ajax
//function wpsh_collection_filter(){
//
//    $tag_slug = '';
//    $stringSearch = '';
//
//
//    if(isset($_GET['tagSlug'])){
//        $tag_slug = $_GET['tagSlug'];
//    }
//
//    if(isset($_GET['stringSearch'])){
//        $stringSearch = $_GET['stringSearch'];
//    }
//
//
//    echo do_shortcode('[swph_ultimatemember stringSearch="'.$stringSearch.'" tag="'.$tag_slug.'" limit="21" users="9587,7795,8155,8132,8791,build,5109,3824,5968,8268,7385,8659,9586,9550,7077,9554,5036,8757,9284,8210,7926,9409,9040,7924,8163,6001,2348,8581,6075,6333,7252,8422,8416,6685,7932,8217,7878,7071,7956,8304,4371,8235,4957,2426,7531,6564,3528,6980,7972,1353,8106,8059,4557,6642,3754,3802,5694,7876,6865,7104,2638,6973,7200,1945,80,6229,1350,7445,4300,7382,6616,1787,745,4228,6721,7436,196,6662,6563,3386,4426,7433,952,6956,3407,89,6778,3083,5720,3212,6957,1645,6383,1166,972,5339,1626,1499,5176,1099,4738,3900,5837"]');
//
//    die();
//
//}
//add_action( 'wp_ajax_wpsh_collection_filter', 'wpsh_collection_filter' );
//add_action( 'wp_ajax_nopriv_wpsh_collection_filter', 'wpsh_collection_filter' );







//add display name to wp user search query
add_filter( 'user_search_columns', function( $search_columns ) {

    $search_columns[] = 'display_name';
    return $search_columns;

});







////collection autocomplete ajax
//function wpsh_collection_autocomplete(){
//
//    if ( isset($_GET['users']) ) { $users = $_GET['users']; $users = json_decode( stripslashes($users) );  $users = $users->users;  }
//    $users = str_replace( ',build' , '' , $users );
//    $term = $_GET['term'];
//    $suggestions = array();
//
//
//    $args = array(
//        'include'           => $users,
//        'meta_query'        => array(
//            'relation'  => 'AND',
//            array(
//                'relation' => 'OR',
//                array(
//                    'key'     => 'first_name',
//                    'value'   => $term,
//                    'compare' => 'LIKE'
//                ),
//                array(
//                    'key'     => 'last_name',
//                    'value'   => $term,
//                    'compare' => 'LIKE'
//                ),
//                array(
//                    'key'     => 'nickname',
//                    'value'   => $term,
//                    'compare' => 'LIKE'
//                ),
//            ),
//            array(
//                'key'     => 'account_status',
//                'value'   => 'approved',
//                'compare' => '='
//            ),
//        ),
//        'number'    => 21,
//    );
//    $user_query = new WP_User_Query($args);
//
//
//    if( !empty ($user_query->get_results()) ) :
//
//        foreach ($user_query->get_results() as $user){
//            $suggestion = array();
//            $suggestion['label'] = $user->first_name;
//            $suggestions[] = $suggestion;
//
//        }
//
//        foreach ($user_query->get_results() as $user){
//            $suggestion = array();
//            $suggestion['label'] = $user->display_name;
//            $suggestions[] = $suggestion;
//
//        }
//
//    endif;
//
//
//    $response = json_encode( $suggestions );
//    echo $response;
//    exit();
//
//}
//add_action( 'wp_ajax_wpsh_collection_autocomplete', 'wpsh_collection_autocomplete' );
//add_action( 'wp_ajax_wpsh_collection_autocomplete', 'wpsh_collection_autocomplete' );