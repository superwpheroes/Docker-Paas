<?php

function submit_images_series_award()
{
    ob_start();

    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;
    $user_login = $current_user->user_login;
    $current_user_ID = $current_user->ID;

    /* Get user role */
    $user_role = UM()->roles()->get_um_user_role($current_user_ID);
    ?>

    <div class="submit-images submit-images-<?php echo $user_role;?>">

        <?php

        /* Get current themes */
        global $wpdb;

        $active_entries = get_active_LF_entries(true);

        /* In case there are open themes */
        if (!empty($active_entries)) {
            $active_entry = $active_entries[0];

            /* Set correct timezone */
            date_default_timezone_set('America/Los_Angeles'); ?>

            <div class="submit-images-intro lf-limit-width">
                <div class="row">
                    <div class="col-md-6">
                        <div class="custom-separator"><div></div></div>
                        <h3>Submit<br/>your series</h3>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-separator" style="visibility: hidden;"><div></div></div>
                        <h3 style="text-transform: none;">Deadline:<br/><span style="color: #f05858;"><?php echo date('d F Y', strtotime($active_entry->end_members)); ?></span></h3>
                    </div>
                </div>
                <div class="row" style="margin-top: 40px;">
                    <div class="col-md-12">
                        <p style="text-align: left;">We're interested in photographic projects of 5-20 images on any topic, and of any style and genre.</p>
                    </div>
                </div>

            </div><!--end submit-images-intro-->

            <div class="submission-steps lf-limit-width">
                <div class="submission-step-1">
                    <p>Drag your images into the upload boxes, and add any text you wish to include in the description boxes. There is no need to name your files in a particular format.</p>
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
                                    Deadline is 23:59 Pacific Time Zone.<br/>
                                    If you have any questions, or if something is unclear you can browse our help topics.
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

            $max_per_theme = 20;
            echo '<input type="hidden" value="'. $max_per_theme .'" id="img_to_submit">';
            echo '<input type="hidden" value="'. $user_login .'" id="user_login">';
            echo '<input type="hidden" value="'. $current_user->first_name .' '.$current_user->last_name .'" id="user_fname_lname">';
            echo '<input type="hidden" value="'. $current_user->user_email .'" id="user_email">';
            echo '<input type="hidden" value="'. $current_user_ID .'" id="user_id">';
            echo '<input type="hidden" value="'. $user_role .'" id="user_role">';

            echo '<input type="hidden" name="submitimages-ajax-nonce" id="submitimages-ajax-nonce" value="' . wp_create_nonce( 'submitimages-ajax-nonce' ) . '" />';


            /* Unset Session last entry id */
            unset($_SESSION['last_entry']);

            if (!session_id()) {
                session_start();
            }

            $_SESSION['success_uploaded'] = '0';

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

            /* Get split theme names */
                $_SESSION['key-0'] = '0'; ?>

                <?php if ($user_role == 'um_member') {
                    $member_entries_submitted = entry_submitted_images($active_entry->theme_id, $user_login, $user_role);
                    $already_submitted = array_key_exists($active_entry->id, $member_entries_submitted) && $member_entries_submitted[$active_entry->id] > 0;
                    if ($already_submitted) { ?>
                        <div class="submit-theme-photos lf-limit-width">
                            <label class="member-img-available mb0">Thank you. Your entry has been saved.</label>
                        </div>
                        <div class="submit-images-holder">
                            <hr />
                        </div>
                    <?php } else { ?>
                        <input type="hidden" id="key-0" value="0">
                        <div class="submit-theme-photos lf-limit-width" >
                            <label class="headline-label">
                                Upload images for the Series Award
                            </label>
                            <div class="clear"></div>
                            <label>Drag or select your images</label>
                            <div class="upload-form upload-form-entrant lf-upload-form" id="form-0" data-role="<?php echo $user_role;?>"
                                 data-box="key-0"
                                 data-entry_item="<?php echo trim($active_entry->name);?>"
                                 date-time="<?php echo strtotime('now');?>"
                                 data-current_theme="<?php echo $active_entry->theme_id;?>"
                                 data-current_entry_theme="<?php echo $active_entry->id;?>"
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

                            <textarea name="submit-images-description[0]" data-entry_desc="<?php echo trim($active_entry->name);?>" placeholder="<?php echo $textarea_ph;?>"></textarea>
                            <div class="clear"></div>
                        </div>	<!-- END submit-theme-photos-->

                        <div class="submit-images-holder">
                            <hr>
                            <div class="lf-limit-width">
                                <p class="mb0">Review your entry carefully. Changes cannot be made once submitted.</p>
                                <button class="dz-submit-images"  data-submission_type="series">
                                    <span class="submit">Submit</span>
                                    <i class="fa fa-spinner fa-spin"></i>
                                </button>
                            </div>
                            <hr>
                        </div>
                    <?php } ?>

                <?php } elseif ($user_role == 'um_entrant') { ?>
                    <div class="submit-theme-photos lf-limit-width">
                        <label class="headline-label">
                            Upload images for the Series Award
                        </label>
                        <div class="submit-series-award-disabled bg-black text-white" >
                            <p class="text-white mb0">
                                The Series Award is only open to Members.
                                <a href="/my-lf-entrant#mylf-payment-table" class="text-white">Become a Member now</a>
                                to submit a series and win a solo show.
                            </p>
                        </div>
                    </div>
                <?php }
        } else {
            echo '<h4 class="text-center">There are no open themes at the moment!</h4>';
            echo '<div class="submition-help"><hr /></div>';
        } ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('submit_images_series_award', 'submit_images_series_award');


function past_uploads_series_award($atts = [], $content = null) {
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
        INNER JOIN {$wpdb->prefix}lf_photos AS lf_photos ON (lf_photos.entry_id = lf_entry.id)
        INNER JOIN {$wpdb->prefix}lf_themes as lf_themes ON (lf_entry.theme_id = lf_themes.id) 
        WHERE 
            lf_entry.wp_user='$current_user->user_login' AND
            lf_themes.is_series_award = 1 AND
			lf_entry.time>='2018-03-01'
			
        ORDER BY
            lf_entry.time DESC,
            lf_photos.theme_entry_id ASC,
            lf_photos.id ASC
    ", ARRAY_A);

    echo '<div class="images-submitted images-submitted-'.$atts['role'].'" id="my-past-uploads">';
    echo '<h3 id="pastuploads">SERIES AWARD PAST UPLOADS</h3>';

    if ($images_subbmitted) {

        if(isset($_GET['success']) && $_GET['success']=='true'){
            echo '<div class="lf-img-submitted">';
            echo '<span class="lf-img-submitted-dismiss"><i class="fa fa-times-thin"></i></span>';
            echo '<p>Thank you. Your entry has been made. You can see your submission below.</p>';
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

    } else{
        echo '<div style="display: block; height: 50px;"></div>';
    }

    echo '</div><!-- END .my-past-uploads -->';

    return ob_get_clean();
}

add_shortcode('past_uploads_series_award','past_uploads_series_award');
