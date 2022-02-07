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

require_once get_stylesheet_directory().'/includes/shortcodes.php';

require_once get_stylesheet_directory().'/includes/lf-entry-process.php';
require_once get_stylesheet_directory().'/includes/lf-entry-series-award.php';

require_once get_stylesheet_directory().'/includes/class-avada-nav-walker-megamenu.php';




function lf_profile_header($args) {
    remove_action('um_profile_header', 'um_profile_header', 9);
    add_action('um_profile_header', 'my_um_profile_header', 9);
}
add_action('init', 'lf_profile_header');


//facebook open graph meta start
function remove_default_um_meta_desc(){
    remove_action('wp_head', 'um_profile_dynamic_meta_desc', 9999999);

}
add_action('wp_head', 'remove_default_um_meta_desc');


function change_yoast_meta_title_specific_page ( $myfilter ) {
    if(is_page(8176)){
        um_fetch_user( um_get_requested_user() );
        $user_id = um_user('ID');
        $myfilter =  um_get_display_name( $user_id ).' - Photographer';
        return $myfilter;
    }
    return $myfilter;
}
add_filter( 'wpseo_opengraph_title', 'change_yoast_meta_title_specific_page' );







function my_own_og_function() {
    $image = '';
    if(is_page(8176)){
        um_fetch_user( um_get_requested_user() );
        if ( um_profile('cover_photo') ) {
            $image = um_get_cover_uri( um_profile('cover_photo'), null );
        } else if( um_profile('synced_cover_photo') ) {
            $image = um_profile('synced_cover_photo');
        }else{
            $image = um_get_default_cover_uri();
        }
    }

    // echo $image;

    $GLOBALS['wpseo_og']->image_output( $image ); // This will echo out the og tag in line with other WPSEO og tags
}
add_action( 'wpseo_opengraph', 'my_own_og_function', 9999 );







function change_yoast_meta_url_specific_page ( $myfilter ) {
    if(is_page(8176)){
        um_fetch_user( um_get_requested_user() );
        $myfilter = um_user_profile_url();
        return $myfilter;
    }
    return $myfilter;
}
add_filter( 'wpseo_opengraph_url', 'change_yoast_meta_url_specific_page' );





function change_yoast_meta_desc_specific_page ( $myfilter ) {
    if(is_page(8176)){
        $myfilter = 'The Collection is curated by our editors and provides a platform to celebrate more diverse photographic stories.';
        return $myfilter;
    }
    return $myfilter;
}
add_filter( 'wpseo_opengraph_desc', 'change_yoast_meta_desc_specific_page' );
//facebook open graph meta end






function lf_profile_header_cover_area($args) {
    remove_action('um_profile_header_cover_area', 'um_profile_header_cover_area', 9);
    add_action('um_profile_header_cover_area', 'my_um_profile_header_cover_area', 9);
}
// add_action('init', 'lf_profile_header_cover_area');


/*
 * Profile Cover header - Add vertical line and user information
 */
add_action( 'um_cover_area_content', 'lf_cover_area_content', 10, 1 );
function lf_cover_area_content( $user_id ) {
    ?>
    <div class="profile-header-cover">
            <div class="profile-header-cover__caption profile-header-cover__caption--big"><a style="color:#000;" href="<?php echo um_user_profile_url(); ?>" title="<?php echo um_user('display_name'); ?>"><?php echo um_user('display_name', 'html'); ?></a></div>
            <div class="profile-header-cover__line">
                <img src="<?php echo home_url();?>/wp-content/uploads/2016/09/Black-line-1.png" width="6" height="" class="alignnone size-full wp-image-13904"/>
            </div>
            <div class="profile-header-cover__caption profile-header-cover__caption--small profile-header-cover__caption--underline">Collection</div>

        </div>
<?php
}




function lf_add_edit_icon($args) {
    remove_action('um_pre_header_editprofile', 'um_add_edit_icon');
    add_action('um_pre_header_editprofile', 'my_um_add_edit_icon');
}
add_action('init', 'lf_add_edit_icon');




//remove_action('wp_head' , 'insert_og_meta');




/* * *
 * **   @um_add_edit_icon
 * * */

function my_um_add_edit_icon($args) {
    $output = '';

    if (!is_user_logged_in())
        return; // not allowed for guests

    if (isset(UM()->user()->cannot_edit) && UM()->user()->cannot_edit == 1)
        return; // do not proceed if user cannot edit

    if (UM()->fields()->editing != true) {
        wp_redirect(um_edit_profile_url());
        exit;
        }
    }


        /*         * *
         * **   @profile header
         * * */

        function my_um_profile_header($args) {
            $classes = null;

            if (!$args['cover_enabled']) {
                $classes .= ' no-cover';
            }
            // $default_size = str_replace('px', '', $args['photosize']);
            $default_size = 120;

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

            <a class="um-profile-photo-img" title="<?php echo um_user('display_name'); ?>"><?php echo $overlay . get_avatar(um_profile_id(), $default_size); ?></a>

    <?php
    if (!isset(UM()->user()->cannot_edit)) {

        UM()->fields()->add_hidden_field('profile_photo');

        if (!um_profile('profile_photo')) { // has profile photo
            $items = array(
                '<a href="#" class="um-manual-trigger" data-parent=".um-profile-photo" data-child=".um-btn-auto-width">' . __('Upload photo', 'ultimatemember') . '</a>',
                '<a href="#" class="um-dropdown-hide">' . __('Cancel', 'ultimatemember') . '</a>',
            );

            $items = apply_filters('um_user_photo_menu_view', $items);

            echo UM()->profile()->new_ui('bc', 'div.um-profile-photo', 'click', $items);
        } else if (UM()->fields()->editing == true) {

            $items = array(
                '<a href="#" class="um-manual-trigger" data-parent=".um-profile-photo" data-child=".um-btn-auto-width">' . __('Change photo', 'ultimatemember') . '</a>',
                '<a href="#" class="um-reset-profile-photo" data-user_id="' . um_profile_id() . '" data-default_src="' . um_get_default_avatar_uri() . '">' . __('Remove photo', 'ultimatemember') . '</a>',
                '<a href="#" class="um-dropdown-hide">' . __('Cancel', 'ultimatemember') . '</a>',
            );

            $items = apply_filters('um_user_photo_menu_edit', $items);

            echo UM()->profile()->new_ui('bc', 'div.um-profile-photo', 'click', $items);
        }
    }
    ?>

        </div>

        <div class="profile-header__meta um-profile-meta">

            <div class="um-main-meta">
                <div class="sub-artist">Photographer</div>
                <?php
                // echo '<pre style="text-align: left;">'. print_R($args, 1). '</pre>';
                ?>
            <?php if ($args['show_name']) { ?>
                    <div class="um-name">

                        <?php
                        echo um_user('display_name', 'html');
                        // $userdata = get_userdata( um_profile_id() );
                        // echo $userdata->display_name;
                        ?>

                <?php do_action('um_after_profile_name_inline', $args); ?>

                    </div>
            <?php } ?>

                <div class="um-clear"></div>

            <?php do_action('um_after_profile_header_name_args', $args); ?>
            <?php do_action('um_after_profile_header_name'); ?>

            </div>

            <?php if (isset($args['metafields']) && !empty($args['metafields'])) { ?>
                <div class="um-meta">

                <?php
                $profile_metas = $args['metafields'];

                //todo nick: check where metafields element is set

                $facebook = get_user_meta(um_profile_id(), 'social_facebook_username', true);
                if ($facebook) {
                    echo '<a href="https://facebook.com/' . $facebook . '" title="Facebook" target="_blank" rel="nofollow" class="social-user-link">Facebook</a>';
                }

                $instagram = get_user_meta(um_profile_id(), 'social_instagram_username', true);
                if ($instagram) {
                    echo '<a href="https://instagram.com/' . $instagram . '" title="Instagram" target="_blank" rel="nofollow" class="social-user-link">Instagram</a>';
                }

                $website = get_user_meta(um_profile_id(), 'social_website_custom', true);
                if ($website) {
                    echo '<a href="' . $website . '" title="Website" target="_blank" rel="nofollow" class="social-user-link">Website</a>';
                }


//                foreach($profile_metas as $meta_key) {
//                    if (in_array($meta_key, ['facebook', 'instagram'])) {
//                        // ignore facebook and instagram urls
//                        continue;
//                    }
//
//                    $meta_key = str_replace('social_', '', $meta_key);
//                    $meta_value = get_user_meta(um_profile_id(), 'social_'.$meta_key, true);
//                    if ($meta_key == 'instagram_username' && $meta_value) {
//                        $meta_key = 'Instagram';
//                        $meta_value = 'https://instagram.com/' . $meta_value;
//                    }
//                    if($meta_value !== ''){
//                        $title = ucfirst(str_replace('social_', '', $meta_key));
//                        if (strpos($meta_value,'http://') === false && strpos($meta_value,'https://') === false) {
//                            $meta_value = 'http://'.$meta_value;
//                        }
//                         echo '<a href="'.$meta_value.'" title="'.$title.'" target="_blank" rel="nofollow" class="social-user-link">'. $title . '</a>';
//                    }
//                }

                ?>
                </div>
            <?php } ?>

    <?php if (UM()->fields()->viewing == true && um_user('description') && $args['show_bio']) { ?>

            <div class="um-meta-text" style="text-align: left;">
                <?php
                $description = get_user_meta(um_user('ID'), 'description', true);

                if (UM()->options()->get('profile_show_html_bio')) :

                    echo make_clickable(wpautop(wp_kses_post($description))); ?>

                <?php else : ?>

                    <?php echo nl2br($description); ?>

                <?php endif; ?>
            </div>

    <?php } else if (UM()->fields()->editing == true && $args['show_bio']) {
            $user_desc = trim(get_user_meta(um_user('ID'), 'description', true));
        ?>
            <div class="um-meta-text">
                <textarea id="um-meta-bio" data-user-id="<?php echo um_user('ID');?>" data-character-limit="<?php echo UM()->options()->get('profile_bio_maxchars'); ?>" placeholder="<?php _e('Tell us a bit about yourself...', 'ultimatemember'); ?>" name="<?php echo 'description-' . $args['form_id']; ?>" id="<?php echo 'description-' . $args['form_id']; ?>"><?php echo $user_desc; ?></textarea>
                <span class="um-meta-bio-character um-right"><span class="um-bio-limit"><?php echo UM()->options()->get('profile_bio_maxchars'); ?></span></span>
                <span data-ns-res style="font-size: 12px;"></span>
                <?php
                if (UM()->fields()->is_error('description')) {
                    echo UM()->fields()->field_error(UM()->fields()->show_error('description'), true);
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
        if (UM()->fields()->is_error('profile_photo')) {
            echo UM()->fields()->field_error(UM()->fields()->show_error('profile_photo'), 'force_show');
        }
        ?>

        <?php do_action('um_after_header_info', um_user('ID'), $args); ?>

    </div>

    <?php
}








// * Load external scripts
function theme_gsap_script() {
    wp_enqueue_script('bootstrap-js', '/wp-content/themes/Avada-Child-Theme/assets/js/bootstrap.min.js', array(), false, true);
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
    wp_localize_script( 'custom-js', 'my_ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    
    //localize
    if(function_exists('um_user_profile_url')) {
        wp_localize_script( 'custom-js', 'um_var', array( 
                'um_user_profile_url' => um_user_profile_url(),
            ));
    }
    
    /* bootstrap fix
     * somehow the avada theme calls it twice
     * one min one not min
     */
    wp_deregister_script( 'bootstrap' );
    wp_dequeue_script( 'bootstrap' );
    wp_deregister_script( 'um_old_css' );
    wp_dequeue_script( 'um_old_css' );

}
add_action('wp_enqueue_scripts', 'theme_gsap_script');







function theme_enqueue_styles() {
    wp_enqueue_style('avada-parent-stylesheet', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('animate-stylesheet', '//cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ,'avada-parent-stylesheet') );
    wp_enqueue_style('boostrap-css', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css' );
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

    exit;
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
    exit;
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
function swph_get_members($atts="0", $jatts = "0", $page = "1" ) {

    extract( shortcode_atts( array(
    'users' => null,
    'tag'   => null,
    'stringsearch' => null,
    'limit' => 21,
), $atts ) );


$args = array(
    'meta_key' => 'account_status',
    'meta_value' => 'approved',
);


if(isset($_GET['jatts']) && $_GET['jatts']){
    $users = $_GET['jatts'];
}


//$page = $limit * $page;

$admin = new WP_User_Query( array( 'role' => 'Administrator' ) );
$admin = $admin->results['0']->ID;
$users = str_replace("build",$admin, $users);
$asst = get_stylesheet_directory_uri();

$users = explode(',', $users);


if(!is_null($users)){
    $args['include'] = $users;
    $args['orderby'] = 'include';
    $args['order'] = 'ASC';
    $args['number'] = $limit;
    $args['paged'] = $page;
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


        $search_list[] = $user_meta['first_name'][0];
        $search_list[] = $user_meta['last_name'][0];
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
            $content .= "<div class='col-sm-4 col-xs-6 col-user' data-search='".$data_search_list."'  data-terms='".$terms_list."'>";
                $content .= "<div class='ns-collection__box'>";

                    $content .= "<a href='" . $homeurl . "/profile-features' title='Share Your Work'>";
                        $content .= "<img class='lazyload' data-src='" . $asst . "/assets/img/profile-features.jpg' alt=''>";
                    $content .= "</a>";

                    $content .= "<div class='um-member-card no-photo 000'>";

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
            if ( file_exists( UM()->files()->upload_basedir . $user->ID . '/'.$image ) ) {
                $resized = image_make_intermediate_size( UM()->files()->upload_basedir . $user->ID . '/'.$image, 450, 300, true );
                if( is_ssl() ){
                    UM()->files()->upload_baseurl = str_replace("http://", "https://",  UM()->files()->upload_baseurl );
                }
                $base_url = UM()->files()->upload_baseurl . $user->ID . '/';
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
            // $permalink = new Permalinks;
            $permalinks_class = 'um\core\Permalinks';
            $permalink = new $permalinks_class();
            $user_slug = $permalink->profile_slug($user->display_name, $user->first_name, $user->last_name);
            $content .= "<div class='col-sm-4 col-xs-6 col-user' data-search='".$data_search_list."' data-terms='".$terms_list."'>";
                $content .= "<div id='".$user->ID."' class='ns-collection__box'>";

                    $content .= "<a href='" . $homeurl . "/photographer/" . $user_slug . "/' title='" . $user->display_name."'>";
                        $content .= "<img src='" . $uri . "' alt=''>";
                    $content .= "</a>";

                    $content .= "<div class='um-member-card no-photo 010'>";

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
        $content .= "<div id='data-search' data-search='".$search_list."'></div>";
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


    $atts = ( shortcode_atts( array(
        'users' => null,
        'tag'   => null,
        'limit'   => 21,
        'stringsearch'   => null,
    ), $atts ) );

    $atts['all_users'] = $atts['users'];



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

        global $wpdb;
        $users = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE display_name = '".$atts['stringsearch']."'");

        $users_array = [];
        foreach($users as $user){

            $users_array[] = $user->ID;
        }

        $atts['users'] = implode(',' , $users_array );

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
                            'key'     => 'full_name',
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
$userno = array_unique($userno);
$userno = count($userno);

// $output = "<input type='hidden' data-usercount='".$userno."' id='data_user_count'>";

$output = "<input type='hidden' value='".$userno."' id='data_user_count'><div class='ns-collection um-form' data-limit='' data-users='".json_encode($atts['all_users'])."'><div class='row'>" . $output . "</div>";


$limit = $atts['limit'];
//show load more button if more then 21 users
if(!is_home() && !is_front_page()){
    $output .= '<div class="row"><div class="col-md-12"><p class="col-md-12 text-center profiles-information">You have discovered <span class="loaded-profiles"> </span> of <span class="total-profiles"> </span> profiles</p></div></div>';
    $percent = $limit*100/$userno;
    $output .= '<div class="row"><div class="col-md-12">
    <div class="collection-progress">
    <div class="collection-progressbar" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$percent.'%">
   
    </div>
  </div>
    </div></div>';
}

if($userno > $limit){

    $output .= "<div class='filter-loading-loadmore'>";
    $output .= "<div class='fusion-loading-container' style='margin-top:10px;margin-bottom:10px;'>";
    $output .=  "<div class='fusion-loading-spinner'><div class='fusion-spinner-1'></div><div class='fusion-spinner-2'></div><div class='fusion-spinner-3'></div></div>";
    $output .= "</div>";
    $output .= "</div>";

    $output .= "<div class='row'><div class='butrow text-center'><button id='loadmore' data-count='".$userno."' data-page='1' data-users='".json_encode($atts['users'])."'  data-limit='".$limit."'   class='fusion-button button-flat fusion-button-round button-large button-custom home_cta'>Load more...</button></div>";
}
else{
    $output .= '<div class="collection-spacer"></div>';
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




function box_image_top_text_shortcode($atts = array())
{
    extract(shortcode_atts([
        'link' => '',
        'src' => '',
        'head_text' => '',
        'paragraph_text' => '',
    ], $atts));

    $output = '
        <div class="ns-box-description-top">
            <img class="size-full" src="' . $src . '" />
            <a href="' . $link . '">
                <div class="ns-box-top-text-container">
                    <p class="ns-box-title">' . $head_text . '</p>
                    <p class="ns-box-description">' . html_entity_decode($paragraph_text) . '</p>
                </div>
            </a>
        </div>
    ';

    return $output;
}
add_shortcode('ns_box_image_top_text', 'box_image_top_text_shortcode');

//function box_image_top_right_text_shortcode($atts = array())
//{
//    extract(shortcode_atts([
//        'link' => '',
//        'src' => '',
//        'head_text' => '',
//        'paragraph_text' => '',
//        'corner_text' => '',
//    ], $atts));
//
//    if ($corner_text) {
//        $corner_text = '
//            <div class="corner-text">
//                <p>' . $corner_text . '</p>
//            </div>
//        ';
//    }
//    $content = $corner_text . '
//        <div class="ns-full-box-bottom-text-container">
//            <p class="ns-box-title">' . $head_text . '</p>
//            <p class="ns-box-description">' . html_entity_decode($paragraph_text) . '</p>
//        </div>
//    ';
//    if ($link) {
//        $content = "
//            <a href='{$link}'>
//                {$content}
//            </a>
//        ";
//    }
//
//    $output = '
//        <div class="ns-full-box-description-top-right">
//            <img class="size-full" src="' . $src . '" />
//            <a href="' . $link . '">
//                ' . $corner_text . '
//                <div class="ns-full-box-bottom-text-container">
//                    <p class="ns-box-title">' . $head_text . '</p>
//                    <p class="ns-box-description">' . html_entity_decode($paragraph_text) . '</p>
//                </div>
//            </a>
//        </div>
//    ';
//
//    return $output;
//}
//add_shortcode('ns_box_image_top_right_text', 'box_image_top_right_text_shortcode');

function lf_register_fusion_elements()
{
    fusion_builder_map(
        [
            'name'            => 'Life Framer Event',
            'shortcode'       => 'lf_event',
            'icon'            => 'fusiona-list-alt',
//            'preview'         => PLUGIN_DIR . 'js/previews/fusion-text-preview.php',
//            'preview_id'      => 'fusion-builder-block-module-text-preview-template',
            'allow_generator' => true,
            'params'          => [
                [
                    'type'         => 'upload',
                    'heading'      => 'Image',
                    'description'  => 'Upload an image to display.',
                    'param_name'   => 'image',
                    'value'        => '',
                    'dynamic_data' => true,
                ],
                [
                    'type'        => 'textfield',
                    'heading'     => 'Head text',
                    'description' => 'Heading text.',
                    'param_name'  => 'head_text',
                    'value'       => '',
                ],
                [
                    'type'        => 'textfield',
                    'heading'     => 'First paragraph',
                    'description' => '',
                    'param_name'  => 'paragraph_one',
                    'value'       => '',
                ],
                [
                    'type'        => 'textfield',
                    'heading'     => 'Second paragraph',
                    'description' => '',
                    'param_name'  => 'paragraph_two',
                    'value'       => '',
                ],
                [
                    'type'        => 'radio_button_set',
                    'heading'     => 'Text box vertical alignment',
                    'description' => '',
                    'param_name'  => 'text_box_vertical_alignment',
                    'value'       => [
                        'top'     => 'Top',
                        'bottom'  => 'Bottom',
                    ],
                ],
                [
                    'type'        => 'radio_button_set',
                    'heading'     => 'Text box horizontal alignment',
                    'description' => '',
                    'param_name'  => 'text_box_horizontal_alignment',
                    'value'       => [
                        'left'     => 'Left',
                        'center'   => 'Center',
                        'right'    => 'Right',
                    ],
                    'default' => 'left',
                ],
                [
                    'type'        => 'radio_button_set',
                    'heading'     => 'Text box vertical alignment',
                    'description' => '',
                    'param_name'  => 'text_box_vertical_alignment',
                    'value'       => [
                        'top'     => 'Top',
                        'bottom'  => 'Bottom',
                    ],
                    'default' => 'top',
                ],
                [
                    'type'        => 'dimension',
                    'heading'     => 'Text box margins',
                    'description' => '',
                    'param_name'  => 'text_box_margins',
                    'value'       => '0px 0px 0px 0px',
                ],
                [
                    'type'        => 'textfield',
                    'heading'     => 'Corner text',
                    'description' => 'Corner text.',
                    'param_name'  => 'corner_text',
                    'value'       => '',
                ],
                [
                    'type'        => 'radio_button_set',
                    'heading'     => 'Corner text alignment',
                    'description' => '',
                    'param_name'  => 'corner_text_alignment',
                    'value'       => [
                        'top-left'      => 'Top left',
                        'top-right'     => 'Top right',
                        'bottom-left'   => 'Bottom left',
                        'bottom-right'  => 'Bottom right',
                    ],
                    'default' => 'top-left',
                ],
                [
                    'type'        => 'colorpickeralpha',
                    'heading'     => 'Corner text background color',
                    'description' => '',
                    'param_name'  => 'corner_text_bg_color',
                    'value'       => '#f64e41',
                    'default'     => '#f64e41',
                ],
                [
                    'type'        => 'textfield',
                    'heading'     => 'Link',
                    'description' => 'Block link',
                    'param_name'  => 'link',
                    'value'       => '',
                ],
                [
                    'type'        => 'textfield',
                    'heading'     => 'Copyright text',
                    'description' => 'Copyright text that appears under the image',
                    'param_name'  => 'copyright',
                    'value'       => '',
                ]
            ],
        ]
    );

    fusion_builder_map(
        [
            'name'            => 'LF Event Expander',
            'shortcode'       => 'lf_event_expander',
            'icon'            => 'fusiona-list-alt',
            'allow_generator' => true,
            'params'          => [
                [
                    'type'        => 'textfield',
                    'heading'     => 'Expander identifier',
                    'description' => 'Has to be unique per page, not visible to users.',
                    'param_name'  => 'expander_id',
                    'value'       => '',
                ],
                [
                    'type'        => 'textfield',
                    'heading'     => 'Trigger text',
                    'description' => '',
                    'param_name'  => 'trigger_text',
                    'value'       => '',
                ],
                [
                    'type'        => 'tinymce',
                    'heading'     => 'Button',
                    'description' => 'Optional button',
                    'param_name'  => 'element_content',
                    'value'       => '',
                ],
            ],
        ]
    );

    fusion_builder_map(
        [
            'name'            => 'LF Event Expanded',
            'shortcode'       => 'lf_event_expanded',
            'icon'            => 'fusiona-list-alt',
            'allow_generator' => true,
            'params'          => [
                [
                    'type'        => 'textfield',
                    'heading'     => 'Expander identifier',
                    'description' => 'This is linking to an expander. Specify the id of an already defined LF Event Expander',
                    'param_name'  => 'expander_id',
                    'value'       => '',
                ],
            ],
        ]
    );
}
add_action( 'fusion_builder_before_init', 'lf_register_fusion_elements' );

function lf_event($atts)
{
    extract(shortcode_atts([
        'link' => '',
        'image' => '',
        'head_text' => '',
        'paragraph_one' => '',
        'paragraph_two' => '',
        'corner_text' => '',
        'corner_text_bg_color' => '#f64e41',
        'text_box_horizontal_alignment' => 'left',
        'text_box_vertical_alignment' => 'top',
        'text_box_margins' => '',
        'corner_text_alignment' => 'top-left',
        'copyright' => '',
    ], $atts));
    [$marginTop, $marginRight, $marginBottom, $marginLeft] = explode(' ', $text_box_margins);

    if (trim($corner_text)) {
        $horizontal = 'left';
        $vertical = 'top';
        if (in_array($corner_text_alignment, ['top-right', 'bottom-right'])) {
            $horizontal = 'right';
        }
        if (in_array($corner_text_alignment, ['bottom-left', 'bottom-right'])) {
            $vertical = 'bottom';
        }
        $corner_text = '
            <div class="lf-event-corner-text" style="' . $horizontal . ': 0; ' . $vertical . ': 0;">
                <p style="background-color: ' . $corner_text_bg_color . '">' . $corner_text . '</p>
            </div>
        ';
    }
    $verticalAlignment = ($text_box_vertical_alignment == 'bottom') ? 'bottom' : 'top';
    $wrapperMargin = '';
    if ($text_box_horizontal_alignment == 'right') {
        $horizontalAlignment = 'right';
        $horizontalOffset = 0;
        if ($verticalAlignment == 'top') {
            $margin = "{$marginTop} {$marginRight} 0 0";
            if (stripos($marginTop, '-') !== false) {
                $wrapperMargin = 'margin-top: ' . trim($marginTop, '-');
            }
        } else {
            $margin = "0 {$marginRight} {$marginBottom} 0";
            if (stripos($marginBottom, '-') !== false) {
                $wrapperMargin = 'margin-bottom: ' . trim($marginBottom, '-');
            }
        }
    } elseif ($text_box_horizontal_alignment == 'center') {
        $horizontalAlignment = 'left';
        $horizontalOffset = '50%';
        $left = ((int) str_replace('px', '', $marginLeft)) - 110;
        if ($verticalAlignment == 'top') {
            $margin = "{$marginTop} 0 0 {$left}px";
            if (stripos($marginTop, '-') !== false) {
                $wrapperMargin = 'margin-top: ' . trim($marginTop, '-');
            }
        } else {
            $margin = "0 0 {$marginBottom} {$left}px";
            if (stripos($marginBottom, '-') !== false) {
                $wrapperMargin = 'margin-bottom: ' . trim($marginBottom, '-');
            }
        }
    } else {
        $horizontalAlignment = 'left';
        $horizontalOffset = 0;
        if ($verticalAlignment == 'top') {
            $margin = "{$marginTop} 0 0 {$marginLeft}";
            if (stripos($marginTop, '-') !== false) {
                $wrapperMargin = 'margin-top: ' . trim($marginTop, '-');
            }
        } else {
            $margin = "0 0 {$marginBottom} {$marginLeft}";
            if (stripos($marginBottom, '-') !== false) {
                $wrapperMargin = 'margin-bottom: ' . trim($marginBottom, '-');
            }
        }
    }
    $randomId = rand();
    $content = $corner_text . '
        <div
            class="lf-event-box-container"
            id="lf-event-box-container-' . $randomId . '"
            style="' . $horizontalAlignment . ': ' . $horizontalOffset . '; ' . $verticalAlignment . ': 0; margin: ' . $margin . '"
        >
            <p class="lf-event-box-title">' . $head_text . '</p>
            <p class="lf-event-box-description">' . html_entity_decode($paragraph_one) . '<br/>' . html_entity_decode($paragraph_two) . '</p>
        </div>
    ';
    if ($link) {
        $content = "
            <a href='{$link}'>
                {$content}
            </a>
        ";
    }

    $output = '
        <style>
            @media only screen and (max-width: 1024px) {
                #lf-event-box-container-' . $randomId . ' {
                    ' . $horizontalAlignment . ': 50% !important;
                    margin-' . $horizontalAlignment . ': -110px !important;
                }
            }
        </style>
        <div class="lf-event-box-wrapper" style="' . $wrapperMargin . '">
            <img src="' . $image . '" />
            ' . $content . '
        </div>
        <p class="lf-event-copyright">' . $copyright . '</p>
    ';

    return $output;
}
add_shortcode('lf_event', 'lf_event');

function lf_event_expander($atts, $content)
{
    extract(shortcode_atts([
        'expander_id' => '',
        'trigger_text' => '',
    ], $atts));

    $output = sprintf(
        "
            <div class='lf-event-expander'>
                <div class='lf-event-expander-trigger-container'>
                    <p data-lf-event-expander-id='%s'><i class='fa-plus fas'></i> %s</p>
                </div>
                <div class='lf-event-expander-button-container'>
                    %s
                </div>
            </div>
        ",
        $expander_id,
        $trigger_text,
        do_shortcode($content)
    );

    return $output;
}
add_shortcode('lf_event_expander', 'lf_event_expander');

function lf_event_expanded($atts)
{
    extract(shortcode_atts([
        'expander_id' => '',
    ], $atts));

    $output = sprintf(
        "
            <div class='lf-event-expanded'>
                <i class='fa-times fas' data-lf-event-expanded-id='%s'></i>
            </div>
        ",
        $expander_id
    );

    return $output;
}
add_shortcode('lf_event_expanded', 'lf_event_expanded');

function box_image_bottom_text_shortcode($atts = array())
{
    extract(shortcode_atts([
        'link' => '',
        'src' => '',
        'head_text' => '',
        'paragraph_text' => '',
        'corner_text' => '',
    ], $atts));

    if ($corner_text) {
        $corner_text = '
            <div class="corner-text">
                <p>' . $corner_text . '</p>
            </div>
        ';
    }

    $output = '
        <div class="ns-full-box-description-bottom">
            <img class="size-full" src="' . $src . '" />
            ' . ($link ? '<a href="' . $link . '">' : '') . '
                ' . $corner_text . '
                <div class="ns-full-box-bottom-text-container">
                    <p class="ns-box-title">' . $head_text . '</p>
                    <p class="ns-box-description">' . html_entity_decode($paragraph_text) . '</p>
                </div>
            ' . ($link ? '</a>' : '') . '
        </div>
    ';

    return $output;
}
add_shortcode('ns_full_box_image_bottom_text', 'box_image_bottom_text_shortcode');



function nsTimelineShortcode($atts = [], $content = "")
{
    $atts = shortcode_atts([
        'title' => '',
    ], $atts);

    return sprintf(
        '
            <div class="timeline">
                <h2>%s</h2>
                <div class="timeline-container">
                    <div class="timeline-image">
                        <img src="%s" />
                    </div>
                    <div class="timeline-content">
                        %s
                    </div>
                </div>
            </div>
        ',
        $atts['title'],
        get_stylesheet_directory_uri() . '/assets/img/arrow-no-space.svg',
        do_shortcode($content)
    );
}
add_shortcode('ns_timeline', 'nsTimelineShortcode');


function nsLoggedInSubmitMessage()
{
    if (!is_user_logged_in()) {
        return '';
    }

    $user = get_user_by('id', get_current_user_id());
    $user_role = UM()->roles()->get_um_user_role($user->ID);
    $get_active_LF_entries_entrant = get_active_LF_entries();
    $images_to_submit = 0;
    if (!empty($get_active_LF_entries_entrant)) {
        $theme_start_date = $get_active_LF_entries_entrant[0]->start;
        $user_email = $user->user_email;
        $user_login = $user->user_login;
        /* Get images to submit */
        date_default_timezone_set('America/Los_Angeles');
        /* For entrants credit can be used 4 months after the payment */
        $limit_date = date('Y-m-d H:i:s', strtotime(' -4 months'));
        $images_to_submit = get_images_left_to_submit($user_email, $user_login, $user_role, $limit_date);

        $extra_images_to_submit = get_user_meta($user->ID,'extra_entry_imgs',true);
        if(!isset($extra_images_to_submit) || empty($extra_images_to_submit)){
            $extra_images_to_submit = 0;
        }
        $images_to_submit += $extra_images_to_submit;
    }

    if ($user_role != 'um_member' && !$images_to_submit) {
        return '';
    }

    switch ($user_role) {
        case 'um_entrant':
            $redirect_url = get_bloginfo('url').'/my-lf-entrant/';
            break;
        case 'um_member':
            $redirect_url = get_bloginfo('url').'/my-lf-member/';
            break;
//        case 'um_past':
//            $redirect_url = get_bloginfo('url').'/my-lf-past/';
//            break;
        default:
            return '';
    }

    return sprintf(
        '
            <div class="already-logged-in">
                <p>
                    You\'re already logged-in. Click 
                    <a href="%s">here</a>
                    to go to your account and submit images, or purchase more image credits below.
                </p>
            </div>
        ',
        $redirect_url
    );
}
add_shortcode('ns_logged_in_submit_message', 'nsLoggedInSubmitMessage');







//user profile
function ns_user_profile_shortcode($atts = array(), $content = ""){

    ob_start();

    // include get_stylesheet_directory().'/includes/output-shortcode-ns-user-profile.php';
    $public_profile = explode('?',um_edit_profile_url());


    echo '<a href="'.um_edit_profile_url().'">'.$public_profile['0'].'</a>';

    $output = ob_get_clean();

    return $output;
}
add_shortcode('ns_user_profile', 'ns_user_profile_shortcode');





//mandatory field
function ns_mandatory_field_shortcode($atts = array(), $content = ""){

    $output = '';
    $output .= '<span class="mandatory_field">* mandatory field</span>';
    $output .= '<label class="terms_privacy"><input type="checkbox" checked="checked" disabled="disabled">By signing up, you agree to Life Framer\'s <a href="'.get_bloginfo("url").'/legal'.'">terms and privacy</a></label>';

    return $output;
}
add_shortcode('ns_mandatory_field', 'ns_mandatory_field_shortcode');






//save "about you" section
function ns_update_user_social_links(){
// if this fails, check_admin_referer() will automatically print a "failed" page and die.
    if ( ! empty( $_POST ) && wp_verify_nonce( $_POST['_ns_update_social_security'], 'ns_update_user_social' )  ) {
           // process form data
        $user_id = $_POST['user_id'];

    //    if ( isset( $_POST["social_website"] ) && (trim($_POST['social_website'])!='') && ( (!filter_var(trim($_POST['social_website']), FILTER_VALIDATE_URL)) || (!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i',trim(strtolower($_POST['social_website'])),$matches))) ) {
    //        $result['website_error'] = 'Your Website URL has not a valid format (ex. http://www.example.com)';
    //    }
    //    else{
    //        update_user_meta($user_id,'social_website',trim(strtolower($_POST["social_website"])));
    //        $result['website_error'] = '';
    //    }
        $result['website_error'] = '';
        if (isset($_POST['social_website_custom'])) {
            $website = trim(strtolower($_POST['social_website_custom']));
            if ($website) {
                if (strpos($website, 'http') !== 0) {
                    $website = 'https://' . $website;
                }
                if (!filter_var($website, FILTER_VALIDATE_URL)) {
                    $result['website_error'] = 'Your Website URL has not a valid format (ex. https://www.example.com, www.example.com)';
                } else {
                    if (strpos($website, 'http') !== 0) {
                        $website = 'https://' . $website;
                    }
                    update_user_meta($user_id,'social_website_custom', $website);
                }
            } else {
                update_user_meta($user_id,'social_website_custom', $website);
            }
        }

//        if ( isset( $_POST['social_facebook'] ) && (trim($_POST['social_facebook'])!='') && ( (!filter_var(trim(
//                    $_POST['social_facebook']), FILTER_VALIDATE_URL)) || (!preg_match('#https?\://(?:www\.)?facebook\.com/(\d+|[A-Za-z0-9\.]+)/?#',trim(strtolower($_POST['social_facebook'])),$matches))) ) {
//            $result['facebook_error'] = 'Your Facebook URL has not a valid format (ex. http://www.facebook.com/username)';
//        }
//        else{
//            update_user_meta($user_id,'social_facebook',trim(strtolower($_POST["social_facebook"])));
//            $result['facebook_error'] = '';
//        }

        if ( isset( $_POST['social_instagram_username'] ) ) {
            update_user_meta($user_id,'social_instagram_username', filter_var(trim(trim(strtolower($_POST["social_instagram_username"]), '#@')), FILTER_SANITIZE_STRING));
            $result['instagram_error'] = '';
        }

        if ( isset( $_POST['social_facebook_username'] ) ) {
            update_user_meta($user_id,'social_facebook_username', filter_var(trim(trim(strtolower($_POST["social_facebook_username"]), '#@')), FILTER_SANITIZE_STRING));
            $result['facebook_error'] = '';
        }

    }


    $result = json_encode($result);
    echo $result;
    exit;
}
add_action( 'wp_ajax_ns_update_user_social_links', 'ns_update_user_social_links' );
add_action( 'wp_ajax_nopriv_ns_update_user_social_links', 'ns_update_user_social_links' );




//save "general" section
function ns_update_user_general_info(){
// if this fails, check_admin_referer() will automatically print a "failed" page and die.

global $wpdb;


if ( ! empty( $_POST ) && wp_verify_nonce( $_POST['_ns_update_general_security'], 'ns_update_user_general' )  ) {
       // process form data
    $user_id    = filter_input(INPUT_POST, 'user_id');
    $first_name = filter_input(INPUT_POST, 'first_name');
    $last_name  = filter_input(INPUT_POST, 'last_name');
    $email_address = trim(filter_input(INPUT_POST, "email_address"));
    $current_email = trim(filter_input(INPUT_POST, "current_email"));
    // echo '<pre>'.print_r($_POST,1).'</pre>';

    $result['firstname_error'] = $result['lastname_error'] = $result['email_error'] =  '';
    $result['error'] = false;

    if(trim($first_name) ==''){
        $result['firstname_error'] = 'You must provide your first name';
        $result['error'] = true;
    }
    else{
        update_user_meta($user_id,'first_name',$_POST["first_name"]);
    }


    if(trim($last_name) ==''){
        $result['lastname_error'] = 'You must provide your last name';
        $result['error'] = true;
    }
    else{
        update_user_meta($user_id,'last_name',$_POST["last_name"]);
    }


    if(trim($_POST['email_address']) ==''){
        $result['email_error'] = 'You must provide your email address';
        $result['error'] = true;
    }
    else{
        /* Check if email is valid */

        if(!filter_input(INPUT_POST, "email_address", FILTER_VALIDATE_EMAIL)){
            $result['email_error'] = 'Email is not valid';
            $result['error'] = true;
        }
        else{
            $email_exists = email_exists( $email_address );


            // echo 'current-email '.$current_email;

            if($current_email != $email_address){
                /* Check if email was previously user for payments aor submitions */
                $get_payment = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lf_payments WHERE  email_address = '$email_address'" );
                $get_entry = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lf_entry WHERE  email_address = '$email_address'" );
                if($get_payment || $get_entry){
                     $result['email_error'] = 'Email already used by someone else for payment or submitting images';
                     $result['error'] = true;
                }

                if ( $email_exists ) {
                    $result['email_error'] = 'Email already linked to another account';
                    $result['error'] = true;
                }

            }




        }

    }

    if($result['email_error']=='' && ($current_email != $email_address) ){
        wp_update_user( array( 'ID' => $user_id, 'user_email' => $email_address ) );

        $wpdb->update(
            "{$wpdb->prefix}lf_entry",
            array(
                'email_address' => $email_address
            ),
            /* Where statement */
            array(
                'email_address' => $current_email
            ),
            array(
                '%s'    // value2
            ),
            array(
                '%s'
            )
        );

        $wpdb->update(
            "{$wpdb->prefix}lf_payments",
            array(
                'email_address' => $email_address
            ),
            /* Where statement */
            array(
                'email_address' => $current_email
            ),
            array(
                '%s'    // value2
            ),
            array(
                '%s'
            )
        );
        $result['test'] = 'test';
    }



    }


    $result = json_encode($result);
    echo $result;
    die();
}
add_action( 'wp_ajax_ns_update_user_general_info', 'ns_update_user_general_info' );
// add_action( 'wp_ajax_nopriv_ns_update_user_general_info', 'ns_update_user_general_info' );










if( isset($_GET['update_users']) && ($_GET['update_users'] == 'update_old_statuses') ) {
    // add_action('template_redirect','update_old_statuses');
}

function update_old_statuses()
{
    $upload_dir = wp_upload_dir();
    $files = [
        'members-202109.csv',
    ];
    
    foreach ($files as $file) {
        $handle = fopen($upload_dir['basedir'] . '/' . $file, 'r');
        if (!$handle) {
            echo '<p style="color: red">Unable to read ' . $file . '</p>';
            continue;
        }
        while (($row = fgetcsv($handle)) !== false) {
            $email = $row[0];
            $user = get_user_by('email', $email);
            if (!$user) {
                echo '<p style="color: red">User with email ' . $email . ' not found!</p>';
                continue;
            }
            $user_id = $user->ID;
            UM()->roles()->set_role($user_id, 'um_past');
            echo '<p>' . $user_id . ':' . $email . '</p>';
        }
    }
//    $file = $upload_dir['basedir'].'/members-202003.csv';

//    $csv = file_get_contents($file);

//    $emails_array = explode("\n",$csv);
    // echo '<pre>'.print_r($emails_array,1).'</pre>';
    $ids_array = array();

//    $emails_array = ['dwadw@dwadwa.dwa'];
//    echo '<ol>';
//    foreach($emails_array as $email){
//        echo '<li>';
//        $user = get_user_by( 'email', $email );
//        echo '<p>Email: '.$email;
//        if($user){
//            $user_id = $user->ID;
////            $old_role = get_user_meta($user_id, 'role', true);
////            // update_user_meta($user_id,'role','past');
////            $new_role = get_user_meta($user_id, 'role', true);
////            echo '<p>Role changed from '.$old_role.' to '.$new_role;
////            um_fetch_user( 14 );
//            // Change user role
//            UM()->roles()->set_role($user_id, 'um_past');
//        }
//        else{
//            echo '<p>User not found</p>';
//        }
//        echo '<hr>';
//        echo '</li>';
//    }
//
//    echo '</ol>';

    // echo '<pre>'.print_r($emails_array,1).'</pre>';
    // echo '<pre>'.print_r($ids_array,1).'</pre>';

    // $args = array(
    //     'exclude' => $ids_array,
    //     // 'number' => 5
    //     );
    // $user_query = new WP_User_Query( $args );

    // // Get the results
    // $users = $user_query->get_results();

    // // Check for results
    // if (!empty($users)) {
    //     echo '<ol>';
    //     // loop through each author
    //     foreach ($users as $key => $user)
    //     {
    //         // get all the user's data
    //         $user_id = $user->ID;
    //         echo '<li>';
    //         echo 'User email: '  .$user->user_email;
    //         $role = strtolower( get_user_meta($user_id, 'role', true) );
    //         if( ($role =='member') || ($role =='entrant') ){
    //             update_user_meta($user_id,'role','past');
    //             $new_role = get_user_meta($user_id,'role',true);
    //             echo ' - Role changed from <b>'.$role .'</b> to <b>'. $new_role .'</b>';
    //         }
    //         else{
    //           echo ' - Role didn\'t changed, current role: '.$role;
    //         }
    //         echo '</li>';
    //     }
    //     echo '</ol>';
    // } else {
    //     echo 'No users found';
    // }

    die();

};



if( isset($_GET['update_users']) && ($_GET['update_users'] == 'add_extra_entries_test') ) {
    // add_action('template_redirect','add_extra_entries_test');
}

function add_extra_entries_test(){

    $row = 1;

    $upload_dir = wp_upload_dir();
    $file = $upload_dir['basedir'].'/member_credits.csv';

    $csv = file_get_contents($file);

    $emails_array = explode("\n",$csv);

    $ids_array = array();

    echo '<ol>';
    foreach($emails_array as $email){
        // echo '<li>';
        $user = get_user_by( 'email', $email );
        echo '<p>Email: '.$email.'</p>';

        if($user){
            $user_id = $user->ID;
            echo '<li>'.$email.'</li>';
            // $init_extra_img = get_user_meta($user_id, 'extra_entry_imgs', true);
            $init_extra_img = get_user_meta($user_id, 'extra_img_1', true);
            echo '<p> old extra_entry_imgs = '.$init_extra_img.'</p>';
            if($init_extra_img){
                echo '<p>already has extra img</p>';

            }
            $new_extra_img = $init_extra_img+10;
            echo '<p>new image nb='.$new_extra_img.'</p>';
            update_user_meta($user_id, 'extra_img_1', $new_extra_img);
            $new_extra_entry_imgs = get_user_meta($user_id, 'extra_img_1', true);
            echo '<p> new extra_entry_imgs = '.$new_extra_entry_imgs.'</p>';
           


        }
        else{
            echo '<p> user not found </p>';
        }
        echo '<hr>';
        // echo '</li>';
    }

    echo '</ol>';

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
                                <div class="initial_text_series">You are about to submit your profile and associated body of work to the Series Award. All submissions are final so please make sure that your profile is 100 percent complete and includes:<br><br>
                                - a series of 5 to 20 images<br>
                                - a series description<br>
                                - your biography/artist statement<br>
                                - your full name and surname (you can edit this along with your website and social media links on your account page)<br>
                                - a banner and profile photo of your choice<br>
                                <br>
                                Please note the Series Award is not a monthly competition but a yearly one and will not be judged until 31 October 2020, after the end of this edition of Life Framer edition VI.</div>

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
    $user_role = UM()->roles()->get_um_user_role($user_id);
    if($user_role=='um_member'){
        $usermta = get_user_meta($user_id, 'user_mail_sent', true);
        if($usermta  == 1 || validateDate($usermta))
        {
        ?>
        <div class="fusion-aligncenter txtfeedrqs"><i>Thanks for submitting your Profile for feedback. Your request has been safely received, and we will return our critique to you by email. This may take some time, but please rest assured we have not forgotten.</i></div>
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
    }
    else{ ?>
        <div class="request-feedback-disabled bg-black text-white">
            <p class="text-white mb0">Feedback is only open to Members. <a href="/my-lf-entrant#mylf-payment-table" class="text-white">Become a member now</a> and receive constructive comments.</p>
        </div>
    
    <?php }

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
<!--        <div class="fusion-aligncenter"><i>Thank you very much for your submission to the Series Award. Your submission found us well... The winner will be selected by our guest judge shortly after the end of the award and you can expect and announcement before mid-July 2018. We will contact the grand winner and honorary mentions shortly before the announcement. Thank you and good luck!</i></div>-->
        <div class="fusion-aligncenter"><i>Thank you very much for your submission to the Series Award. Your submission found us well... The winner will be selected by our guest judge shortly after the end of the award and you can expect an announcement before the end of September 2019. We will contact the grand winner and honorary mentions shortly before the announcement. Thank you and good luck!</i></div>
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
        /* Send user confirmation feedback request */
        $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
	    $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';
		$mail_headers[] = 'Content-type: text/html;charset=utf-8' . "\r\n";
        swph_send_feedback_confirmation_email($_POST["user_email"], $mail_headers ,ucfirst($_POST["user_name"]));
    }
    else {
        echo 'error';
    }
    die();
}

add_action( 'wp_ajax_send_pop_email', 'send_pop_email' );
add_action( 'wp_ajax_nopriv_send_pop_email', 'send_pop_email' );



function swph_send_feedback_confirmation_email($to, $mail_headers, $customer_name) {
	$subject = "Feedback request received!";
	$content = '<html>
		<style type="text/css">
		    @media only screen and (max-width: 780px){
		        .layoutTable{
		            display:block !important;
		            width:100% !important;
		        }
		    }
		</style>
			<body>
				<table style="margin: 0 auto; width: 80%; text-align: center; border: none; border-collapse: separate; font-size: 16px" cellpadding="0" border="0" cellspacing="0">
					<tbody>
						<tr>
							<td style="border: none;" colspan="4"><img style="width: 15%;" src="https://life-framer.com/wp-content/themes/Avada-Child-Theme/assets/img/email/LF.png"/></td>
						</tr>
						<tr style="">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr>
							<td style="border: none;" colspan="4"><h2 style="font-size: 24px; text-decoration: underline; font-weight: bold;">FEEDBACK REQUEST RECEIVED</h2></td>
						</tr>
						<tr style="">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr>
							<td style="border: none; text-align: left;" colspan="4">
								<p>Hello ' . $customer_name . ',</p>
								<p>
                                    Thank you very much for requesting feedback on the series of work uploaded to your Profile page.
		                        </p>
		                        <p>
                                    One of our team will now review your work in detail, and be in touch directly by email with their constructive critique. Note that this can take up to five weeks, and so please be patient with us.
		                        </p>
		                        <p>
								    If you have any questions or feedback, you are always welcome to contact us at <a style="color: black; text-decoration: underline;" href="mailto:info@life-framer.com">info@life-framer.com</a>.
		                        </p>
								<p>
								    Thank you very much!<br>
								    The Life Framer team.
		                        </p>
							</td>
						</tr>
						<tr style="">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr>
							<td style="border: none;" colspan="4"><h2 style="font-size: 24px; text-decoration: underline; font-weight: bold;">DISCOVER MORE<h2></td>
						</tr>
						<tr style="">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr>
							<td colspan="4">
								<table style="width: 49.5%; text-align: center; display: table-cell;"" border="0" cellpadding="10" cellspacing="0" class="layoutTable">
									<tr>
										<td style="border: none;">
											<img width="130" style="max-width: 130px" src="https://life-framer.com/wp-content/themes/Avada-Child-Theme/assets/img/email/1-the-journal.png" />
										</td>
										<td style="border: none;">
											<img width="130" style="max-width: 130px" src="https://life-framer.com/wp-content/themes/Avada-Child-Theme/assets/img/email/2-the-series-award.png" />
										</td>
									</tr>
									<tr>
										<td style="border: none;">
											<a style="font-size: 24px; text-decoration: underline; color: #F15958" href="https://www.life-framer.com/journal/">The Journal</a>
										</td>
										<td style="border: none;">
											<a style="font-size: 24px; text-decoration: underline; color: #F15958;" href="https://www.life-framer.com/competitions/">Monthly Competitions</a>
										</td>
									</tr>
									<tr>
										<td style="border: none;">
											Stories, interviews, tips and inspiration.
										</td>
										<td style="border: none;">
											Submit to our monthly calls for entries.
										</td>
									</tr>
								</table>

								<table style="width: 49.5%; text-align: center; display: table-cell;" border="0" cellpadding="10" cellspacing="0" class="layoutTable">
									<tr>
										<td style="border: none;">
											<img width="130" style="max-width: 130px" src="https://life-framer.com/wp-content/themes/Avada-Child-Theme/assets/img/email/3-the-annual.png" />
										</td>
										<td style="border: none;">
											<img width="130" style="max-width: 130px" src="https://life-framer.com/wp-content/themes/Avada-Child-Theme/assets/img/email/4-the-collection.png" />
										</td>
									</tr>
									<tr>
										<td style="border: none;">
											<a style="font-size: 24px; text-decoration: underline; color: #F15958" href="https://www.life-framer.com/past-editions/">The Annual</a>
										</td>
										<td style="border: none;">
											<a style="font-size: 24px; text-decoration: underline; color: #F15958" href="https://www.life-framer.com/collection/">The Collection</a>
										</td>
									</tr>
									<tr>
										<td style="border: none;">
											Our photobook, celebrating the award.
										</td>
										<td style="border: none;">
											Inspiring stories from our community.
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr style="">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr style="background-color: #f1f1f1;">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr style="background-color: #f1f1f1;">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr style="background-color: #f1f1f1;">
							<td style="border: none;" colspan="4">
								<a href="https://www.facebook.com/lifeframer"><img style="margin-left: 0px; margin-right: 10px;" src="https://life-framer.com/wp-content/themes/Avada-Child-Theme/assets/img/email/fb.png"/></a>


								<a href="https://www.instagram.com/life_framer"><img style="margin-left: 10px; margin-right: 0px;" src="https://life-framer.com/wp-content/themes/Avada-Child-Theme/assets/img/email/inst.png"/></a>
							</td>
						</tr>
						<tr style="background-color: #f1f1f1;">
							<td style="border: none;" colspan="4"><a style="color: #9C9C9C; text-decoration: underline" href="https://www.life-framer.com/login/">www.life-framer.com</td>
						</tr>
						<tr style="background-color: #f1f1f1;">
							<td style="border: none;" colspan="4"><p style="color: #9C9C9C;">&copy; Life Framer '.date('Y').'. All rights reserved.</p></td>
						</tr>
						<tr style="background-color: #f1f1f1;">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
					</tbody>
				</table>
			</body>
        </html>';
	wp_mail($to, $subject, $content, $mail_headers);
}

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
            <a href="<?php echo um_edit_profile_url();?>" class="fusion-button button-flat fusion-button-round button-large button-custom button-3 home_cta go-edit-profile" target="_self"><span class="fusion-button-text">Edit Profile</span></a>
        </div>
    <?php

    return ob_get_clean();
}
add_shortcode( 'go_edit_profile', 'edit_profile' );







function custom_user_profile_fields($user){
    $social_instagram  = '';
    $social_facebook  = '';
    $social_website  = '';

    if(is_object($user)) {
        $user_mail_sent = esc_attr( get_the_author_meta( 'user_mail_sent', $user->ID ) );
        $feature_mail_sent  = esc_attr( get_the_author_meta( 'feature_mail_sent', $user->ID ) );
        $series_mail_sent  = esc_attr( get_the_author_meta( 'series_mail_sent', $user->ID ) );

        $social_instagram  = esc_attr( get_the_author_meta( 'social_instagram_username', $user->ID ) );
        $social_facebook  = esc_attr( get_the_author_meta( 'social_facebook_username', $user->ID ) );
        $social_website  = esc_attr( get_the_author_meta( 'social_website_custom', $user->ID ) );
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

    <h3>Profile Social Links</h3>
    <table class="form-table">
        <tr>
            <th><label for="social_instagram_username">Instagram username</label></th>
            <td>
                <input type="text" class="large-text" name="social_instagram_username" value="<?php echo $social_instagram; ?>" id="social_instagram_username" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="social_facebook_username">Facebook username</label></th>
            <td>
                <input type="text" class="large-text" name="social_facebook_username" value="<?php echo $social_facebook; ?>" id="social_facebook_username" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="social_website_custom">Website URL</label></th>
            <td>
                <input type="text" class="large-text" name="social_website_custom" value="<?php echo $social_website; ?>" id="social_website_custom" /><br />
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

    update_user_meta($user_id, 'social_instagram_username', esc_attr(trim(strtolower($_POST['social_instagram_username']))));
    update_user_meta($user_id, 'social_facebook_username', esc_attr(trim(strtolower($_POST['social_facebook_username']))));
    update_user_meta($user_id, 'social_website_custom', esc_attr(trim(strtolower($_POST['social_website_custom']))));
}
add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');


/*
 * SWPH - Change cover photo upload message
 */
function um_change_profile_cover_photo_label_custom( $args ){
    try {
        $cover_min_w = UM()->options()->get('cover_min_width');
        $max_size = UM()->files()->format_bytes( $args['cover_photo']['max_size'] );
        list( $file_size, $unit ) = explode(' ', $max_size );
        if( $file_size < 999999999  ){
            $args['cover_photo']['upload_text'] = 'Upload profile cover <small class=\'um-max-filesize\'>('.$cover_min_w .'px minimum width, max '.$file_size.$unit.')</small>';
        }
    }
    catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    return $args;
}
remove_filter('um_predefined_fields_hook','um_change_profile_cover_photo_label',10,1);
add_filter('um_predefined_fields_hook','um_change_profile_cover_photo_label_custom',10,1);


/*
 * SWPH - Change profile photo upload message
 */

function um_change_profile_photo_label_custom( $args ){
    try {
        $max_size =  UM()->files()->format_bytes( $args['profile_photo']['max_size'] );
        $min_width =  $args['profile_photo']['min_width'] ;
        list( $file_size, $unit ) = explode(' ', $max_size );

        if( $file_size < 999999999  ){
            $args['profile_photo']['upload_text'] = 'Upload your photo <small class=\'um-max-filesize\'>('.$min_width.'px minimum width, max '.$file_size.$unit.')</small>';
        }
    }
    catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    return $args;
}
remove_filter('um_predefined_fields_hook','um_change_profile_photo_label',10,1);
add_filter('um_predefined_fields_hook','um_change_profile_photo_label_custom',10,1);






//ultimate member custom validation
function website_custom_validation( $args ) {

    if ( isset( $args['social_website'] ) && (trim($args['social_website'])!='') && ( (!filter_var(trim($args['social_website']), FILTER_VALIDATE_URL))  ) ) {
            UM()->form()->add_error( 'social_website', 'Your Website URL has not a valid format (ex. http://www.example.com)' );
    }

    if ( isset( $args['social_website_custom'] )) {
        $website = trim(strtolower($args['social_website_custom']));
        if ($website && strpos($website, 'http') !== 0) {
            $website = 'https://' . $website;
        }
        if ($website && !filter_var($website, FILTER_VALIDATE_URL)) {
            UM()->form()->add_error('social_website_custom', 'Your Website URL has not a valid format (ex. http://www.example.com, www.example.com)');
        }
    }

    global $wpdb;
    $user_email =  $args['user_email'];


    global $post;
    $slug = $post->post_name;

    if(($slug=='entrant-registration') || ($slug=='register-entrant') ){
            $payment = $wpdb->get_results("
            SELECT * FROM {$wpdb->prefix}lf_payments 
            WHERE  email_address ='$user_email' 
            AND (description = 'Life-Framer - 6 entries' 
            OR description = 'Life-Framer - 3 entries' 
            OR description = 'Life-Framer - 1 entry' ) ");
        }
        else if(($slug=='membership-registration-2') || ($slug=='register-member')){
            $payment = $wpdb->get_results("
            SELECT * FROM {$wpdb->prefix}lf_payments 
            WHERE  email_address ='$user_email' 
            AND description = 'Life-Framer - membership' ");
        }

    if ( isset( $args['user_email'] ) )  {

        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            UM()->form()->add_error( 'user_email', 'Your email address is invalid.' );
        }

        if ( email_exists( $user_email ) ) {
            UM()->form()->add_error( 'user_email', 'Your email address is already in our system. If you have already registered in the past, please log-in to your existing ‘my LF’ account using the button at the bottom of the page.' );
        }

        if ( empty($payment) ){
            UM()->form()->add_error( 'user_email', 'Your email is not recognized, please contact our support team.' );
        }



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



/* ********** Feedback Request List */

add_action('admin_action_feedback_request_list', 'feedback_request_list_action');

function feedback_request_list_action()
{
    /** Include PHPExcel */
   include("includes/PHPExcel/PHPExcel.php");
   $url = get_bloginfo('wpurl');
   $feedback = get_users(
        array(
            'meta_key' => "user_mail_sent",
            'meta_value' => "0",
            'meta_compare' => ">",
            'fields' => array ( "user_nicename", "user_email", "ID", "display_name" )
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

            $permalinks_class = 'um\core\Permalinks';
            $permalink = new $permalinks_class();
            $user_slug = $permalink->profile_slug($user->display_name, $firstname['0'], $lastname['0']);
            if($user_slug != ''){
                $newfeed['url'] = $url . "/photographer/" . $user_slug;
            }
            else{
                $newfeed['url'] = $url . "/photographer/" . $user->user_nicename;
            }

            $feedbacks[] = $newfeed;
        }
    }

    usort($feedbacks, "date_sort");


    if (!$feedbacks) die('Couldn\'t fetch records');

    /* Start Sheet */
    $feedbacksheet = new PHPExcel();
    $feedbacksheet->getProperties()->setCreator("Life-Framer")
        ->setLastModifiedBy("Life-Framer Feedback Request List")
        ->setTitle("Life-Framer Payments Feedback Request List")
        ->setSubject("Life-Framer Payments Feedback Request List")
        ->setDescription("Life-Framer Payments Feedback Request List")
        ->setKeywords("life-framer Feedback Request List")
        ->setCategory("Life-Framer");
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

    /* End array creation */

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="feedback_requests.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($feedbacksheet, 'Excel5');
    $objWriter->save('php://output');
    exit;

    exit;
}
/* ********** END Feedback Request List */


/* ********** Feature Request List */
add_action('admin_action_feature_request_list', 'feature_request_list_action');

function feature_request_list_action()
{
    /** Include PHPExcel */
    include("includes/PHPExcel/PHPExcel.php");
    $url = get_bloginfo('wpurl');

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

    if (!$features) die('Couldn\'t fetch records');

    /* Start Sheet */
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

    /* End array creation */

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="featured_requests.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($featuredsheet, 'Excel5');
    $objWriter->save('php://output');
    exit;

}
/* ********** END Feature Request List */


/* ********** Series Award Request List */
add_action('admin_action_series_award_request_list', 'series_award_request_list_action');

function series_award_request_list_action()
{
    /** Include PHPExcel */
    include("includes/PHPExcel/PHPExcel.php");
    $url = get_bloginfo('wpurl');
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

    if (!$seriess) die('Couldn\'t fetch records');

    /* Start Sheet */
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

    /* End array creation */

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="series_award_requests.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($seriessheet, 'Excel5');
    $objWriter->save('php://output');
    exit;

}


function swph_dashboard_excel() {
    ?>

    <br><h2>Feedback requested</h2>
    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
        <input type="hidden" name="action" value="feedback_request_list">
        <input type="submit" value="Feedback request list" class="button button-primary">
    </form>

    <br><h2>Featured requested</h2>
    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
        <input type="hidden" name="action" value="feature_request_list">
        <input type="submit" value="Feature request list" class="button button-primary">
    </form>

    <br><h2>Series Award requested</h2>

    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
        <input type="hidden" name="action" value="series_award_request_list">
        <input type="submit" value="Series Award request list" class="button button-primary">
    </form>

<?php

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
// add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );






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

    if(isset( $_POST[$meta_key] )){
        update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );
    }

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
            'exclude' => array( 3186, 6693 ),
        )
    );


    if($tags) :

        $output .= '<div class="collection-filters">';
        $output .= '<div class="collection-filters-header">';
        $output .= '<span class="pull-right collection-search-container"><input name="collection_search" id="collection_search" type="text" placeholder="Search" /></span>';
        $output .= '<span class="pull-left collection-tags-toggle">Tags <i class="fa fa-caret-down"></i></span>';
        $output .= '</div>';
        $output .= '<div class="collection-filters-tags">';

        $output .= '<div class="collection-tag"><input name="user_tag" id="all" value="" type="radio"/><label for="all" class="collection-filter-label collection-filter-all">All</label></div>';
        foreach($tags as $tag) :
            if($tag->slug == 'array' || $tag->slug == 'sport' || $tag->slug == 'still' || $tag->slug == 'concept'){
                continue;
            }
            $output .= '<div class="collection-tag"><input name="user_tag" id="'.$tag->slug.'" value="'.$tag->slug.'" type="radio"/><label for="'.$tag->slug.'" class="collection-filter-label">'.$tag->name.'</label></div>';
        endforeach;

        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="filter-loading">';
            $output .= '<div class="fusion-loading-container">';
                $output .=  '<div class="fusion-loading-spinner"><div class="fusion-spinner-1"></div><div class="fusion-spinner-2"></div><div class="fusion-spinner-3"></div></div>';
            $output .= '</div>';
        $output .= '</div>';

    endif;

    return $output;

}
add_shortcode('swph_ultimatememberfilterheader', 'swph_ultimatememberfilterheader');






//collection filter ajax
function swpsh_collection_filter(){

    $tag_slug = '';
    $users = '';
    $limit = '';
    $string_search = '';


    if(isset($_GET['tagSlug'])){
        $tag_slug = $_GET['tagSlug'];
    }

    if(isset($_GET['stringSearch'])){
        $string_search = $_GET['stringSearch'];
    }

    if(isset($_GET['users'])){
        $users = $_GET['users'];
    }

    if(isset($_GET['limit'])){
        $limit = $_GET['limit'];
    }



    echo do_shortcode('[swph_ultimatemember  tag="'.$tag_slug.'" stringsearch="'.$string_search.'" limit="'.$limit.'" users="'.$users.'"]');

    die();

}
add_action( 'wp_ajax_swpsh_collection_filter', 'swpsh_collection_filter' );
add_action( 'wp_ajax_nopriv_swpsh_collection_filter', 'swpsh_collection_filter' );







//add display name to wp user search query
add_filter( 'user_search_columns', function( $search_columns ) {

    $search_columns[] = 'display_name';
    return $search_columns;

});







////collection autocomplete ajax
function wpsh_collection_autocomplete(){

    if ( isset($_GET['users']) ) { $users = $_GET['users']; }
    $users = str_replace( ',build' , '' , $users );
    $term = $_GET['stringSearch'];
    $suggestions = array();


    // $args = array(
    //     'include'           => $users,
    //     'meta_query'        => array(
    //         'relation'  => 'AND',
    //         array(
    //             'relation' => 'OR',
    //             array(
    //                 'key'     => 'first_name',
    //                 'value'   => $term,
    //                 'compare' => 'LIKE'
    //             ),
    //             array(
    //                 'key'     => 'last_name',
    //                 'value'   => $term,
    //                 'compare' => 'LIKE'
    //             ),
    //             array(
    //                 'key'     => 'nickname',
    //                 'value'   => $term,
    //                 'compare' => 'LIKE'
    //             ),
    //         ),
    //         array(
    //             'key'     => 'account_status',
    //             'value'   => 'approved',
    //             'compare' => '='
    //         ),
    //     ),
    //     'number'    => 5,
    // );


    global $wpdb;
    $user_query = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID IN (".$users.") AND display_name LIKE '%".$term."%' LIMIT 5  ");



    // if( !empty ($user_query) ) :

    //     foreach ($user_query as $user){
    //         $suggestion = array();
    //         $suggestion['label'] = $user->display_name;
    //         $suggestions[] = $suggestion;

    //     }

    // endif;


    $response = json_encode( $user_query );
    echo $response;
    exit();

}
add_action( 'wp_ajax_wpsh_collection_autocomplete', 'wpsh_collection_autocomplete' );
add_action( 'wp_ajax_nopriv_wpsh_collection_autocomplete', 'wpsh_collection_autocomplete' );






// get members
function swph_load_more() {

    $users = $_GET['users'];
    $limit = $_GET['limit'];
    $page = $_GET['page'];



    $admin = new WP_User_Query( array( 'role' => 'Administrator' ) );
    $admin = $admin->results['0']->ID;
    $users = str_replace("build",$admin, $users);
    $asst = get_stylesheet_directory_uri();

    $users = explode(',', $users);


    if(!is_null($users)){
        $args['include'] = $users;
        $args['orderby'] = 'include';
        $args['order'] = 'ASC';
        $args['number'] = $limit;
        $args['paged'] = $page;
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
                $homeurl = home_url();
                $content .= "<div class='col-sm-4 col-xs-6 col-user' data-search='".$data_search_list."'  data-terms='".$terms_list."'>";
                $content .= "<div class='ns-collection__box'>";

                $content .= "<a href='" . $homeurl . "/profile-features' title='Share Your Work'>";
                $content .= "<img src='" . $asst . "/assets/img/profile-features.jpg' alt=''>";
                $content .= "</a>";

                $content .= "<div class='um-member-card no-photo 123'>";

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
                if ( file_exists( UM()->files()->upload_basedir . $user->ID . '/'.$image ) ) {
                    $resized = image_make_intermediate_size( UM()->files()->upload_basedir . $user->ID . '/'.$image, 450, 300, true );
                    if( is_ssl() ){
                        UM()->files()->upload_baseurl = str_replace("http://", "https://",  UM()->files()->upload_baseurl );
                    }
                    $base_url = UM()->files()->upload_baseurl . $user->ID . '/';
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
                if(function_exists('um_fetch_user') && function_exists('um_user_profile_url')){
                    um_fetch_user( $user->ID );
                    $profile_url = um_user_profile_url();
                }

                $content .= "<div class='col-sm-4 col-xs-6 col-user' data-search='".$data_search_list."' data-terms='".$terms_list."'>";
                $content .= "<div id='".$user->ID."' class='ns-collection__box'>";

                $content .= "<a href='" . $profile_url . "' title='" . $user->display_name."'>";
                $content .= "<img class='lazyload' data-src='" . $uri . "' alt=''>";
                $content .= "</a>";

                $content .= "<div class='um-member-card no-photo 111'>";

                $content .= "<div class='um-member-name a'>";
                $content .= "<a href='" . $profile_url . "' title='" . $user->display_name . "'>" . $user->display_name . "</a>";
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
        echo $content;
        die();
    }
}
add_action( 'wp_ajax_swph_load_more', 'swph_load_more' );
add_action( 'wp_ajax_nopriv_swph_load_more', 'swph_load_more' );



add_action( 'profile_update', 'lf_update_email_profile_update', 10, 2 );

function lf_update_email_profile_update( $user_id, $old_user_data ) {
    global $wpdb;
    # again do this only if you can1
    if(!current_user_can('manage_options'))
        return false;

    $old_user_email = $new_user_email = $result = '';
    $old_user_email= $old_user_data->user_email;
    $new_user_email= $_POST['email'];

    if($old_user_email != $new_user_email){

        /* Check if email was previously user for payments aor submitions */
        $get_payment = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lf_payments WHERE  email_address = '$new_user_email'" );
        $get_entry = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lf_entry WHERE  email_address = '$new_user_email'" );
        if($get_payment || $get_entry){
            $result = 'Email: <b>'.$new_user_email.'</b> already used by someone else for payment or submitting images';
            $url_user_redirect =  get_edit_user_link($user_id);
            wp_update_user( array( 'ID' => $user_id, 'user_email' => $old_user_email ) );
            ?>

                <script type="text/javascript">
                    alert('Email: <?php echo $new_user_email;?> already used by someone else for payment or submitting images');
                    window.location.href = '<?php echo $url_user_redirect;?>';
                </script>

                <?php
                die();

        }
        else{
            /* Update _lf_entry table */
            $wpdb->update(
                "{$wpdb->prefix}lf_entry",
                array(
                    'email_address' => $new_user_email
                ),
                /* Where statement */
                array(
                    'email_address' => $old_user_email
                ),
                array(
                    '%s'    // value2
                ),
                array(
                    '%s'
                )
            );
            /* Update _lf_entry table */
            $wpdb->update(
                "{$wpdb->prefix}lf_payments",
                array(
                    'email_address' => $new_user_email
                ),
                /* Where statement */
                array(
                    'email_address' => $old_user_email
                ),
                array(
                    '%s'    // value2
                ),
                array(
                    '%s'
                )
            );

        }
    }
}




if(isset($_GET['test_php']) && ($_GET['test_php']=='1')){
    // $to = 'woman.inthebox89@gmail.com';
    // $subject = 'test from laura';
    // $body = 'this is a test. working?';
    //         $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
    //     $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';
    //     $mail_headers[] = 'Content-type: text/html;charset=utf-8' . "\r\n";

    // if(wp_mail( $to, $subject, $body, $mail_headers )){
    //     echo '111110';
    // }
    //     else{
    //         echo '2222';
    //     }
    // die();
}







function swph_photo_gallery($output, $attr, $instance){
    if(!wp_is_mobile()){
        $attr['size'] = array(
            '540', '272'
        );
    }

    $html5 = current_theme_supports( 'html5', 'gallery' );
    $atts  = shortcode_atts(
        array(
            'order'      => 'ASC',
            'orderby'    => 'menu_order ID',
            'id'         => $post ? $post->ID : 0,
            'itemtag'    => $html5 ? 'figure' : 'dl',
            'icontag'    => $html5 ? 'div' : 'dt',
            'captiontag' => $html5 ? 'figcaption' : 'dd',
            'columns'    => 3,
            'size'       => 'thumbnail',
            'include'    => '',
            'exclude'    => '',
            'link'       => '',
        ),
        $attr,
        'gallery'
    );


    $id = intval( $atts['id'] );

    if ( ! empty( $atts['include'] ) ) {
        $_attachments = get_posts(
            array(
                'include'        => $atts['include'],
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[ $val->ID ] = $_attachments[ $key ];
        }
    } elseif ( ! empty( $atts['exclude'] ) ) {
        $attachments = get_children(
            array(
                'post_parent'    => $id,
                'exclude'        => $atts['exclude'],
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );
    } else {
        $attachments = get_children(
            array(
                'post_parent'    => $id,
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );
    }

    if ( empty( $attachments ) ) {
        return '';
    }

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment ) {
            $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
        }
        return $output;
    }

    $itemtag    = tag_escape( $atts['itemtag'] );
    $captiontag = tag_escape( $atts['captiontag'] );
    $icontag    = tag_escape( $atts['icontag'] );
    $valid_tags = wp_kses_allowed_html( 'post' );
    if ( ! isset( $valid_tags[ $itemtag ] ) ) {
        $itemtag = 'dl';
    }
    if ( ! isset( $valid_tags[ $captiontag ] ) ) {
        $captiontag = 'dd';
    }
    if ( ! isset( $valid_tags[ $icontag ] ) ) {
        $icontag = 'dt';
    }

    $columns   = intval( $atts['columns'] );
    $itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
    $float     = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = '';

    /**
     * Filters whether to print default gallery styles.
     *
     * @since 3.1.0
     *
     * @param bool $print Whether to print default gallery styles.
     *                    Defaults to false if the theme supports HTML5 galleries.
     *                    Otherwise, defaults to true.
     */
    if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
        $gallery_style = "
        <style type='text/css'>
            #{$selector} {
                margin: auto;
            }
            #{$selector} .gallery-item {
                float: {$float};
                margin-top: 10px;
                text-align: center;
                width: {$itemwidth}%;
            }
            #{$selector} img {
                border: 2px solid #cfcfcf;
            }
            #{$selector} .gallery-caption {
                margin-left: 0;
            }
            /* see gallery_shortcode() in wp-includes/media.php */
        </style>\n\t\t";
    }

    $size_class  = sanitize_html_class( $atts['size'] );
    $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

    /**
     * Filters the default gallery shortcode CSS styles.
     *
     * @since 2.5.0
     *
     * @param string $gallery_style Default CSS styles and opening HTML div container
     *                              for the gallery shortcode output.
     */
    $output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {

        $attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
        if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
            $image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
        } elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
            $image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
        } else {
            $image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
        }
        $image_meta = wp_get_attachment_metadata( $id );

        $orientation = '';
        if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
            $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
        }
        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
            <{$icontag} class='gallery-icon {$orientation}'>
                $image_output
            </{$icontag}>";
        if ( $captiontag && trim( $attachment->post_excerpt ) ) {
            $output .= "
                <{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
                " . wptexturize( $attachment->post_excerpt ) . "
                </{$captiontag}>";
        }
        $output .= "</{$itemtag}>";
        if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
            $output .= '<br style="clear: both" />';
        }
    }

    if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
        $output .= "
            <br style='clear: both' />";
    }

    $output .= "
        </div>\n";

    return $output;
    //    die('asd');
}
// add_filter('post_gallery' , 'swph_photo_gallery' , 10 , 3);


/*
 * SWPH Change Username error message on register
 */

/* Delete previously action hook */
remove_action('um_submit_form_errors_hook_', 'um_submit_form_errors_hook_', 10);

/* Add new  action hook */
add_action('um_submit_form_errors_hook_', 'life_framer_um_submit_form_errors_hook_', 10);

function life_framer_um_submit_form_errors_hook_( $args ){

    $form_id = $args['form_id'];
    $mode = $args['mode'];
    $fields = unserialize( $args['custom_fields'] );
       $um_profile_photo = um_profile('profile_photo');

    if ( get_post_meta( $form_id, '_um_profile_photo_required', true ) && ( empty( $args['profile_photo'] ) && empty( $um_profile_photo ) ) ) {
        UM()->form()->add_error('profile_photo', sprintf(__('%s is required.','ultimatemember'), 'Profile Photo' ) );
    }


   if( isset(  $fields ) && ! empty(  $fields ) ){
        foreach( $fields as $key => $array ) {

            $array = apply_filters('um_get_custom_field_array', $array, $fields );

            if( isset( $array ['conditions'] ) && ! empty(  $array ['conditions'] )  ){

                foreach( $array ['conditions'] as $condition ){

                    $visibility = $condition[0];
                    $parent_key = $condition[1];
                    $op = $condition[2];
                    $parent_value = $condition[3];

                    if( $visibility == 'hide' ){
                        if( $op == 'equals to' ){

                            if( $args[ $parent_key ] == $parent_value ){
                                    continue 2;
                            }

                        }
                    }

                }

            }

            if ( isset( $array['type'] ) && $array['type'] == 'checkbox' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) ) {
                UM()->form()->add_error($key, sprintf(__('%s is required.','ultimatemember'), $array['title'] ) );
            }

            if ( defined('um_user_tags_path') && isset( $array['type'] ) && $array['type'] == 'user_tags' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) ) {
                UM()->form()->add_error($key, sprintf(__('%s is required.','ultimatemember'), $array['title'] ) );
            }

            if ( isset( $array['type'] ) && $array['type'] == 'radio' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) && !in_array($key, array('role_radio','role_select') ) ) {
                UM()->form()->add_error($key, sprintf(__('%s is required.','ultimatemember'), $array['title'] ) );
            }

            if ( isset( $array['type'] ) && $array['type'] == 'multiselect' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) && !in_array($key, array('role_radio','role_select') ) ) {
                UM()->form()->add_error($key, sprintf(__('%s is required.','ultimatemember'), $array['title'] ) );
            }

            if ( $key == 'role_select' || $key == 'role_radio' ) {
                if ( isset( $array['required'] ) && $array['required'] == 1 && ( !isset( $args['role'] ) || empty( $args['role'] ) ) ) {
                    UM()->form()->add_error('role', __('Please specify account type.','ultimatemember') );
                }
            }

            if ( isset( $args[$key] ) ) {

                if ( isset( $array['required'] ) && $array['required'] == 1 ) {
                    if ( !isset($args[$key]) || $args[$key] == '' ) {
                        UM()->form()->add_error($key, sprintf( __('%s is required','ultimatemember'), $array['label'] ) );
                    }
                }

                if ( isset( $array['max_words'] ) && $array['max_words'] > 0 ) {
                    if ( str_word_count( $args[$key] ) > $array['max_words'] ) {
                    UM()->form()->add_error($key, sprintf(__('You are only allowed to enter a maximum of %s words','ultimatemember'), $array['max_words']) );
                    }
                }

                if ( isset( $array['min_chars'] ) && $array['min_chars'] > 0 ) {
                    if ( $args[$key] && strlen( utf8_decode( $args[$key] ) ) < $array['min_chars'] ) {
                    UM()->form()->add_error($key, sprintf(__('Your %s must contain at least %s characters','ultimatemember'), $array['label'], $array['min_chars']) );
                    }
                }

                if ( isset( $array['max_chars'] ) && $array['max_chars'] > 0 ) {
                    if ( $args[$key] && strlen( utf8_decode( $args[$key] ) ) > $array['max_chars'] ) {
                    UM()->form()->add_error($key, sprintf(__('Your %s must contain less than %s characters','ultimatemember'), $array['label'], $array['max_chars']) );
                    }
                }

                $profile_show_html_bio = UM()->options()->get('profile_show_html_bio');

                if(  $profile_show_html_bio == 1 && $key !== "description" ){
                    if ( isset( $array['html'] ) && $array['html'] == 0 ) {
                        if ( wp_strip_all_tags( $args[$key] ) != trim( $args[$key] ) ) {
                            UM()->form()->add_error($key, __('You can not use HTML tags here','ultimatemember') );
                        }
                    }
                }

                if ( isset( $array['force_good_pass'] ) && $array['force_good_pass'] == 1 ) {
                    if ( !UM()->validation()->strong_pass( $args[$key] ) ) {
                    UM()->form()->add_error($key, __('Your password must contain at least one lowercase letter, one capital letter and one number','ultimatemember') );
                    }
                }

                if ( isset( $array['force_confirm_pass'] ) && $array['force_confirm_pass'] == 1 ) {
                    if ( $args[ 'confirm_' . $key] == '' && !UM()->form()->has_error($key) ) {
                    UM()->form()->add_error( 'confirm_' . $key , __('Please confirm your password','ultimatemember') );
                    }
                    if ( $args[ 'confirm_' . $key] != $args[$key] && !UM()->form()->has_error($key) ) {
                    UM()->form()->add_error( 'confirm_' . $key , __('Your passwords do not match','ultimatemember') );
                    }
                }

                if ( isset( $array['min_selections'] ) && $array['min_selections'] > 0 ) {
                    if ( ( !isset($args[$key]) ) || ( isset( $args[$key] ) && is_array($args[$key]) && count( $args[$key] ) < $array['min_selections'] ) ) {
                    UM()->form()->add_error($key, sprintf(__('Please select at least %s choices','ultimatemember'), $array['min_selections'] ) );
                    }
                }

                if ( isset( $array['max_selections'] ) && $array['max_selections'] > 0 ) {
                    if ( isset( $args[$key] ) && is_array($args[$key]) && count( $args[$key] ) > $array['max_selections'] ) {
                    UM()->form()->add_error($key, sprintf(__('You can only select up to %s choices','ultimatemember'), $array['max_selections'] ) );
                    }
                }

                if ( isset( $array['validate'] ) && !empty( $array['validate'] ) ) {

                    switch( $array['validate'] ) {

                        case 'custom':
                            $custom = $array['custom_validate'];
                            do_action("um_custom_field_validation_{$custom}", $key, $array, $args );
                            break;

                        case 'numeric':
                            if ( $args[$key] && !is_numeric( $args[$key] ) ) {
                                UM()->form()->add_error($key, __('Please enter numbers only in this field','ultimatemember') );
                            }
                            break;

                        case 'phone_number':
                            if ( !UM()->validation()->is_phone_number( $args[$key] ) ) {
                                UM()->form()->add_error($key, __('Please enter a valid phone number','ultimatemember') );
                            }
                            break;

                        case 'youtube_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'youtube.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'soundcloud_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'soundcloud.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'facebook_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'facebook.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'twitter_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'twitter.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'instagram_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'instagram.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'google_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'plus.google.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'linkedin_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'linkedin.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'vk_url':
                            if ( !UM()->validation()->is_url( $args[$key], 'vk.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'url':
                            if ( !UM()->validation()->is_url( $args[$key] ) ) {
                                UM()->form()->add_error($key, __('Please enter a valid URL','ultimatemember') );
                            }
                            break;

                        case 'skype':
                            if ( !UM()->validation()->is_url( $args[$key], 'skype.com' ) ) {
                                UM()->form()->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimatemember'), $array['label'] ) );
                            }
                            break;

                        case 'unique_username':

                            if ( $args[$key] == '' ) {
                                UM()->form()->add_error($key, __('You must provide a username','ultimatemember') );
                            } else if ( $mode == 'register' && username_exists( sanitize_user( $args[$key] ) ) ) {
                                UM()->form()->add_error($key, __('Your username is already taken','ultimatemember') );
                            } else if ( is_email( $args[$key] ) ) {
                                UM()->form()->add_error($key, __('Username cannot be an email','ultimatemember') );
                            } else if ( !UM()->validation()->safe_username( $args[$key] ) ) {
                                UM()->form()->add_error($key, __('Usernames can contain letters and numbers, but not spaces or special characters (£%$& etc.)','ultimatemember') );
                            }

                            break;

                        case 'unique_username_or_email':

                            if ( $args[$key] == '' ) {
                                UM()->form()->add_error($key,  __('You must provide a username','ultimatemember') );
                            } else if ( $mode == 'register' && username_exists( sanitize_user( $args[$key] ) ) ) {
                                UM()->form()->add_error($key, __('Your username is already taken','ultimatemember') );
                            } else if ( $mode == 'register' && email_exists( $args[$key] ) ) {
                                UM()->form()->add_error($key,  __('This email is already linked to an existing account','ultimatemember') );
                            } else if ( !UM()->validation()->safe_username( $args[$key] ) ) {
                                UM()->form()->add_error($key,  __('Usernames can contain letters and numbers, but not spaces or special characters (£%$& etc.)','ultimatemember') );
                            }

                            break;

                        case 'unique_email':

                            $args[ $key ] = trim( $args[ $key ] );

                            if ( in_array( $key, array('user_email') ) ) {

                                if( ! isset( $args['user_id'] ) ){
                                    $args['user_id'] = um_get_requested_user();
                                }

                                $email_exists =  email_exists( $args[ $key ] );

                                if ( $args[ $key ] == '' && in_array( $key, array('user_email') ) ) {
                                    UM()->form()->add_error( $key, __('You must provide your email','ultimatemember') );
                                } else if ( in_array( $mode, array('register') )  && $email_exists  ) {
                                    UM()->form()->add_error($key, __('This email is already linked to an existing account','ultimatemember') );
                                } else if ( in_array( $mode, array('profile') )  && $email_exists && $email_exists != $args['user_id']  ) {
                                    UM()->form()->add_error( $key, __('This email is already linked to an existing account','ultimatemember') );
                                } else if ( !is_email( $args[ $key ] ) ) {
                                    UM()->form()->add_error( $key, __('This is not a valid email','ultimatemember') );
                                } else if ( !UM()->validation()->safe_username( $args[ $key ] ) ) {
                                    UM()->form()->add_error( $key,  __('Your email contains invalid characters','ultimatemember') );
                                }

                            } else {

                                if ( $args[ $key ] != '' && !is_email( $args[ $key ] ) ) {
                                    UM()->form()->add_error( $key, __('This is not a valid email','ultimatemember') );
                                } else if ( $args[ $key ] != '' && email_exists( $args[ $key ] ) ) {
                                    UM()->form()->add_error($key, __('This email is already linked to an existing account','ultimatemember') );
                                } else if ( $args[ $key ] != '' ) {

                                    $users = get_users('meta_value='.$args[ $key ]);

                                    foreach ( $users as $user ) {
                                        if( $user->ID != $args['user_id'] ){
                                            UM()->form()->add_error( $key, __('This email is already linked to an existing account','ultimatemember') );
                                        }
                                    }


                                }

                            }

                            break;

                        case 'unique_value':

                            if ( $args[$key] != '' ) {

                                $args_unique_meta = array(
                                    'meta_key' => $key,
                                    'meta_value' => $args[ $key ],
                                    'compare' => '=',
                                    'exclude' => array( $args['user_id'] ),
                                );

                                $meta_key_exists = get_users( $args_unique_meta );

                                if( $meta_key_exists ){
                                   UM()->form()->add_error( $key , __('You must provide a unique value','ultimatemember') );
                                }
                            }
                        break;

                        case 'alphabetic':

                            if ( $args[$key] != '' ) {

                                if( ! ctype_alpha( str_replace(' ', '', $args[$key] ) ) ){
                                   UM()->form()->add_error( $key , __('You must provide alphabetic letters','ultimatemember') );
                                }
                            }
                        break;

                        case 'lowercase':

                            if ( $args[$key] != '' ) {

                                if( ! ctype_lower( str_replace(' ', '',$args[$key] ) ) ){
                                   UM()->form()->add_error( $key , __('You must provide lowercase letters.','ultimatemember') );
                                }
                            }

                        break;

                    }

                }

            }

            if ( isset( $args['description'] ) ) {

                $max_chars = UM()->options()->get('profile_bio_maxchars');
                $profile_show_bio = UM()->options()->get('profile_show_bio');

                if( $profile_show_bio ){
                    if ( strlen( utf8_decode( $args['description'] ) ) > $max_chars && $max_chars  ) {
                            UM()->form()->add_error('description', sprintf(__('Your user description must contain less than %s characters','ultimatemember'), $max_chars ) );
                    }
                }

            }

        } // end if ( isset in args array )
    }
}

// Remove author pages
add_action('template_redirect', 'disable_author_pages');
function disable_author_pages() {
     if ( is_author() ) {
        wp_redirect(get_option('home'), 301);
        exit;
    }
}

add_action('wp_head', 'add_meta_robots_noindex');
function add_meta_robots_noindex() {
    if (is_author()) {
        echo "<meta name=\"robots\" content=\"noindex\">\r\n";
    }
}

// Add custom META Description to Photographer page
function change_yoast_meta_description_for_photographer ( $myfilter ) {
    if(is_page(8176)){
        um_fetch_user( um_get_requested_user() );
        $user_id = um_user('ID');
        $album_id = um_gallery_get_default_album( $user_id );
        $album = um_gallery_album_by_id( $album_id );

        $AlbumDescription = stripcslashes($album->album_description);
        $AlbumDescription = trim(preg_replace('/\s+/', ' ', $AlbumDescription));
        $PhotographerDescription = get_user_meta( $user_id, 'description', true );
        $PhotographerDescription = trim(preg_replace('/\s+/', ' ', $PhotographerDescription));
        $PhotographerName =  um_get_display_name( $user_id );
        $PageDescription = '';
        if ($AlbumDescription != '') {
            $PageDescription = $PhotographerName.', photographer. '.$AlbumDescription;
        } else {
            if ($PhotographerDescription != '') {
                $PageDescription = $PhotographerName.', photographer. '.$PhotographerDescription;
            } else {
                $PageDescription = $PhotographerName.', photographer.';
            }
        }

        $myfilter =  $PageDescription;
        return $myfilter;
    }
    return $myfilter;
}
add_filter( 'wpseo_metadesc', 'change_yoast_meta_description_for_photographer' );

// Change VIDEO shortcode parameters
add_shortcode( 'video', function ( $atts, $content ) {
  $output = wp_video_shortcode( $atts, $content );
  $output = str_ireplace( '<video ', '<video playsinline webkit-playsinline ', $output );

  return $output;
});

// Add new element "LF responsive banner" to Fusion Builder
function fusion_element_lf_banner_responsive() {

    fusion_builder_map(
        array(
            'name'            => esc_attr__( 'LF Responsive Banner', 'fusion-builder' ),
            'shortcode'       => 'lf_responsive_banner',
            'icon'            => 'fusiona-image',
            'preview_id'      => 'fusion-builder-lf-banner',
            'allow_generator' => true,
            'params'          => array(
                array(
                    'type'        => 'upload',
                    'heading'     => esc_attr__( 'Desktop Banner Image', 'fusion-builder' ),
                    'description' => esc_attr__( 'Image visible on desktop and tablet devices', 'fusion-builder' ),
                    'param_name'  => 'desktop_banner',
                    'value'       => '',
                ),
                array(
                    'type'        => 'upload',
                    'heading'     => esc_attr__( 'Mobile Banner Image', 'fusion-builder' ),
                    'description' => esc_attr__( 'Image visible on mobile devices', 'fusion-builder' ),
                    'param_name'  => 'mobile_banner',
                    'value'       => '',
                ),
                array(
                    'type'        => 'textfield',
                    'heading'     => esc_attr__( 'Button Text', 'fusion-builder' ),
                    'param_name'  => 'button_txt',
                    'value'       => '',
                ),
                array(
                    'type'        => 'textfield',
                    'heading'     => esc_attr__( 'Button URL', 'fusion-builder' ),
                    'param_name'  => 'button_url',
                    'value'       => '',
                ),
            ),
        )
    );
}

add_action( 'fusion_builder_before_init', 'fusion_element_lf_banner_responsive' );


function lf_responsive_banner_output($atts){
    ob_start();?>

    <div class="w-100 lf-responsive-banner">
        <div class="lf-banner lf-desktop-banner flex_back background_contain" style="background-image: url(<?php echo $atts['desktop_banner'];?>)">
            <a href="<?php echo $atts['button_url'];?>" class="home_cta"><?php echo $atts['button_txt'];?></a>
        </div>
        <div class="lf-banner lf-mobile-banner">
            <img src="<?php echo ($atts['mobile_banner']?$atts['mobile_banner']:$atts['desktop_banner']);?>">
            <a href="<?php echo $atts['button_url'];?>" class="home_cta"><?php echo $atts['button_txt'];?></a>
        </div>

    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('lf_responsive_banner','lf_responsive_banner_output');


add_action('wp_footer','lf_user_registration_remember_fields',10);
function lf_user_registration_remember_fields(){

    if(isset($_POST['_wp_http_referer'])){
        /* Entrant Registration */
        if( strpos( $_POST['_wp_http_referer'], '/register-entrant/' ) !==false ){
            ?>
            <script type="text/javascript">
                setTimeout(function(){
                    document.getElementById("user_login-8383").value = '<?php echo $_POST["user_login-8383"];?>';
                    document.getElementById("first_name-8383").value = '<?php echo $_POST["first_name-8383"];?>';
                    document.getElementById("last_name-8383").value = '<?php echo $_POST["last_name-8383"];?>';
                    document.getElementById("user_email-8383").value = '<?php echo $_POST["user_email-8383"];?>';
                    document.getElementById("user_password-8383").value = '<?php echo $_POST["user_password-8383"];?>';
                    document.getElementById("confirm_user_password-8383").value = '<?php echo $_POST["confirm_user_password-8383"];?>';
                    document.getElementById("social_instagram-8383").value = '<?php echo $_POST["social_instagram-8383"];?>';
                    document.getElementById("social_facebook-8383").value = '<?php echo $_POST["social_facebook-8383"];?>';
                    document.getElementById("social_website-8383").value = '<?php echo $_POST["social_website-8383"];?>';
                }, 1000);
            </script>
        <?php
        }
        else{
            /* Member Registration */
            if( strpos( $_POST['_wp_http_referer'], '/register-member/' ) !==false ){
                ?>
                <script type="text/javascript">
                    setTimeout(function(){
                        document.getElementById("user_login-8172").value = '<?php echo $_POST["user_login-8172"];?>';
                        document.getElementById("first_name-8172").value = '<?php echo $_POST["first_name-8172"];?>';
                        document.getElementById("last_name-8172").value = '<?php echo $_POST["last_name-8172"];?>';
                        document.getElementById("user_email-8172").value = '<?php echo $_POST["user_email-8172"];?>';
                        document.getElementById("user_password-8172").value = '<?php echo $_POST["user_password-8172"];?>';
                        document.getElementById("confirm_user_password-8172").value = '<?php echo $_POST["confirm_user_password-8172"];?>';
                        document.getElementById("social_instagram-8172").value = '<?php echo $_POST["social_instagram-8172"];?>';
                        document.getElementById("social_facebook-8172").value = '<?php echo $_POST["social_facebook-8172"];?>';
                        document.getElementById("social_website-8172").value = '<?php echo $_POST["social_website-8172"];?>';
                    }, 1000);
                </script>
            <?php
            }
        }
    }
}

add_filter( 'body_class', 'lf_add_body_custom_class' );
function lf_add_body_custom_class( $classes ) {
    if( is_user_logged_in() && !user_has_completed_profile() && !isset($_SESSION["hide_red_edit_banner"]) ) {
        $classes[] = 'incomplete-profile';
    }
    return $classes;
}



function lf_add_remove_um_hooks(){
    if(function_exists('um_gallery')){
        $um_gallery = um_gallery();
        $um_gallery_ajax = $um_gallery->ajax;

        /* Upload photo in gallery */
        remove_action( 'wp_ajax_um_gallery_photo_upload', array($um_gallery_ajax, 'um_gallery_photo_upload' ));
           add_action( 'wp_ajax_um_gallery_photo_upload', 'lf_um_gallery_photo_upload');

        /* Remove load more  */
        remove_action( 'wp_ajax_um_gallery_get_more_photos', array( $um_gallery_ajax, 'um_gallery_get_more_photos' ) );
        remove_action( 'wp_ajax_nopriv_um_gallery_get_more_photos', array( $um_gallery_ajax, 'um_gallery_get_more_photos' ) );


        /* Change template for each item gallery */
        $um_gallery_template = $um_gallery->template;
        remove_action( 'wp_footer', array( $um_gallery_template, 'add_render_tmpls' ) );
        add_action( 'wp_footer', 'lf_add_render_tmpls' );

    }

}
add_action('init', 'lf_add_remove_um_hooks');



function lf_um_gallery_photo_upload(){
    $results = array();
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    if ( ! empty( $_FILES ) ) :
        $user_id = get_current_user_id();
        if ( is_user_logged_in() ) {
            $path = um_gallery()->gallery_path . '/' . $user_id . '/';
            if ( ! file_exists( $path ) ) {
                wp_mkdir_p( $path );
            }

            //$file = um_gallery_fix_image_orientation( $_FILES );
            $file       = $_FILES['file'];
            $album_id   = (int) $_POST['album_id'];
            $tmp_file   = $file['tmp_name'];
            $name       = sanitize_text_field( $file['name'] );
            $filename   = wp_unique_filename( $path, $name, false );
            $targetFile = $path . $filename;  //5

            if ( move_uploaded_file( $tmp_file, $targetFile ) ) {

                $image    = wp_get_image_editor( $targetFile );
                $image_original = wp_get_image_editor( $targetFile );
                $filetype = wp_check_filetype( $targetFile );
                $basename = basename( $targetFile, '.' . $filetype['ext'] );
                if ( ! is_wp_error( $image ) ) {
                    /* LF modify thumbnail size */
                    $size = $image->get_size();
                    if($size['width'] < $size['height'] + 21 && $size['width'] > $size['height'] - 21 ){
                        $image->resize( 280, 280, false );
                    }
                    else{
                        $image->resize( 360, 360, false );
                    }
                    $image->save( $path . $basename.'-thumbnail.'.$filetype['ext'] );
                }

                /* LF force resize to a max width */
                if ( ! is_wp_error( $image_original )  && get_option( 'image_quality_settings' ) ) {

                    $size = $image_original->get_size();

                    //if we have an image bigger than 1500px than crop it before save as an original
                    if($size['width'] >= '1500'){
                        $image_original->resize( 1500, 2500, false );
                    }

                    $image_original->set_quality(get_option( 'image_quality_settings' ));
                    $image_original->save( $path . $basename.'.'.$filetype['ext'] );
                }


                global $wpdb;
                $wpdb->insert(
                    $wpdb->prefix . 'um_gallery',
                    array(
                        'album_id'    => $album_id,
                        'file_name'   => $filename,
                        'upload_date' => date( 'Y-m-d H:i:s' ),
                        'user_id'     => $user_id,
                        'status'      => 1,
                    ),
                    array(
                        '%d',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                    )
                );
                $last_id = $wpdb->insert_id;
            }
        } // End if().
    endif;

    $images_var = array();
    $images = um_gallery_photos_by_album( $album_id );
    if ( ! empty( $images ) ) {
        foreach ( $images as $item ) {
            global $photo;
            $image = um_gallery_setup_photo( $item );
            $images_var[ $image->id ] = $image;
        }
    }
    $image_src = $images_var[ $last_id ]->full_url;
    $thumb     = $images_var[ $last_id ]->thumbnail_url;
    $results   = array(
        'id'			 => $last_id,
        'user_id'		 => $user_id,
        'album_id'		 => $album_id,
        'image_src'		 => $image_src,
        'thumb'			 => $thumb,
        'gallery_images' => $images_var,
    );
    do_action( 'um_gallery_photo_updated', $results );
    wp_send_json( $results );
}

/* Change template for each item gallery */
function lf_get_item_block_html() {
    ob_start();
    $profile_id = um_profile_id();
    ?>
    <div class="um-gallery-item um-gallery-col-1-4 ui-sortable-handle" id="um-photo-{{id}}" data-ns-sort='{"image_id": {{id}}, "user_id": <?php echo $profile_id;?>, "menu_order": 1}'>
        <div class="um-gallery-inner">
            <a href="{{media_url}}" data-source-url="{{media_url}}"  class="um-gallery-open-photo" id="um-gallery-item-{{id}}" data-title=""  data-id="{{id}}"><img src="{{media_image_url}}" />
            </a>
            <div class="ns-gallery-actions">
                <a href="#" class="ns_um-gallery-delete-item" data-id="{{id}}"><i class="um-faicon-trash"></i></a>
                <a href="#" class="ns_um-gallery-caption-edit" data-id="{{id}}"><i class="um-faicon-pencil"></i> <span>Edit Caption</span></a>
            </div>

        </div>
    </div>
    <?php
    $html = ob_get_contents();
    ob_end_clean();
    return apply_filters( 'um_gallery_item_render_layout', $html );
}

function lf_add_render_tmpls() {
    ?>
    <script type="type="text/x-handlebars-template" id="um_gallery_item_block"><?php echo lf_get_item_block_html(); ?></script>
    <script type="type="text/x-handlebars-template" id="um_gallery_media"><?php include_once( UM_GALLERY_PATH . 'assets/tmpl/media.php' ); ?></script>
    <?php
}


add_action('wp_enqueue_scripts', 'lf_um_script', 99);
function lf_um_script(){
    if(is_page('submit-entrant') || is_page('submit-member') || is_page('submit-past') || 1){
    wp_deregister_script( 'um_gallery');
    wp_dequeue_script( 'um_gallery' );

    wp_register_script( 'um_gallery', get_stylesheet_directory_uri() . '/assets/js/um-gallery-pro.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-autocomplete', 'jquery-ui-position', 'jquery-masonry', 'masonry' ), UM_GALLERY_PRO_VERSION, true );
			// Localize the script with new data
			$localization = array(
				'site_url' 				=> site_url(),
				'nonce' 				=> wp_create_nonce( 'um-event-nonce' ),
				'ajax_url' 				=> admin_url( 'admin-ajax.php' ),
				'is_owner' 				=> um_gallery()->is_owner(),
				'enable_comments'		=> um_gallery_pro_addon_enabled( 'comments' ),
				'save_text' 			=> __( 'Save', 'um-gallery-pro' ),
				'edit_text' 			=> __( '<i class="um-faicon-pencil"></i> Edit Caption', 'um-gallery-pro' ),
				'cancel_text' 			=> __( 'Cancel', 'um-gallery-pro' ),
				'album_id' 				=> um_galllery_get_album_id(),
				'dictDefaultMessage' 	=> '<span class="icon"><i class="um-faicon-picture-o"></i></span>
			                                <span class="str">' . __( 'Upload your photos (max 8MB/photo)', 'um-gallery-pro' ) . '</span>',
				'upload_complete' 		=> __( 'Upload Complete', 'um-gallery-pro' ),
				'no_events_txt' 		=> __( 'No photos found.', 'um-gallery-pro' ),
				'confirm_delete' 		=> __( 'Are you sure you want to delete this?', 'um-gallery-pro' ),
				'layout_mode'			=> um_gallery_pro_get_option( 'um_gallery_single_album', 0 ),
				'show_full_screen'      => 'on' === um_gallery_pro_get_option( 'um_gallery_fullscreen' ) ? true : false,
				'closeModalAfterSave'   => 'on' === um_gallery_pro_get_option( 'close_modal_save' ) ? false : true,
			);
			$localization['user']  = array();
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				um_fetch_user( $user_id );
				$localization['user']  = array(
					'id' 		=> $user_id,
					'name' 		=> um_user( 'display_name' ),
					'link' 		=> um_user_profile_url(),
					'avatar' 	=> um_get_user_avatar_url(),
				);
				um_reset_user();
			}

			// Comment text.
			$localization['comments']['textareaPlaceholderText'] = esc_html__( 'Add a comment', 'um-gallery-pro' );
			$localization['comments']['newestText']              = esc_html__( 'Newest', 'um-gallery-pro' );
			$localization['comments']['oldestText']              = esc_html__( 'Oldest', 'um-gallery-pro' );
			$localization['comments']['popularText']             = esc_html__( 'Popular', 'um-gallery-pro' );
			$localization['comments']['attachmentsText']         = esc_html__( 'Attachments', 'um-gallery-pro' );
			$localization['comments']['sendText']                = esc_html__( 'Send', 'um-gallery-pro' );
			$localization['comments']['replyText']               = esc_html__( 'Reply', 'um-gallery-pro' );
			$localization['comments']['editText']                = esc_html__( 'Edit', 'um-gallery-pro' );
			$localization['comments']['editedText']              = esc_html__( 'Edited', 'um-gallery-pro' );
			$localization['comments']['youText']                 = esc_html__( 'You', 'um-gallery-pro' );
			$localization['comments']['saveText']                = esc_html__( 'Save', 'um-gallery-pro' );
			$localization['comments']['deleteText']              = esc_html__( 'Delete', 'um-gallery-pro' );
			$localization['comments']['viewAllRepliesText']      = esc_html__( 'View all __replyCount__ replies', 'um-gallery-pro' );
			$localization['comments']['hideRepliesText']         = esc_html__( 'Hide replies', 'um-gallery-pro' );
			$localization['comments']['noCommentsText']          = esc_html__( 'No comments', 'um-gallery-pro' );
			$localization['comments']['noAttachmentsText']       = esc_html__( 'No attachments', 'um-gallery-pro' );
			$localization['comments']['attachmentDropText']      = esc_html__( 'Drop files here', 'um-gallery-pro' );
			wp_localize_script( 'um_gallery', 'um_gallery_config', $localization );
            wp_enqueue_script( 'um_gallery' );
        }

}


// Loader on the register form after submit
add_action( 'um_after_form', 'swph_register_form_loader', 10, 1 );
function swph_register_form_loader( $args )
{
    if ($args['template'] !== 'register') {
        return;
    }

    echo '
        <div class="sortable-loading register-loader" style="display:none;">
            <div class="register-loader-content">
                <h3 data-fontsize="30" data-lineheight="36px" class="fusion-responsive-typography-calculated" style="--fontSize:30; line-height: 1.2 !important;">We are creating your account</h3>
                <div class="bubblingG">
                    <span id="bubblingG_1">
                    </span>
                    <span id="bubblingG_2">
                    </span>
                    <span id="bubblingG_3">
                    </span>
                </div>
            </div>
        </div>
    ';
}

/*
 * UM - Change email body background
 */
add_filter( 'um_email_template_body_attrs', 'my_email_template_body_attrs', 10, 3 );
function my_email_template_body_attrs( $body_atts, $slug, $args ) {
    // your code here
    return 'style="background: #fff;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;"';
}

//add_action('um_submit_form_register', 'swph_registration_error_report');
function swph_registration_error_report()
{
    if (!isset(UM()->form()->errors)) {
        return;
    }

    $postForm = UM()->form()->post_form;

    // send an email to support in case the user calls
    $mail_headers = [];
    $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
    $mail_headers[] = 'MIME-Version: 1.0" . "\n';
    $mail_headers[] = "Content-type:text/html;charset=UTF-8" . "\n";
    $data = [
        'user_ip' => $_SERVER['REMOTE_ADDR'],
        'date' => current_time('mysql'),
        'submitted_data' => isset($postForm['submitted']) ? $postForm['submitted'] : 'unknown',
        'error' => UM()->form()->errors
    ];
    if (isset($data['submitted_data']['user_password'])) {
        $data['submitted_data']['user_password'] = '<i>[REDACTED]</i>';
    }
    if (isset($data['submitted_data']['confirm_user_password'])) {
        $data['submitted_data']['confirm_user_password'] = '<i>[REDACTED]</i>';
    }

    $subject = "Life-Framer user registration error!";
    $message = sprintf(
        "<h3>Hi! There was failed registration attempt:</h3><pre>%s</pre>",
        str_replace('Array', 'Data', print_r($data, true))
    );
    $to = "info@life-framer.com";
    wp_mail($to, $subject, $message, $mail_headers);
}


/*
 * LF - force redirect to My LF
 */

add_action( 'um_on_login_before_redirect', 'lf_on_login_before_redirect', 10, 1 );

function lf_on_login_before_redirect( $user_id ) {
    $user_role = UM()->roles()->get_um_user_role($user_id);

    switch($user_role){
        case 'um_entrant':
            $redirect_url = get_bloginfo('url').'/my-lf-entrant/';
            $found_role = true;
            break;

        case 'um_member':
            $redirect_url = get_bloginfo('url').'/my-lf-member/';
            $found_role = true;
            break;

        case 'um_past':
            $redirect_url = get_bloginfo('url').'/my-lf-past/';
            $found_role = true;
            break;

        default:
        $redirect_url = '';
        $found_role = false;
    }

    if($found_role){
        wp_redirect($redirect_url);
        exit();
    }
}

add_filter( 'um_before_save_filter_submitted', 'lf_before_save_filter_submitted', 10, 1 );
function lf_before_save_filter_submitted( $submitted )
{
    $submitted = lf_social_website_custom_before_save($submitted);
    $submitted = lf_social_instagram_username_before_save($submitted);

    return $submitted;
}

function lf_social_website_custom_before_save($submitted)
{
    if (empty($submitted['social_website_custom']) || !trim($submitted['social_website_custom'])) {
        return $submitted;
    }

    $website = trim(strtolower($submitted['social_website_custom']));
    if (strpos($website, 'http') !== 0 ) {
        $submitted['social_website_custom'] = 'https://' . $website;
    }

    return $submitted;
}

function lf_social_instagram_username_before_save($submitted)
{
    if (empty($submitted['social_instagram_username'])) {
        return $submitted;
    }

    $submitted['social_instagram_username'] = trim(trim(strtolower($submitted['social_instagram_username']), "#@"));

    return $submitted;
}

//add_filter('um_field_extra_atts', 'lf_custom_instagram_field', 10, 3);
//function lf_custom_instagram_field($atts, $key, $data)
//{
//    if ($key !== 'social_instagram_username') {
//        return $atts;
//    }
//
//    echo '<pre>';
//    var_dump($atts, $key, $data);
//    echo '</pre>';
//
//    return $atts;
//}


add_filter( 'um_shortcode_args_filter', 'um_shortcode_args_filter_lf_registration', 10, 1 );
function um_shortcode_args_filter_lf_registration( $args ) {
    if( $args['mode']=='register' && is_user_logged_in() ){
        echo '<p style="margin: 30px auto 20px;text-align: center;font-weight: 600; font-size: 15px;">You are already registered. Please head over to your My LF account to submit</p>';
        return;

    }
    return $args;
}


/* Register custom menu  */
function lf_register_nav_menu(){
    register_nav_menus( array(
        'footer_menu'  => __( 'Footer Menu', 'lifeframer' ),
    ) );
}
add_action( 'after_setup_theme', 'lf_register_nav_menu', 12 );

function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
   }
   add_filter('upload_mimes', 'cc_mime_types', 99);