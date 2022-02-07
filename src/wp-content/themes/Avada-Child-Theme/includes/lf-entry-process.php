<?php
/* Redirect entrant-registration to my-lf-entrant - if user logged in */
/* Redirect entrant-registration to my-lf-entrant - if user logged in */
/* OR Redirect member-registration-2 to my-lf-member - if user logged in */
add_action('template_redirect','redirect_entrant_registration');

function redirect_entrant_registration(){
	global $post;
    $slug = $post->post_name;
	$current_user = wp_get_current_user();
	if ($current_user->ID ) {
		if($slug=='entrant-registration'){
			wp_redirect(get_bloginfo('url').'/my-lf-entrant');
		}
		else if($slug=='membership-registration-2'){
			wp_redirect(get_bloginfo('url').'/my-lf-member');
		}
	}
}

function get_active_LF_theme_entrant($isSeriesAward = false){
    global $wpdb;
    $current_time = current_time('mysql');

    $isSeriesAward = $isSeriesAward ? 1 : 0;
    $theme_entrant = $wpdb->get_row("
    	SELECT * FROM {$wpdb->prefix}lf_themes 
    	WHERE start < '$current_time' 
        AND is_series_award = {$isSeriesAward}
    	AND end > '$current_time' and deleted = false LIMIT 1");

    return $theme_entrant;
}

function get_active_LF_entries_entrant(){
    global $wpdb;
    // $first_day_month = date('Y-m-01');
 	$current_time = current_time('mysql');

    $entries_entrant = $wpdb->get_results("
    	SELECT * FROM {$wpdb->prefix}lf_theme_entries 
    	WHERE  end_entrants > '$current_time' 
    	and deleted = false ");

    // echo '<pre>'.print_r($entries_entrant,1).'</pre>';
    // die();

    return $entries_entrant;
}


function get_active_LF_themes_member($isSeriesAward = false){
    global $wpdb;
    // $first_day_month = date('Y-m-01');
 	$current_time = current_time('mysql');

    $isSeriesAward = $isSeriesAward ? 1 : 0;
    $themes_member = $wpdb->get_row("
    	SELECT * FROM {$wpdb->prefix}lf_themes 
    	WHERE  end > '$current_time' 
        AND is_series_award = {$isSeriesAward}
    	and deleted = false  LIMIT 1
    	");

    return $themes_member;
}

function get_active_LF_entries_member(){
    global $wpdb;
    // $first_day_month = date('Y-m-01');
 	$current_time = current_time('mysql');

    $entries_member = $wpdb->get_results("
    	SELECT * FROM {$wpdb->prefix}lf_theme_entries 
    	WHERE  end_members > '$current_time' 
    	and deleted = false ");

    return $entries_member;
}

function get_active_LF_entries($isSeriesAward = false){
    global $wpdb;
    // $first_day_month = date('Y-m-01');
 	$current_time = current_time('mysql');

 	$entries_member = array();

 	$isSeriesAward = $isSeriesAward ? 1 : 0;
    $lf_themes = $wpdb->get_row("
		    	SELECT * FROM {$wpdb->prefix}lf_themes 
		    	WHERE start <= '$current_time' 
		    	AND deleted = false 
		    	AND is_series_award = {$isSeriesAward}
		    	ORDER BY start DESC
		    	LIMIT 1 ", ARRAY_A);

    if(!empty($lf_themes)){
    	$theme_id = $lf_themes['id'];
		$entries_member = $wpdb->get_results(
				"SELECT * 
			    FROM {$wpdb->prefix}lf_theme_entries
			    WHERE theme_id = '$theme_id'
			    ORDER BY id ASC ");
    }

    return $entries_member;
}





function get_images_left_to_submit($user_email,$user_login,$user_type='',$start_date){
 	global $wpdb;

	/* Get total numbers of images available get_images_left_to_submite from payments */
 	// $start_date = date('Y-m-01 00:00:00');

    $payments = $wpdb->get_results("
    	SELECT description FROM {$wpdb->prefix}lf_payments 
    	WHERE email_address = '$user_email' 
    	AND date >= '{$start_date}' ",
    	ARRAY_A);

	// // Print last SQL query string
	// echo $wpdb->last_query;
	// // Print last SQL query result
	// echo '<pre>'.print_r($wpdb->last_result,1).'</pre>';
	// // Print last SQL query Error
	// echo $wpdb->last_error;

    $total_payment_images = 0;
    if($payments){
    	foreach($payments as $payment){
    		switch($payment['description']){
    			case 'Life-Framer - 1 entry':
    				$total_payment_images++;
    				break;
				case 'Life-Framer - 3 entries';
					$total_payment_images = $total_payment_images+3;
					break;
				case 'Life-Framer - 6 entries':
					$total_payment_images = $total_payment_images+6;
					break;
				default :
    		}
    	}
    }

	/* Get already uploaded images */

	$str_to_time_Startdate = strtotime($start_date);

    $uploaded_images = $wpdb->get_var("
    	SELECT SUM(no_images) FROM {$wpdb->prefix}lf_entry 
		WHERE wp_user = '$user_login' 
		AND role = '$user_type'
    	AND date_time >= '{$str_to_time_Startdate}'"
    );

    	// Print last SQL query string
	// echo $wpdb->last_query;
	// // Print last SQL query result
	// echo '<pre>'.print_r($wpdb->last_result,1).'</pre>';
	// // Print last SQL query Error
	// echo $wpdb->last_error;

    $total_images = $total_payment_images - $uploaded_images;
    return $total_images;
}



function get_amount_payment_ref($user_email){
	global $wpdb;
	$start_date = date('Y-m-01 00:00:00');
 	$end_date = date('Y-m-d H:i:s', strtotime('now'));
    // $payments = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}lf_payments WHERE email_address = '$user_email' AND date BETWEEN '{$start_date}' AND '{$end_date}' ORDER BY date DESC", ARRAY_A);

    $payments = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}lf_payments WHERE email_address = '$user_email' ORDER BY date DESC", ARRAY_A);
    return $payments;
}


function lf_enqueue_scripts() {
	/* If page = submit-*** remove initial dropzone script and add the new one */
	if(is_page('submit-entrant')|| is_page('submit-member') || is_page('submit-past') || is_page('submit-series-award')){
//	    wp_enqueue_style( 'dropzone-css', get_stylesheet_directory_uri().'/assets/css/dropzone.css' );
	 	wp_deregister_script( 'um_gallery' );
	 	wp_dequeue_script( 'um_gallery' );

    //    wp_enqueue_script('exif', get_stylesheet_directory_uri().'/assets/js/exif.js', [], false, true);

		wp_enqueue_script( 'dropzone-js', get_stylesheet_directory_uri().'/assets/js/dropzone.js', true );
	    wp_enqueue_script( 'js-load-img', get_stylesheet_directory_uri().'/assets/js/load-image.all.min.js', true );


        wp_enqueue_script('submit-handle-uploads', get_stylesheet_directory_uri().'/assets/js/submit-handle-uploads.js', array('js-load-img','dropzone-js'), false, true);
        wp_localize_script( 'submit-handle-uploads', 'my_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

        wp_localize_script( 'submit-handle-uploads', 'um_gallery_config',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
}
add_action( 'wp_enqueue_scripts', 'lf_enqueue_scripts', 100 );

function lf_enqueue_dropzone_css() {
    if(is_page('submit-entrant')|| is_page('submit-member') || is_page('submit-past') || is_page('submit-series-award')) {
        wp_enqueue_style('dropzone-css', get_stylesheet_directory_uri() . '/assets/css/dropzone.css');
    }
}
add_action('wp_enqueue_scripts', 'lf_enqueue_dropzone_css');


function lf_admin_enqueue_scripts($hook) {
    wp_enqueue_script( 'custom-admin-js',get_stylesheet_directory_uri().'/assets/js/admin-js.js', array('jquery'), null, true);
    wp_enqueue_style( 'custom-admin-css', get_stylesheet_directory_uri().'/assets/css/admin-css.css' );
}
add_action( 'admin_enqueue_scripts', 'lf_admin_enqueue_scripts' );


function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

function submit_images_fc($atts = [], $content = null) {
	$atts = shortcode_atts( array(
	    'type' => '',
	), $atts);

	global $wpdb;
	ob_start();
	// echo $atts['type'];
	$current_user = wp_get_current_user();
	$user_email = $current_user->user_email;
	$user_login = $current_user->user_login;
	$current_user_ID = $current_user->ID;

	/* Get user role */
	// um_fetch_user( $current_user_ID );
	$user_role = UM()->roles()->get_um_user_role($current_user_ID);
	?>

	<div class="submit-images submit-images-<?php echo $user_role;?>">

	<?php

	/* ENTRANT role */

		/* Get current themes */
		$active_entries = get_active_LF_entries();

		/* In case there are open themes */
		if(!empty($active_entries)){

			/* Set correct timezone */
			date_default_timezone_set('America/Los_Angeles');
			/* For entrants credit can be used 4 months after the payment */
			$limit_date = date('Y-m-d H:i:s', strtotime(' -4 months'));

			/* Get images to submit */
			if($user_role == 'um_entrant'){
				$images_left = get_images_left_to_submit($user_email,$user_login,$user_role, $limit_date);
				$extra_images = get_user_meta($current_user_ID,'extra_entry_imgs',true);
				if(!isset($extra_images) || empty($extra_images)){
					$extra_images = 0;
				}
				$total_images = $images_left + $extra_images;
			}
			else{
				$total_images = 0;
			}

			if( ($user_role == 'um_entrant' && $total_images>0) || ($user_role == 'um_member') ){
				?>
				<div class="submit-images-intro lf-limit-width">
					<div class="row">
                        <div class="col-md-6">
                            <div class="custom-separator"><div></div></div>
                            <h3>Submit Images</h3>
                        </div>
                        <div class="col-md-6">
                            <?php
                            if($user_role == 'um_entrant'){ ?>
                                <p>You have <span id="images-to-submit" class="textred"><?php echo $total_images;?> image<?php echo ($total_images=='1'?'':'s');?></span> available to submit</p>
                            <?php } ?>
                        </div>
					</div>

				</div><!--end submit-images-intro-->

				<div class="submission-steps lf-limit-width">
					<div class="submission-step-1">
						<p>Drag your images into the upload boxes, and add any text you wish to include in the description boxes. There is no need to name your files in a particular format and you can submit multiple times ahead of each deadline.</p>
					</div>
					<div class="clear"></div>
					<div class="submission-step-2">
						<p>Once you’re ready, press ‘Submit’ at the bottom. Review your entries carefully before submission – you will not have an opportunity to edit them afterwards.</p>
					</div>
				</div>
				<div class="clear"></div>

				<div class="submition-help">
					<hr>
					<div class="lf-limit-width">
						<div class="row flex-elem flex-elem-middle">
							<div class="col-md-8">
                                <p class="mb0">
                                    <i>
                                        If you have any questions, or if something is unclear you can browse our help topics.
                                        All deadlines are 23:59 Pacific Time Zone.
                                    </i>
                                </p>
							</div>
							<div class="col-md-4">
								<a href="<?php echo get_bloginfo('url');?>/faq" class="bg-black helpbtn">Help</a>
							</div>
						</div>
					</div>
					<hr>
				</div>

				<div class="clear"></div>
				<?php


				echo '<input type="hidden" value="'. $total_images .'" id="img_to_submit">';
				echo '<input type="hidden" value="'. $user_login .'" id="user_login">';
				echo '<input type="hidden" value="'. $current_user->first_name .' '.$current_user->last_name .'" id="user_fname_lname">';
				echo '<input type="hidden" value="'. $current_user->user_email .'" id="user_email">';
				echo '<input type="hidden" value="'. $current_user_ID .'" id="user_id">';
				echo '<input type="hidden" value="'. $user_role .'" id="user_role">';

				echo '<input type="hidden" name="submitimages-ajax-nonce" id="submitimages-ajax-nonce" value="' . wp_create_nonce( 'submitimages-ajax-nonce' ) . '" />';


				/* Unset Session last entry id */
				unset($_SESSION['last_entry']);

				if (!session_id())
					session_start();

				$_SESSION['success_uploaded']='0';

				$textarea_ph = '';
				$textarea_ph .= 'Tell us about your image(s) or series (we share our favourites on our Instagram, with the hashtag #lifeframerstories)';
				$textarea_ph .= '&#10;&#10;';
				$textarea_ph .= 'Series statement: ….';
				$textarea_ph .= '&#10;&#10;';
				$textarea_ph .= 'and/or';
				$textarea_ph .= '&#10;&#10;';
				$textarea_ph .= 'Image 1: Title and/or description&#10;';
				$textarea_ph .= 'Image 2: …&#10;';
				$textarea_ph .= 'Image 3: …&#10;';
				$textarea_ph .= 'Image 4: …&#10;';

				if($user_role == 'um_member'){
					$member_entries_submitted = entry_submitted_images($active_entries[0]->theme_id, $user_login, $user_role);
					// echo '<pre>'.print_r($member_entries_submitted,1).'</pre>';
				}

				/* Get split theme names */
				foreach($active_entries as $key=> $entry){
					
					$_SESSION['key-'.$key]='0';

					?>
					<input type="hidden" id="<?php echo 'key-'.$key;?>" value="0">
					<div class="submit-theme-photos lf-limit-width" >
						<label class="headline-label">
							<?php echo 'Upload images for <span class="textred textuppercase">'.trim($entry->name).'</span>';?>
							<?php echo '<br>Deadline: '.date('d F Y',strtotime($entry->end_members));?>
						</label>
						<?php
						if($user_role == 'um_member'){
							echo '<div class="clear"></div>';
							/* Get the number of images submitted per entry */
							
							$max_per_theme = 10;
							if(array_key_exists($entry->id,$member_entries_submitted)){
								$already_submitted = $member_entries_submitted[$entry->id];
							}
							else{
								$already_submitted = 0;
							}
							$max_per_theme = $max_per_theme - $already_submitted;
							$extra_img_per_theme = get_user_meta($current_user_ID, 'extra_img_'.($key+1), true);
							if($extra_img_per_theme){$max_per_theme +=$extra_img_per_theme; }

							if($max_per_theme>0){
								echo '<label class="member-img-available">You have <span class="textred">'.$max_per_theme.' image'.($max_per_theme>1?'s':'').'</span> available to submit</label>';
							}
							else{
								echo '<label class="member-img-available">You have <span class="textred">no entry</span> credits available to submit. Good luck for when the selection is made! A new theme will be announced at the start of next month.</label>';
							}

						} ?>
						<div class="clear"></div>
						<?php
						if($user_role == 'um_entrant' || ($user_role == 'um_member' && $max_per_theme>0) ){ ?>

							<label>Drag or select your images</label>
							<div class="upload-form upload-form-entrant lf-upload-form" id="form-<?php echo $key;?>" data-role="<?php echo $user_role;?>"
								data-box="<?php echo 'key-'.$key;?>"
								data-entry_item="<?php echo trim($entry->name);?>"
								date-time="<?php echo strtotime('now');?>"
								data-current_theme="<?php echo $entry->theme_id;?>"
								data-current_entry_theme="<?php echo $entry->id;?>"
								data-max_per_theme="<?php echo $max_per_theme;?>"
								>
								<div class="dz-message">
									<span>
										<span class="icon"><i class="um-faicon-picture-o"></i></span>
										<span class="str">Upload your photos (.jpg or .jpeg only - max 8MB/photo)</span>
									</span>
								</div>
							</div>

							<label class="submit-description">Description (optional)</label>

							<textarea name="submit-images-description[<?php echo $key;?>]" data-entry_desc="<?php echo trim($entry->name);?>" placeholder="<?php echo $textarea_ph;?>"></textarea>
						<?php } ?>
						<div class="clear"></div>
					</div>	<!-- END submit-theme-photos-->
					<?php
					if($key < count($active_entries)-1){ echo '<hr>';}?>
					<?php
					// break;
				}?>



				<div class="submit-images-holder">
					<hr>
						<div class="lf-limit-width">
							<p class="mb0">Review your entry carefully. Changes cannot be made once submitted.</p>
							<button class="dz-submit-images" data-submission_type="entry">
								<span class="submit">Submit</span>
								<i class="fa fa-spinner fa-spin"></i>
							</button>
						</div>
					<hr>
				</div>

				<?php

			}
			else{
				?>
				<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$(".enter-award-again").css('display','block');
				});
				</script>
			<?php
			}
		}
		else{
			echo '<h4 class="text-center">There are no open themes at the moment!</h4>';
		}


	// echo do_shortcode('[past_uploads role="'.$user_role.'"]');
?>
	</div>
<?php
	return ob_get_clean();
}

add_shortcode('submit_images','submit_images_fc');


function past_uploads_fs($atts = [], $content = null) {
    $atts = shortcode_atts( array(
        'role' => '',
    ), $atts);
	ob_start();
	// return false;

	/* Past submitions functionality */
	global $wpdb;
	$current_user = wp_get_current_user();



	$images_subbmitted = $wpdb->get_results("
        SELECT 
            lf_entry.time,
            lf_entry.theme_id,
            lf_entry.date_time,
            lf_photos.path,
            lf_photos.theme_entry_id
        FROM {$wpdb->prefix}lf_entry AS lf_entry
        INNER JOIN {$wpdb->prefix}lf_photos AS lf_photos
        ON lf_photos.entry_id =  lf_entry.id 
        INNER JOIN {$wpdb->prefix}lf_themes AS lf_themes
        ON lf_entry.theme_id =  lf_themes.id 
        WHERE 
            lf_entry.wp_user='$current_user->user_login' AND 
            lf_themes.is_series_award = 0 AND
			lf_entry.time>='2018-03-01'
			
        ORDER BY
            lf_entry.time DESC,
            lf_photos.theme_entry_id ASC,
            lf_photos.id ASC
    ", ARRAY_A);

	echo '<div class="images-submitted images-submitted-'.$atts['role'].'" id="my-past-uploads">';
		echo '<h3 id="pastuploads">MY PAST UPLOADS</h3>';

		if($images_subbmitted){

			if(isset($_GET['success']) && $_GET['success']=='true'){
				echo '<div class="lf-img-submitted">';
				echo '<span class="lf-img-submitted-dismiss"><i class="fa fa-times-thin"></i></span>';
				echo '<p>Thank you. Your entry has been made. You can see your submissions below. Feel free to submit again prior to each deadline.</p>';
				echo '</div>';
			}

			$upload_dir = wp_upload_dir();
			$upload_baseurl = $upload_dir['baseurl'];
			$upload_basedir = $upload_dir['basedir'];

			$date = '';
			foreach ($images_subbmitted as $key => $result) {
				// echo '<pre>'.print_r($result,1).'</pre>';
				$entry_date = date('d M Y',strtotime($result['time']));
				if($date != $entry_date){
					$submittion_date = date('d M Y',($result['date_time']));
					if($key!='0')
						{
							echo '<div class="clearfix"></div>';
							echo '</div><!--end 1 -->';
							echo '</div><!--end 2 -->';
						}

					echo '<div class="submittion-items">';
					echo '<span>- '.$submittion_date.'</span>';
					echo '<div class="submittion-images">';
				}


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


					$image_path = $result['path'];
					$image_path_array = explode('/',$image_path);

					$image_name = $image_path_array[count($image_path_array)-1];
					$image_name_array = explode('-',$image_name);

					$theme_name = $image_name_array['1'];
					$theme_name = preg_replace('/[0-9]+/', '', $theme_name);


					$theme_entry_name = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lf_theme_entries WHERE  id = '$theme_entry_id'" );

					// echo '<span class="entry_title">' .$theme_entry_name->name. '</span>';
					echo '<span class="entry_title">' .$theme_name. '</span>';
					echo '</div><!--end submision image holder-->';
					echo '</div><!--end submision image-->';
				}
				$date = $entry_date;
			}
			echo '<div class="clearfix"></div>';
			echo '</div><!--end 1 -->';
			echo '</div><!--end 2 -->';
			echo '<p class="mb0 centered"><small><i>Any text submitted is not displayed here but don\'t worry, we\'ve received it</i></small></p>';
			echo '</div>';

		}
		else{
			echo '<div style="display: block; height: 50px;"></div>';
		}

	echo '</div><!-- END .my-past-uploads -->';

	return ob_get_clean();
}

add_shortcode('past_uploads','past_uploads_fs');

function  entry_submitted_images($theme_id, $user_login, $role){
	global $wpdb;

	$count_images = $wpdb->get_results(
		"SELECT count(lf_entry.id) as total,
		SUM(no_images) as no_images,
		lf_photos.theme_entry_id as theme_entry_id 
		FROM {$wpdb->prefix}lf_entry AS lf_entry
		
		INNER JOIN {$wpdb->prefix}lf_photos AS lf_photos 
		ON lf_photos.entry_id =  lf_entry.id 
	
		WHERE lf_entry.wp_user='$user_login' 
		AND lf_entry.theme_id = '$theme_id' 
		AND lf_entry.role = '$role' 
		AND lf_photos.is_extra ='0' 
		GROUP BY theme_entry_id",
	ARRAY_A);


	// WHERE lf_entry.email_address='$email_address'

// 	$uploaded_images = $wpdb->get_var("
// 	SELECT SUM(no_images) FROM {$wpdb->prefix}lf_entry 
// 	WHERE wp_user = '$user_login' 
// 	AND role = '$user_type'
// 	AND date_time >= '{$str_to_time_Startdate}'"
// );

	$result = array();

	if(isset($count_images) && !empty($count_images)){
	
		foreach($count_images as $count_img){
			$result[$count_img['theme_entry_id']] = $count_img['total'];
		}
	}
	return $result;

}

if(isset($_GET['submit_upload']) && ($_GET['submit_upload'])=='true'){
	add_action('template_redirect','submit_upload');
}

function insert_lf_photo($entry_id,$path,$theme_id,$theme_entry_id, $is_extra){
	global $wpdb;
	$wpdb->insert(
		"{$wpdb->prefix}lf_photos",
			array(
				'entry_id' => $entry_id,
				'path' => $path,
				'theme_id' => $theme_id,
				'theme_entry_id' => $theme_entry_id,
				'is_extra' => $is_extra
			),
			array(
				'%d',
				'%s',
				'%d',
				'%d',
				'%d'
			)
		);
}


function update_lf_entry($entry_id){
	global $wpdb;
	$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->prefix}lf_entry
					              SET no_images = no_images + 1
					              WHERE id = %d", $entry_id)
	    );
}

function update_lf_entry_extra_credits($entry_id){
	global $wpdb;
	$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->prefix}lf_entry
	               				  SET no_img_extra = no_img_extra + 1
	             				  WHERE id = %d", $entry_id)
	    );
}


function submit_upload(){
 	global $wpdb;

	$submit_response = array();

	/* Insert new entry to _lf_entry if is not inserted yet */
	$date_time = $_GET['date_time'];
	$user_login = $_GET['user_login'];
	$user_fname_lname = stripslashes($_GET['user_fname_lname']);
	$user_email = $_GET['user_email'];
	$role =  $_GET['user_role'];

	$current_theme = $_GET['current_theme'];
	$current_entry_theme = $_GET['current_entry_theme'];
	$theme_id = $_GET['theme_id'];
	$entry_item = $_GET['entry_item'];
	$entry_desc = strip_tags($_REQUEST['entry_desc'], '<br>');
	// $entry_desc = ($_GET['entry_desc']);
	$theme_index = $_GET['index'];
	$submit_response['some_msg'] = $theme_index;


	$entry = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lf_entry WHERE  wp_user = '$user_login' AND date_time = '$date_time' AND theme_id = '$current_theme' " );


	// // Print last SQL query string
	// echo $wpdb->last_query;
	// // Print last SQL query result
	// echo '<pre>'.print_r($wpdb->last_result,1).'</pre>';
	// // Print last SQL query Error
	// echo $wpdb->last_error;



	if(empty($entry)){
		/* Insern new entry in lf_entry table */
		$payment_info = get_amount_payment_ref($user_email);

		$amount = $payment_ref = '';
		if($payment_info){
			$amount = $payment_info['amount'];
			$payment_ref = $payment_info['payment_ref'];
		}
		date_default_timezone_set('America/Los_Angeles');
		$wpdb->insert(
		"{$wpdb->prefix}lf_entry",
			array(
				'time' => date('Y-m-d H:i:s'),
				'date_time' => $date_time,
				'name' => ucwords($user_fname_lname),
				'wp_user' => $user_login,
				'email_address' => $user_email,
				'theme_id' => $current_theme,
				'payment' => $amount,
				'payment_reference' => $payment_ref,
				'role'=> $role,
				'additional_information' => $entry_desc

			),
			array(
				'%s',
				'%s' ,
				'%s' ,
				'%s' ,
				'%s' ,
				'%d' ,
				'%d' ,
				'%s' ,
				'%s' ,
				'%s'
			)
		);
		$last_id = $wpdb->insert_id;

		$_SESSION['last_entry'] = $last_id;
	}



	if( !empty($_FILES) && ($_FILES['img-submit']['error']=='0') && isset($_SESSION['last_entry'] )){

		/* Create photo name */
		$_SESSION[$theme_id] = $_SESSION[$theme_id]+1;
		$image_name = $_SESSION['last_entry'].'-'.$entry_item.$_SESSION[$theme_id].'-'.str_replace(" ","",ucwords($user_fname_lname));

		$targetPath = $_SERVER['DOCUMENT_ROOT'].'/wp-content/uploads/LifeFramerV2/';
		$ext = strtolower(pathinfo($_FILES['img-submit']['name'], PATHINFO_EXTENSION));
		$targetFile = $targetPath.''.$image_name.'.'.$ext;

		if (!file_exists($targetPath)) {
		    mkdir($targetPath, 0777, true);
	    }

        $thumbImgSource = $_FILES['img-submit']['tmp_name'] . 'exif';
        copy($_FILES['img-submit']['tmp_name'], $thumbImgSource);
		removeExif($thumbImgSource);

		$image = wp_get_image_editor( $thumbImgSource );

		if ( ! is_wp_error( $image ) ) {

			/* Get image orientation - LAndscape/portrait/square */
			$image_size = $image->get_size();
			/* Langscape */
			if($image_size['width'] > $image_size['height']){
				$image->resize( 268, null, false);
			}

			elseif ($image_size['width'] < $image_size['height']) {
				/* Portrait */
				$image->resize( null, 210, false);
			} else {
				/* Square */
				$image->resize( 170, 170, false);
			}



			// $image->resize( 300, 300, true );
			$image->set_quality(100);
			// $image->rotate( 180 );
			$image->save($targetFile);

//            removeExif($targetFile);


			$is_extra = 0;

			/* Update entry - > increase the number of images */
			if($role=='um_member'){
				$user_id = $_GET['user_id'];
				/* For um_members the extr images are set per theme index , like extra imgs for theme 1, theme 2, theme 3 */
				$extra_img_theme = get_user_meta($user_id, 'extra_img_'.$theme_index, true);
				if($extra_img_theme>0){
					update_lf_entry_extra_credits($_SESSION['last_entry']);
					update_user_meta($user_id, 'extra_img_'.$theme_index, $extra_img_theme-1);
					$is_extra = 1;
				}
				else{
					update_lf_entry($_SESSION['last_entry']);
				}
			}
			elseif($role=='um_entrant'){
				$user_id = $_GET['user_id'];
				$extra_credits = get_user_meta($user_id,'extra_entry_imgs',true);
				if($extra_credits>0){
					update_lf_entry_extra_credits($_SESSION['last_entry']);
					update_user_meta($user_id,'extra_entry_imgs',$extra_credits-1);
					$is_extra = 1;
				}
				else{
					update_lf_entry($_SESSION['last_entry']);
				}
			}
			/* Insert new entry to lf_photos */
			insert_lf_photo($_SESSION['last_entry'], '/LifeFramerV2/'.$image_name.'.'.$ext, $current_theme, $current_entry_theme, $is_extra);

			/* Save image to Google Drive - */
			$response = insert_image_to_drive($image_name.'.'.$ext, $_FILES['img-submit']['tmp_name'], $_FILES['img-submit']['type']);
			if(strpos($response, 'error') !== false){
				if (!file_exists($targetPath.'Google-Drive-Error')) {
				    mkdir($targetPath.'Google-Drive-Error', 0777, true);
				}
			    $image_full = wp_get_image_editor( $_FILES['img-submit']['tmp_name'] );
			    if ( ! is_wp_error( $image_full ) ) {
		    		$image_full->set_quality(90);
					$image_full->save($targetPath.'Google-Drive-Error/'.''.$image_name.'.'.$ext);
				}
			}
			$submit_response['drive'] = $response;
		}

	}

	$_SESSION['success_uploaded'] = isset($_SESSION['success_uploaded']) ? ($_SESSION['success_uploaded'] + 1) : 1;
	$submit_response['success_uploaded'] = $_SESSION['success_uploaded'];

	/*All images were uploaded*/

	if($_SESSION['success_uploaded']==$_GET['all_images']){
		/* Send confirmation mail */
		$mail_headers[] = 'From: Life Framer <info@life-framer.com>';
	    $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';
		$mail_headers[] = 'Content-type: text/html;charset=utf-8' . "\r\n";

	    $to = $user_email;
	    $customer_name = ucfirst($user_fname_lname);

		if($_GET['submission_type']=='entry'){
			$subject = "Life-Framer Submit Images Successful!";
			swph_send_upload_confirmation_email($to, $mail_headers, $customer_name);
		}
		else{
			swph_send_series_confirmation_email($to, $mail_headers, $customer_name);

		}



	}

	echo json_encode($submit_response);

	die();
}

function removeExif($image)
{
    $exif = exif_read_data($image);
    // default to no flip / no rotation
    $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : 1;

    $tmp = $image . '.noexif';

    // Open the input file for binary reading
    $f1 = fopen($image, 'rb');
    // Open the output file for binary writing
    $f2 = fopen($tmp, 'wb');

    // Find EXIF marker
    while (($s = fread($f1, 2))) {
        $word = unpack('ni', $s)['i'];
        if ($word == 0xFFE1) {
            // Read length (includes the word used for the length)
            $s = fread($f1, 2);
            $len = unpack('ni', $s)['i'];
            // Skip the EXIF info
            fread($f1, $len - 2);
            break;
        } else {
            fwrite($f2, $s, 2);
        }
    }

    // Write the rest of the file
    while (($s = fread($f1, 4096))) {
        fwrite($f2, $s, strlen($s));
    }

    fclose($f1);
    fclose($f2);

    copy($tmp, $image);
    unlink($tmp);

    resample($image, $orientation);
}

function resample($jpgFile, $orientation) {
    $image   = imagecreatefromjpeg($jpgFile);

    if (in_array($orientation, [2, 4, 5, 7])) {
        imageflip($image, IMG_FLIP_HORIZONTAL);
    }

    // Fix Orientation
    switch($orientation) {
        case 3:
        case 4:
            $image = imagerotate($image, 180, 0);
            break;
        case 5:
        case 6:
            $image = imagerotate($image, -90, 0);
            break;
        case 7:
        case 8:
            $image = imagerotate($image, 90, 0);
            break;
    }
    // Output
    imagejpeg($image, $jpgFile, 90);
}


function insert_image_to_drive($img_name,$tmp_name,$tmp_type){

	require_once get_stylesheet_directory().'/includes/google-api-php-client/vendor/autoload.php';
	$client = new Google_Client();
	// echo '<pre>'.print_r($client,1).'</pre>';
	$client->setAuthConfig(get_stylesheet_directory().'/includes/client_secret.json');

	$client->setAccessType("offline");        // offline access
	$client->setIncludeGrantedScopes(true);   // incremental auth
	$client->addScope(Google_Service_Drive::DRIVE);

	// $client->setRedirectUri($this->redirectUri);
    $client->setApprovalPrompt('force');

	$googledrive_access_token = get_option('googledrive_access_token');
	// die();

	// add_option( 'googledrive_access_token', $googledrive_access_token);

	if (isset($googledrive_access_token) && $googledrive_access_token !='') {
		/* client refresh access token */
	try {

		$client->setAccessToken($googledrive_access_token);

		if ($client->isAccessTokenExpired()) {


			// save refresh token to some variable
			$refreshTokenSaved = $client->getRefreshToken();

			// update access token
			$client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

			// pass access token to some variable
			$accessTokenUpdated = $client->getAccessToken();


			// append refresh token
			$accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
			update_option('googledrive_access_token',$accessTokenUpdated);

			//Set the new acces token
			$accessToken = $refreshTokenSaved;
			$client->setAccessToken($accessTokenUpdated);
		}

		$service = new Google_Service_Drive($client);
		$file = new Google_Service_Drive_DriveFile();

		$folder_id = '';

		    $get_folder = $service->files->listFiles(array(
		        'q' => 'name="LifeFramerV2"',
		        'spaces' => 'drive',
		        'mimeType'=>'application/vnd.google-apps.folder',
		        'fields' => 'files(id, name)',
		    ));

		    if(!empty($get_folder_files = $get_folder->files)){
		    	$get_folder_files = $get_folder->files;
		    	$folder_id = $get_folder_files[0]->id;
		    }
		    else{

		    	$folderMetadata = new Google_Service_Drive_DriveFile(array(
				    'name' => 'LifeFramerV2',
				    'mimeType' => 'application/vnd.google-apps.folder'
				));

				$folder = $service->files->create($folderMetadata, array(
				    'fields' => 'id'));

				if($folder){
					$folder_id = $folder->id;
				}

		    }

			$fileMetadata = new Google_Service_Drive_DriveFile(array(
			    'name' => $img_name,
			    'parents' => array($folder_id)
			));

			$result = $service->files->create(
			      $fileMetadata,
			      array(
			        'data' => file_get_contents($tmp_name),
			        'mimeType' => $tmp_type,
			        'uploadType' => 'media'
			      )
			);
			if($result->id) {
				$response = $result->id;
			}
		}
		catch (Exception $e) {
     		$response =  "An error occurred: " . $e->getMessage();
        }
	}
	else
	{
	  $response = 'error';
	}
	return $response;
}


add_action( 'wp_ajax_ns_submit_upload_completed', 'ns_submit_upload_completed' );
add_action( 'wp_ajax_nopriv_ns_submit_upload_completed', 'ns_submit_upload_completed' );

function ns_submit_upload_completed() {
	global $wpdb;

	check_ajax_referer( 'submitimages-ajax-nonce', 'security', false );

	$total_images = $_POST['total_images'];
	$total_images = array_filter($total_images);

	foreach ($total_images as $key => $total) {
		/* Update _lf_entry table with correct number of photos and add entry description */

		$wpdb->update(
			"{$wpdb->prefix}lf_entry",
			array(
				'no_images' => $total,
				'additional_information' => strip_tags($_POST['entry_description']).'---',

			),
			/* Where statement */
			array(
				'wp_user' => $_POST['user_login'],
				'date_time' => $_POST['date_time'],
				'theme_id' => $key
			),
			array(
				'%d',	// value1
				'%s'	// value2
			),
			array(
				'%s',
				'%s',
				'%d'
			)
		);
	}

	/* Send confirmation mail */
	$mail_headers[] = 'From: Life Framer <info@life-framer.com>';
    $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';
	$mail_headers[] = 'Content-type: text/html;charset=utf-8' . "\r\n";

    $user_login = get_user_by('login', $_POST['user_login']);
    $to = $user_login->user_email;
    $customer_name = ucfirst($user_login->first_name) . ' ' . ucfirst($user_login->last_name);

    $subject = "Life-Framer Submit Images Successful!";

    swph_send_upload_confirmation_email($to, $mail_headers, $customer_name);
    // echo $send_mail;
	die();
}


add_action('entry_submissions', 'generate_entry_submissions');

function generate_entry_submissions(){
    /* Start Date = 30 days from now */
    $end_date  = date('Y-m-d');
    $start_date  = date('Y-m-d', strtotime($end_date . " -30 days"));
?>
    <h2>Generate Entry Submissions</h2>
    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
        <input type="hidden" name="action" value="generate_csv_entries">
        <label>Start date: </label><input type="date" name="start_date" value="<?php echo $start_date; ?>">
        <label>End date: </label><input type="date" name="end_date" value="<?php echo $end_date; ?>"><br><br>
        <input type="submit" value="Generate entries" class="button button-primary">
    </form>
<?php
}
add_action('admin_action_generate_csv_entries', 'generate_csv_entries_fc');
function generate_csv_entries_fc(){
	global $wpdb;

    if(isset($_POST['start_date']) && $_POST['start_date'] != '' && isset($_POST['end_date']) && $_POST['end_date'] != '' ){
        $where = 'lf_entry.time BETWEEN "'.$_POST['start_date'].' 00:00:00.000000" AND "'.$_POST['end_date'].' 23:59:59.999999"';
        $xls_name = ' '.$_POST['start_date'].' - '. $_POST['end_date']. ' ';
    }
    else{
        $where = '1=1';
        $xls_name = '';
    }


	$entries = $wpdb->get_results(
	    "SELECT 
            lf_entry.id,
            lf_entry.time,
            lf_entry.name,
            lf_entry.wp_user,
            lf_entry.email_address,
            w.meta_value website,
            i.meta_value instagram,
            lf_entry.additional_information,
            lf_entry.payment,
            lf_payments.description,
            lf_payments.date,
            lf_entry.payment_reference,
            lf_entry.no_images
	    FROM {$wpdb->prefix}lf_entry AS lf_entry
	    INNER JOIN {$wpdb->prefix}lf_payments AS lf_payments ON lf_entry.payment_reference =  lf_payments.payment_ref
        LEFT JOIN {$wpdb->prefix}users u ON u.user_login = lf_entry.wp_user
        LEFT JOIN {$wpdb->prefix}usermeta i ON u.ID = i.user_id AND i.meta_key = 'social_instagram'
        LEFT JOIN {$wpdb->prefix}usermeta w ON u.ID = w.user_id AND w.meta_key = 'social_website'
	    WHERE
	         date_time>0 AND
	         ".$where. "
		ORDER BY lf_entry.time DESC", ARRAY_A);



	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=LifeFramer-Entries-'.$xls_name.'.csv');

	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	if (PHP_EOL == "\r\n")
	{
	    $eol = "\n";
	}
	else
	{
	    $eol = "\r\n";
	}
	// output the column headings
	fputcsv($output, array('Entry Id', 'Upload Date', 'Name', 'WP Username', 'Email', 'Website', 'Instagram', 'Additional information', 'Payment amount (dollars)', 'Payment description', 'Payment date', 'Payment reference', 'Number of photos'), ',','"', $eol);

	// fetch the data
	// loop over the rows, outputting them
	foreach($entries as $entry){
		// echo $entry;
		// die();
		$entry['additional_information'] = str_replace('<br>',"\n", $entry['additional_information']);
	    fputcsv($output, $entry, ",",'"', $eol);
	}
	fclose($output);
	exit();
}



/* Google drive functionality - OAuth */

function wpdocs_register_google_drive_oauth(){
    add_menu_page(
        __( 'Google Drive OAuth', 'textdomain' ),
        'Google Drive OAuth',
        'manage_options',
        'google-drive-oauth',
        'google_drive_oauth',
        'dashicons-flag',
        999
    );
}
add_action( 'admin_menu', 'wpdocs_register_google_drive_oauth' );

/**
 * Drive debup purpose
 */
function google_drive_oauth(){
?>

	<div class="wrap">
		<h2>Google Drive OAuth</h2>

		<?php
			// update_option('googledrive_access_token','');
			// die();
			$googledrive_access_token = get_option('googledrive_access_token');

			if(!empty($googledrive_access_token)){
					echo '<h3>Your access token:</h3>';
					echo '<pre>'.print_r($googledrive_access_token,1).'<pre>';
			}

			// echo '<p>Date:'. date("Y-m-d H:i:s") .' </p>';

			require_once get_stylesheet_directory().'/includes/google-api-php-client/vendor/autoload.php';
			$client = new Google_Client();
			$client->setAuthConfig(get_stylesheet_directory().'/includes/client_secret.json');

			$client->setAccessType("offline");        // offline access
			$client->setIncludeGrantedScopes(true);   // incremental auth
			$client->addScope(Google_Service_Drive::DRIVE);
			// $client->revokeToken();
			// die();

			$client->setApprovalPrompt('force');
			// $client->setRedirectUri($this->_redirectURI);

			if (isset($googledrive_access_token) && $googledrive_access_token !='') {
				/* client refresh access token */


				 $client->setAccessToken($googledrive_access_token);

				  if ($client->isAccessTokenExpired()) {

			  		echo '<p>token expired</p>';
			  		echo '<p>Get refresh token....</p>';
				  	// save refresh token to some variable
			        $refreshTokenSaved = $client->getRefreshToken();

			        echo '<p>Refresh token:</p>';
			        echo '<pre>'.print_r($refreshTokenSaved,1).'</pre>';


			        // update access token
			        $client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

			        // pass access token to some variable
			        $accessTokenUpdated = $client->getAccessToken();

			        echo '<p>Generating new acccess token....</p>';
			        echo '<p>New access token</p>';
			        echo '<pre>'.print_r($accessTokenUpdated,1).'</pre>';

			        // append refresh token
			        $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
			        update_option('googledrive_access_token',$accessTokenUpdated);

			        //Set the new acces token
			        $accessToken = $refreshTokenSaved;
			        $client->setAccessToken($accessTokenUpdated);

					// Array
					// (
					//     [access_token] => ya29.GltfBad3inSW694P2xzUW9l9BdBqkWpBUZd9sANMF5t6fKebZb_Y3WU5ak-dYE6fg5RhB7C6VWO8CotaegCqUhrXMN7paqOYM1UCJba4mAGuHdN6Rxry0iMiCfXT
					//     [token_type] => Bearer
					//     [expires_in] => 3600
					//     [created] => 1518364451
					//     [refresh_token] => 1/9KCqYaTrWrGfBZJ63nLL_1_9GtVn1UenPJ7TUZrvOv_02D_yIURIr_ijqYmUHvJR
					// ) [refresh_token] => 1/9KCqYaTrWrGfBZJ63nLL_1_9GtVn1UenPJ7TUZrvOv_02D_yIURIr_ijqYmUHvJR
					// )
				  }
				  else{
				  	echo 'token not expired';
				  }

		}

		else {
		  $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '?create_token=true';
		  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}

		?>
	</div>

<?php
}

/* Action to create OAuth2 for Drive */
if(isset($_GET['create_token'])&& ($_GET['create_token']=='true')){
	add_action('template_redirect','create_token_wp');
}

function create_token_wp(){
	require_once get_stylesheet_directory().'/includes/google-api-php-client/vendor/autoload.php';

	$token ='';

	$client = new Google_Client();
	$client->setAuthConfig(get_stylesheet_directory().'/includes/client_secret.json');

	$client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '?create_token=true');
	$client->addScope(Google_Service_Drive::DRIVE);
	$client->setIncludeGrantedScopes(true);
	$client->setAccessType('offline');
    $client->setApprovalPrompt('force');

	if (!isset($_GET['code'])) {
	  $auth_url = $client->createAuthUrl();
	  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
	} else {
	  $client->authenticate($_GET['code']);
	  $token = $client->getAccessToken($token);
	  update_option( 'googledrive_access_token',$token);
	  // $_SESSION['access_token'] = $client->getAccessToken();
	  $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/wp-admin/admin.php?page=google-drive-oauth';
	  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	}

}

/* Prefill User email on registration form based on email member/entrant filled in the payment form */
if(isset($_GET['transaction']) && ($_GET['transaction']!='') && isset($_GET['email']) && ($_GET['email']!='') ) {
	add_action('template_redirect','registration_process_fill_email',999);
}
function registration_process_fill_email(){
	?>
	<script type="text/javascript">
		var payment_email = '<?php echo $_GET['email'];?>';
	</script>
	<?php
}


/* Allows admin to delete photos from the users submision */

add_action( 'show_user_profile', 'extra_credits_user_profile', 10, 1 );
add_action( 'edit_user_profile', 'extra_credits_user_profile', 10, 1 );

function extra_credits_user_profile( $user ) {
	$user_id = $user->ID;
	um_fetch_user( $user_id );
	$user_role =UM()->roles()->get_um_user_role($user_id);

	if($user_role=='um_entrant'){
	?>
		<table class="form-table extra-credits">
	        <tbody>
	        	<tr>
	        		<th colspan="2"><h3>Entrant allowed images to submit</h3></th>
	        	</tr>
	    	<?php

			// $get_active_LF_entries_entrant = get_active_LF_entries_entrant();
			$get_active_LF_entries_entrant = get_active_LF_entries();

			$images_to_submit = 0;
			/* In case  there are open themes */
			if(!empty($get_active_LF_entries_entrant)){

				/* Set correct timezone */
				date_default_timezone_set('America/Los_Angeles');
				/* For entrants credit can be used 4 months after the payment */
				$limit_date = date('Y-m-d H:i:s', strtotime(' -4 months'));

				$user_email = $user->user_email;
				$user_login = $user->user_login;
				/* Get images to submit */
				$images_to_submit = get_images_left_to_submit($user_email, $user_login, $user_role, $limit_date);
				?>
				<tr>
	            	<th>
	            		<label for="user_img_left">Images left to submit:</label>
	            	</th>
	            	<td><span class="images_no left_to_submit"><?php echo $images_to_submit;?></span></td>
	        	</tr>
	            	<?php
			}

			$extra_credits = get_user_meta($user_id,'extra_entry_imgs',true);

			?>

			<tr>
	        	<th>
	        		<label for="extra_entry_imgs">Add number of free entry images:</label>
	        	</th>
	        	<td>
	        		<input type="number" min="0" value="<?php echo $extra_credits;?>" name="extra_entry_imgs" id="extra_entry_imgs">
	        	</td>
	    	</tr>
			<!-- <tr>
	        	<th>
	        		<label for="total_entry_imgs">Total entry images: </label>
	        	</th>
	        	<td><span class="images_no total_entry_imgs"><?php //echo $images_to_submit+$extra_credits;?></span></td>
	    	</tr> -->
    	 </tbody>
		</table>
	<?php
	}
	elseif($user_role=='um_member'){
		$extra_img_1 = get_user_meta($user_id,'extra_img_1',true);
		$extra_img_2 = get_user_meta($user_id,'extra_img_2',true);
		$extra_img_3 = get_user_meta($user_id,'extra_img_3',true);
	?>
	<table class="form-table extra-credits">
	        <tbody>
	        	<tr>
	        		<th colspan="2"><h3>Member extra images</h3></th>
	        	</tr>
	
				<tr>
	            	<th>
	            		<label for="user_img_left">Theme 1 extra images:</label>
	            	</th>
	            	<td><input type="number" class="regular-text" min="0" value="<?php echo $extra_img_1;?>" name="extra_img_1"></td>
	        	</tr>
				<tr>
	            	<th>
	            		<label for="user_img_left">Theme 2 extra images:</label>
	            	</th>
	            	<td><input type="number" class="regular-text" min="0" value="<?php echo $extra_img_2;?>" name="extra_img_2"></td>
	        	</tr>
				<tr>
	            	<th>
	            		<label for="user_img_left">Theme 3 extra images:</label>
	            	</th>
	            	<td><input type="number" class="regular-text" min="0" value="<?php echo $extra_img_3;?>"name="extra_img_3"></td>
	        	</tr>
    	 </tbody>
		</table>
	<?php	
	}
}


function save_extra_credits_user_profile($user_id){

    if(!current_user_can('manage_options'))
        return false;

	# save my custom field
	$extra_entries = array('extra_entry_imgs', 'extra_img_1','extra_img_2','extra_img_3');
	foreach($extra_entries as $extra_entry)
    if( isset($_POST[$extra_entry]) ) {
        update_user_meta( $user_id, $extra_entry, sanitize_text_field( $_POST[$extra_entry] ) );
    } else {
        //Delete the company field if $_POST['company'] is not set
        delete_user_meta( $user_id, $extra_entry );
    }
}
add_action( 'edit_user_profile_update', 'save_extra_credits_user_profile', 10, 1 );


/* Allows admin to delete photos from the users submision */

add_action( 'show_user_profile', 'extra_user_profile_fields', 10, 1 );
add_action( 'edit_user_profile', 'extra_user_profile_fields', 10, 1 );

function extra_user_profile_fields( $user ) { ?>

	<?php

    $user_login = $user->data->user_login;
    $user_id = $user->data->ID;

/* Past submitions functionality */
	global $wpdb;
	$current_user = wp_get_current_user();



	$images_subbmitted = $wpdb->get_results(
	"SELECT 
	lf_entry.time,
	lf_entry.theme_id,
	lf_photos.path,
	lf_entry.id as entry_id,
	lf_photos.id as photo_id,
	lf_themes.name as theme_name
    FROM {$wpdb->prefix}lf_entry AS lf_entry
    INNER JOIN {$wpdb->prefix}lf_photos AS lf_photos
    ON lf_photos.entry_id =  lf_entry.id 

	INNER JOIN {$wpdb->prefix}lf_themes AS lf_themes
    ON lf_entry.theme_id =  lf_themes.id 

    WHERE lf_entry.wp_user='$user_login' 
    and lf_entry.date_time>0 ORDER BY lf_entry.time DESC", ARRAY_A);

    if($images_subbmitted){

		$upload_dir = wp_upload_dir();
		$upload_baseurl = $upload_dir['baseurl'];
		$upload_basedir = $upload_dir['basedir'];

		echo '<input type="hidden" name="delete_img_sub-ajax-nonce" id="delete_img_sub-ajax-nonce" value="' . wp_create_nonce( 'delete_img_sub-ajax-nonce' ) . '" />';

		/* Change theme per image functionality */
    	echo '<div class="images-submitted">';
		echo '<h3>Users Past Uploads</h3>';
		$date = '';
		foreach ($images_subbmitted as $key => $result) {
			# code...
			$entry_date = date('d M Y',strtotime($result['time']));

			if($date != $entry_date){
				$submittion_date = date('d M Y', strtotime($result['time']));
				if($key!='0'){
						echo '<div class="clearfix"></div>';
						echo '</div><!--end 1 -->';
						echo '</div><!--end 2 -->';
				}
				echo '<div class="submittion-items">';
				echo '<span>- '.$submittion_date.'</span>';
				echo '<div class="submittion-images submittion-images-backend">';
			}

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
				echo '<a class="delete-img-sub" data-entry-id="'.$result['entry_id'].'" data-photo-id="'.$result['photo_id'].'" data-user-id="'.$user_id.'"></a>';
				echo '<img src="'.$upload_baseurl.''.$result['path'].'" class="'.$square_img_class.'">';
				$img_name = explode('/',$result['path']);
				$img_name = $img_name[count($img_name)-1];
				echo '<p class="image-info">';
					echo '<textarea class="entry-img_name" disabled>'.$img_name.'</textarea>';
					echo '<br>';
					echo '<span class="change-img_name" data-entry-id="'.$result['entry_id'].'" data-photo-id="'.$result['photo_id'].'" data-user-id="'.$user_id.'"><span>Change image name</span></span>';
					echo '<span style="display:none" class="save-img_name" data-entry-id="'.$result['entry_id'].'" data-photo-id="'.$result['photo_id'].'" data-user-id="'.$user_id.'"><span>Save theme</span></span>';
				echo '</p>';
				// $theme_name = $result['theme_name'];
				// $themes = explode(',',$theme_name);
				?>
				<!-- <select name="change_theme"> -->
					<?php
					// foreach($themes as $theme){
					// 	/* Check if photo is already on theme */
					// 	if(strpos($result['path'],$theme) !== false){
					// 		$selected = 'selected="selected"';
					// 	}
					// 	else{
					// 		$selected = '';
					// 	}
					// 	echo '<option value="'.$theme.'" '.$selected.'>'.$theme.'</option>';
					// }
					?>
				<!-- </select> -->
				<?php
				echo '</div><!--end submision image holder-->';
				echo '</div><!--end submision image-->';
			}
			$date = $entry_date;

		}
		echo '<div class="clearfix"></div>';
		echo '</div><!--end 1 -->';
		echo '</div><!--end 2 -->';

		echo '</div>';
		echo '</div>';
    }
?>


<?php }

/* Change image name from dashboard */
add_action( 'wp_ajax_lf_change_img_name', 'lf_change_img_name' );
add_action( 'wp_ajax_nopriv_lf_change_img_name', 'lf_change_img_name' );

function lf_change_img_name(){
	$response = array();
	$response['output'] = $response['error'] = '';

	try{
		check_ajax_referer( 'delete_img_sub-ajax-nonce', 'security', false );
		global $wpdb;
		$photo_id = $_POST['photo_id'];
		$new_image_name = $_POST['new_img_name'];

		$get_entry = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}lf_photos WHERE id = '$photo_id'");
		// print_r($get_entry);
		if(!empty($get_entry)){
			// echo print_r($get_entry,1);
			// echo "\r\n";
			$image_path = $get_entry->path;
			$image_path_array = explode('/',$image_path);

			$old_image_name = $image_path_array[count($image_path_array)-1];
			// echo $old_image_name;
			// echo "\r\n";

			/* If the name was changed in the form -> update image name in thumbnail folder and in database*/
			if(trim($old_image_name) != trim($new_image_name) ){

				$upload_dir = wp_upload_dir();
				$upload_basedir = $upload_dir['basedir'];
				// echo $upload_basedir;
				// echo "\r\n";
				// echo print_r($image_path_array,1);
				if(!file_exists($upload_basedir.'/'.$image_path_array['1'].'/'.$new_image_name)){
					// echo 'can be renamed';
					try{
						// rename( $old_name, $new_name)
						rename( $upload_basedir.'/'.$image_path_array['1'].'/'.$old_image_name, $upload_basedir.'/'.$image_path_array['1'].'/'.$new_image_name);
						/* Update DB */
						$wpdb->update(
							"{$wpdb->prefix}lf_photos",
							array(
								'path' => '/'.$image_path_array['1'].'/'.$new_image_name,
							),
							/* Where statement */
							array(
								'id' => $photo_id
							),
							array(
								'%s'
							),
							array(
								'%d'
							)
						);
						$response['output'] = trim($new_image_name);
					}
					catch (Exception $e) {
						$response['error'] =  "An error occurred: " . $e->getMessage();
				   }
				}
				else{
					/*image already exists => increment i  */
					// echo 'do more';
					// echo "\r\n";
					$new_image_name_array = explode('-',$new_image_name);
					$new_theme_name = $new_image_name_array['1'];
					$image_index = 0;
					preg_match('!\d+!', $new_theme_name, $nb);
					if(!empty($nb)){
						$image_index = $nb['0'];
					}
					$theme_name = preg_replace('/[0-9]+/', '', $new_theme_name);

					// echo 'number='.$image_index;
					// echo "\r\n";

					// echo 'generate new name='.$generate_new_name;
					$name_exists = true;
					while($name_exists){
						$image_index +=1;
						$generate_new_name = '';
						foreach($new_image_name_array as $key=> $part){
							if($key=='1'){

								$generate_new_name .= $theme_name.$image_index;
							}
							else{
								$generate_new_name .= $part;
							}

							if($key != count($new_image_name_array)-1){
								$generate_new_name .= '-';
							}
						}
						// echo $generate_new_name;
						/* Check if file exists */
						if(!file_exists($upload_basedir.'/'.$image_path_array['1'].'/'.$generate_new_name)){
							$name_exists = false;
							try{
								// rename( $old_name, $new_name)
								rename( $upload_basedir.'/'.$image_path_array['1'].'/'.$old_image_name, $upload_basedir.'/'.$image_path_array['1'].'/'.$generate_new_name);
								/* Update DB */
								$wpdb->update(
									"{$wpdb->prefix}lf_photos",
									array(
										'path' => '/'.$image_path_array['1'].'/'.$generate_new_name,
									),
									/* Where statement */
									array(
										'id' => $photo_id
									),
									array(
										'%s'
									),
									array(
										'%d'
									)
								);
							}
							catch (Exception $e) {
								$response['error'] =  "An error occurred: " . $e->getMessage();
						   }
						}
					}
					$response['output'] = trim($generate_new_name);
				}
			}
			else{
				$response['output'] = trim($new_image_name);
			}

		}
	}

	catch (Exception $e) {
		$response['error'] =  "An error occurred: " . $e->getMessage();
   }

	echo json_encode($response);
	die();
}


add_action( 'wp_ajax_delete_entry_image', 'delete_entry_image' );
add_action( 'wp_ajax_nopriv_delete_entry_image', 'delete_entry_image' );

function delete_entry_image(){

	$response = array();
	$response['extra'] = $response['normal'] = $response['error'] = '';

	try{

		check_ajax_referer( 'delete_img_sub-ajax-nonce', 'security', false );
		global $wpdb;

		$entry_id = $_POST['entry_id'];
		$photo_id = $_POST['photo_id'];
		$user_id = $_POST['user_id'];

		$get_entry = $wpdb->get_row("SELECT id, no_images, no_img_extra FROM {$wpdb->prefix}lf_entry WHERE id = '$entry_id'");

	    $normal_imgs = $get_entry->no_images;
	    $extra_imgs = $get_entry->no_img_extra;

	    if($extra_imgs > 0){
	    	/* Reduce images from extra credit */
	    	$reduce_extra_imgs = $extra_imgs - 1;
	    	$response['extra'] = 1;
	    	/*Update lf_entry table - column "no_img_extra" */
	    	$wpdb->update(
					"{$wpdb->prefix}lf_entry",
					array(
						'no_img_extra' => $reduce_extra_imgs,
					),
					/* Where statement */
					array(
						'id' => $entry_id
					),
					array(
						'%d'
					),
					array(
						'%d'
					)
				);
				/* User_extra_images */
				$user_extra_images = get_user_meta($user_id,'extra_entry_imgs',true);
				update_user_meta($user_id,'extra_entry_imgs',$user_extra_images+1);
	    }
	    else{
	    	if($normal_imgs>0){
	    		/* Reduce images from normal credit */
	    		$reduce_normal_imgs = $normal_imgs - 1;
	    		$response['normal'] = 1;
	    		/*Update lf_entry table - column "no_images" */
	    		$wpdb->update(
					"{$wpdb->prefix}lf_entry",
					array(
						'no_images' => $reduce_normal_imgs,
					),
					/* Where statement */
					array(
						'id' => $entry_id
					),
					array(
						'%d'
					),
					array(
						'%d'
					)
				);
	    	}
	    }


	    /* normal images = 0, extra images 0 => Delete entry */
	    if(($normal_imgs + $extra_imgs) =='1'){
			$wpdb->delete(
		    	"{$wpdb->prefix}lf_entry",
		    	array(
		    		'id' => $entry_id
		    	),
		    	array(
		    		'%d'
		    	)
    		);
	    }


	    $upload_dir = wp_upload_dir();
		$upload_baseurl = $upload_dir['baseurl'];
		$upload_basedir = $upload_dir['basedir'];

		$get_image = $wpdb->get_row("SELECT id,path FROM {$wpdb->prefix}lf_photos WHERE id = '$photo_id'");
	    $img_path = $get_image->path;

	    if(file_exists($upload_basedir.''.$img_path)){
	    	 unlink($upload_basedir.''.$img_path);
	    }

	    $wpdb->delete(
	    	"{$wpdb->prefix}lf_photos",
	    	array(
	    		'id' => $photo_id
	    	),
	    	array(
	    		'%d'
	    	) );

	}

	catch (Exception $e) {
     		$response['error'] =  "An error occurred: " . $e->getMessage();
        }

    echo json_encode($response);

	die();
}


/* Extend theme functionality - split entries */
add_action( 'wp_ajax_split_theme_entries', 'split_theme_entries' );
add_action( 'wp_ajax_nopriv_split_theme_entries', 'split_theme_entries' );

function split_theme_entries(){

	// echo '<pre>'.print_r($_POST,1).'</pre>';
	$theme_name = filter_input(INPUT_POST, 'theme_name');
	$theme_start = filter_input(INPUT_POST, 'theme_start_date');
	$theme_end = filter_input(INPUT_POST, 'theme_end_date');
	if($theme_name!=''){


		echo '<h3>Set deadline for entrants </h3><div class="clearfix"></div>';
		echo '<label>Deadline: </label>';
		echo '<input type="text" name="entry-deadline-entrants[]" value="'.$theme_end.'">';
		echo '<input type="hidden" name="entry-name-entrants[]" value="'.$theme_name.'">';
		echo '<hr>';
		if(strpos($theme_name, ',') === false){
			echo '<h3>Split deadline entries for entrants and members</h3><div class="clearfix"></div>';
			echo '<label>'.$theme_name.' - Deadline: </label>';
			echo '<input type="text" name="entry-deadline[]" value="'.$theme_end.'">';
			echo '<input type="hidden" name="entry-name[]" value="'.$theme_name.'">';
		}
		else{
			/*Split entryes */
			$themes = explode(',', $theme_name);
			echo '<h3>Split entries for members</h3><div class="clearfix"></div>';
			foreach($themes as $theme){
				$theme = trim($theme);
				echo '<label><b>'.$theme.'</b> - Deadline: </label>';
				echo '<input type="text" name="entry-deadline[]" value="'.$theme_end.'">';
				echo '<input type="hidden" name="entry-name[]" value="'.$theme.'"><br>';
			}
		}
	}

	else{
		echo '<span class="error">Theme name empty</span>';
	}
	die();

}

function swph_send_upload_confirmation_email($to, $mail_headers, $customer_name) {
	$subject = "Image submission successful!";
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
							<td style="border: none;" colspan="4"><h2 style="font-size: 24px; text-decoration: underline; font-weight: bold;">UPLOAD SUCCESSFUL</h2></td>
						</tr>
						<tr style="">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr>
							<td style="border: none; text-align: left;" colspan="4">
								<p>Hello ' . $customer_name . ',</p>
								<p>
									Thank you very much for your submission to Life Framer.
		                        </p>
		                        <p>
									Your image(s) have been received. Winners will be selected by our guest judges shortly after the deadline of each competition and you can expect an announcement around the middle of the following month. We will contact all winners directly at the time of announcement as well as sharing the news via our website, newsletter and social media channels.
		                        </p>
		                        <p>
									If you are not already a subscriber, we will have signed you up for our newsletter for this announcement. You can always unsubscribe using a link at the bottom of each issue. Make sure to follow us on <a href="https://www.facebook.com/lifeframer">Facebook</a> and <a href="https://www.instagram.com/life_framer">Instagram</a> for our latest news, inspiration and stories.
		                        </p>
		                        <p>
									If you have any questions or feedback, you are always welcome to contact us at via <a style="color: black; text-decoration: underline;" href="mailto:info@life-framer.com">info@life-framer.com</a>.
		                        </p>
								<p>
									Thanks for entering and good luck!<br>
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
											<a style="font-size: 24px; text-decoration: underline; color: #F15958;" href="https://www.life-framer.com/series-award/">The Series Award</a>
										</td>
									</tr>
									<tr>
										<td style="border: none;">
											Stories, interviews, tips and inspiration.
										</td>
										<td style="border: none;">
											Win a solo exhibition in a prestigious gallery.
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

function swph_send_series_confirmation_email($to, $mail_headers, $customer_name) {
	$subject = "Series Award submission successful!";
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
							<td style="border: none;" colspan="4"><h2 style="font-size: 24px; text-decoration: underline; font-weight: bold;">UPLOAD SUCCESSFUL</h2></td>
						</tr>
						<tr style="">
							<td style="border: none;" colspan="4" height="20"><br></td>
						</tr>
						<tr>
							<td style="border: none; text-align: left;" colspan="4">
								<p>Hello ' . $customer_name . ',</p>
								<p>
									Thank you very much for your submission to the Life Framer Series Award.
		                        </p>
		                        <p>
									Your series submission has been safely received. The selection will be made shortly after the deadline, and we will contact the winner directly as well as announcing to all entrants via our website, newsletter and social  <a href="https://www.facebook.com/lifeframer">Facebook</a> and <a href="https://www.instagram.com/life_framer">Instagram</a> channels.
		                        </p>
		                        <p>
								If you have any questions or feedback, you are always welcome to contact us at <a style="color: black; text-decoration: underline;" href="mailto:info@life-framer.com">info@life-framer.com</a>.
		                        </p>
								<p>
								Thanks for entering and good luck!<br>
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
