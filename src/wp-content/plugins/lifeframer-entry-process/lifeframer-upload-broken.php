<?php
/**
 * Created by PhpStorm.
 * User: antoniomolina
 * Date: 21/03/2016
 * Time: 08:35
 */
defined('ABSPATH') or die('No script kiddies please!');

add_shortcode('lifeframer_upload_form', 'lifeframer_upload_form');

if ( !function_exists( 'get_home_path' ) )
    require_once( dirname(__FILE__) . '/../../../wp-admin/includes/file.php' );

// Requires Dropbox SDK. Install with: "composer require kunalvarma05/dropbox-php-sdk"

require_once( dirname(__FILE__) . '/vendor/autoload.php' );

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

function lifeframer_upload_form($atts)
{
    global $wpdb;
    global $transaction_table;

    nocache_headers();

    $a = shortcode_atts(array(
        'pictures' => '3',
        'redirect' => 'Thanks',
        'series' => "false"
    ), $atts);

    initialize_session();

    $isSeries = $a['series'] == "true";
    $redirect_target = $a['redirect'];

    $redirect = urlencode($_SERVER['REQUEST_URI']);

    $upload_html_code = "";

    if ($isSeries) {
        // do nothing
    } else {
        $current_theme = get_current_lifeframer_theme();
        if (!$current_theme) {
            return "<h2>Sorry, we don't have any theme open at the moment. </h2>";
        } else {
            $upload_html_code .= "2222<h2>Upload images for '$current_theme->name'</h2> ";
        }
    }

    $upload_html_code .= "
<script>
            var invalidFileErrors = [];
            jQuery( document ).ready(function() {
                jQuery('.custom-file-input').bind('change', function() {
                            
                      if(this.files[0].size > 2097152){
                          jQuery(this).next().text('ERROR: Maximum file size allowed is 2MB. Please, resize the image before you upload it.'); 
                          jQuery(this).next().css('color', 'red');
                          invalidFileErrors.push(this.id);
                      } else { 
                          jQuery(this).next().text('');
                          
                          var index = invalidFileErrors.indexOf(this.id);
                          if (index > -1) {
                            invalidFileErrors.splice(index, 1);
                          }
                      }
                });
            }); 

            function validateForm() {
                return validateEmail() && checkRulesHasBeenAccepted() && checkNoErrorsInFile();
            }

            function checkNoErrorsInFile(){
                if(!jQuery.isEmptyObject(invalidFileErrors))
                {
                    alert('Sorry but we ca not upload your images. Some of them are biggest than the max file (2MB).');
                    return false;
                }

                return true;
            }

            function validateEmail() {

                if(jQuery('#email').attr('type') == 'hidden')
                {
                    return true;
                }

                var email = jQuery('#email').val();
                var re = /.+@.+\..+/;
                var valid = re.test(email);
                if(!valid)
                {
                    alert('Please, introduce a valid email ' + email);
                }

                return valid;
            }

            function checkRulesHasBeenAccepted(){
                var rules = document.getElementById('lifeframer-rules').checked;
                if(!rules)
                {
                    alert('Before submitting your images, please read and accept the Life Framer rules ');
                }
                return rules;
            }
        </script>
        <form action=\"\" method=\"post\" class=\"wpcf7-form\" enctype=\"multipart/form-data\" onsubmit=\"return validateForm();\">
            <input type=\"hidden\" name=\"_wp_http_referer\" value=\"$redirect\"> ";

    if (!$isSeries) {
        $upload_html_code .= "<input type=\"hidden\" name=\"theme_id\" value=\"$current_theme->id\">";
    }

    $upload_html_code .= "<input type=\"hidden\" name=\"rt\" value=\"$redirect_target\">";

    if (isset($_SESSION["payment_reference"])) {
        $payment_reference = $_SESSION["payment_reference"];
        $upload_html_code .= "<input type=\"hidden\" name=\"payment_reference\" id=\"payment_reference\" value=\"$payment_reference\">";
    } else {
        if (isset($_GET["transaction"])) {
            $transaction = $wpdb->get_row($wpdb->prepare("SELECT * from $transaction_table WHERE transaction_id='%s' ORDER BY date DESC LIMIT 1", $_GET["transaction"]));
            $payment_reference = $transaction->payment_reference;
            $upload_html_code .= "<input type=\"hidden\" name=\"payment_reference\" id=\"payment_reference\" value=\"$payment_reference\">";
        }
    }


    if (isset($_SESSION["customer-name"])
        && isset($_SESSION["customer-email"])
    ) {
        $name = $_SESSION["customer-name"];
        $email = $_SESSION["customer-email"];

        $upload_html_code .= "<input type=\"hidden\" name=\"name\" id=\"name\" value=\"$name\">";
        $upload_html_code .= "<input type=\"hidden\" name=\"email\" id=\"email\" value=\"$email\">";

    } else {
        $upload_html_code .= "<p><label for=\"name\">Your name</label><br>
            <span class=\"wpcf7-form-control-wrap text-446\">
                <input name=\"name\" id=\"name\" value=\"\" size=\"40\" class=\"wpcf7-form-control wpcf7-text\"
                       type=\"text\">
            </span></p>
            <p><label for=\"email\">Your email address</label><br>
            <span class=\"wpcf7-form-control-wrap email-890\">
                <input name=\"email\" id=\"email\" value=\"\" size=\"40\"
                       class=\"wpcf7-form-control wpcf7-text wpcf7-email\"
                       type=\"email\" required></span>
            </p>";
    }

    $upload_html_code .= "
            <p><label for=\"portfolio\">Your portfolio website (if you have one)</label><br>
            <span class=\"wpcf7-form-control-wrap text-537\">
                <input name=\"portfolio\" id=\"portfolio\" value=\"\" size=\"40\"
                       class=\"wpcf7-form-control wpcf7-text\"
                       type=\"text\"></span></p>
            <p><label for=\"instagram\">Your Instagram (if you have one)</label><br>
            <span class=\"wpcf7-form-control-wrap text-537\">
                <input name=\"instagram\" id=\"instagram\" value=\"\" size=\"40\"
                       class=\"wpcf7-form-control wpcf7-text\"
                       type=\"text\"></span></p>
            <p><label for=\"additionalInformation\">";

    if ($isSeries) {
        $upload_html_code .= "Your series statement (max 400 words in English):"; // TODO Check the 400 words.
    } else {
        $upload_html_code .= "Tell us about your image(s) or series (optional - we share our favourites on our Instagram, with the hashtag #lifeframerstories):";
    }

    $upload_html_code .= "</label><br>
            <span>
                <textarea id=\"additionalInformation\"
                       name=\"additionalInformation\" rows=\"4\" cols=\"50\"></textarea>
            </span></p>
            <div class=\"rules\"><span class=\"wpcf7-form-control-wrap acceptance-971\">
                <input name=\"lifeframer-rules\" id=\"lifeframer-rules\" value=\"1\"
                       class=\"wpcf7-form-control wpcf7-acceptance\"
                       type=\"checkbox\"></span><span
                    style=\"margin-left:15px;\">I have read and accept the Life Framer </span><a
                    href=\"http://www.life-framer.com/rules\" target=\"_blank\">rules</a></div>
            <p>";

    for ($i = 0; $i < $a['pictures']; $i++) {
        $upload_html_code .= "<span class=\"wpcf7-form-control-wrap\"><input name=\"file-$i\"
                    size=\"40\" class=\"wpcf7-form-control wpcf7-file custom-file-input\"
                    aria-invalid=\"false\" type=\"file\"><span class=\"custom-file-input-error\"></span></span><br>";
    }

    $upload_html_code .= "</p>
            <input type=\"submit\" name=\"entry-process-submit\" id=\"entry-process-submit\" value=\"Send\">
        </form>";

    return $upload_html_code;
}

// register jquery and style on initialization
add_action('init', 'register_script');
function register_script() {
   // wp_register_script( 'custom_jquery', plugins_url('/js/custom-jquery.js', __FILE__), array('jquery'), '2.5.1' );
    wp_register_script('resumable', 'https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.0.2/resumable.js', false, '1.0.2', 'all');
    wp_register_script('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', false, '3.3.7', 'all');

    wp_register_style( 'bootstrapcss', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', false, '3.3.7', 'all');

}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');

function enqueue_style(){
    wp_enqueue_script('resumable');
    wp_enqueue_script( 'bootstrap' );
    wp_enqueue_style( 'bootstrapcss' );
}

add_shortcode('lifeframer_new_upload_form', 'lifeframer_new_upload_form');

function lifeframer_new_upload_form($atts)
{
    global $wpdb;
    global $transaction_table;

    nocache_headers();

    $a = shortcode_atts(array(
        'pictures' => '3',
        'redirect' => 'Thanks',
        'series' => "false"
    ), $atts);

    initialize_session();

    $isSeries = $a['series'] == "true";
    $redirect_target = $a['redirect'];

    $redirect = urlencode($_SERVER['REQUEST_URI']);

    $upload_html_code = "";

    if ($isSeries) {
        // do nothing
    } else {
        $current_theme = get_current_lifeframer_theme();
        if (!$current_theme) {
            return "<h2>Sorry, we don't have any theme open at the moment. </h2>";
        } else {
            $upload_html_code .= "-<h2>Upload images for '$current_theme->name'</h2> ";
        }
    }

    wp_enqueue_script('resumable');
    wp_enqueue_script( 'bootstrap' );
    wp_enqueue_style( 'bootstrap' );

    $upload_html_code .= "
<script>
window.onload = function() {
        var r = new Resumable({
            target: '/upload-system/upload.php',
            testChunks: true,
            fileType: ['jpg', 'jpeg', 'gif', 'png', 'tiff', 'tif', 'pdf', 'doc', 'docx'],
            maxFileSize: 2*1024*1024,
            query:{}
        });
        var retries = 0;

        r.assignDrop(document.getElementById('dropTarget'));

        function ProgressBar(ele) {
            this.thisEle = jQuery(ele);

            this.fileAdded = function() {
                (this.thisEle).removeClass('hide').find('.progress-bar').css('width','0%');
            },

            this.uploading = function(progress) {
                (this.thisEle).find('.progress-bar').attr('style', 'width:'+progress+'%');
            },

            this.finish = function() {
                (this.thisEle).addClass('hide').find('.progress-bar').css('width','0%');
            }
        }

        var generalProgressBar = new ProgressBar(jQuery('#upload-progress'));

        r.assignBrowse(document.getElementById('browseButton'));
        document.getElementById('upLoadButton').onclick = function(){
            r.upload();
            retries = 0;
        }

        var filesSpace = document.getElementById('filestobeuploaded');
        function addFileToList(file) {
            var li = document.createElement('p');
            var progressBar = document.createElement('span');
            progressBar.textContent = '0 %';
            progressBar.id = file.uniqueIdentifier + \"-progress\";
            var fileNameSpan = document.createElement('span');
            fileNameSpan.textContent = file.fileName + ' - Upload progress: ';
            var separatorSpan = document.createElement('span');
            separatorSpan.className = 'fileRemoveButton';
            separatorSpan.textContent = ' - ';

            var cancelButton = document.createElement('a');
            cancelButton.href ='#';
            cancelButton.textContent = 'Remove file';
            cancelButton.className = 'fileRemoveButton';
            cancelButton.onclick = function(ev) {
                nfiles = nfiles - 1;
                ev.preventDefault();
                file.cancel();
                filesSpace.removeChild(li);
            }

            li.appendChild(fileNameSpan);
            li.appendChild(progressBar);
            li.appendChild(separatorSpan);
            li.appendChild(cancelButton);
            filesSpace.appendChild(li);
        }

        var nfiles = 0;
        r.on('fileAdded', function(file){
            if(nfiles + 1 > " .$a['pictures']."){
                r.removeFile(file);
                alert('Sorry, the maximum number of files you are allowed to upload is " .$a['pictures']."' );
            } else {
                nfiles = nfiles + 1;
                generalProgressBar.fileAdded();
                addFileToList(file);
            }
        });

        r.on('fileProgress', function(file) {
            var progressBarToUpdate = document.getElementById(file.uniqueIdentifier + \"-progress\");
            var thisFileProgress = Math.round((file.progress(false) * 100.00)*Math.pow(10,2))/Math.pow(10,2);
            var totalProgress = Math.round((r.progress() * 100.00)*Math.pow(10,2))/Math.pow(10,2);
            progressBarToUpdate.textContent = (thisFileProgress) + '%';
            generalProgressBar.uploading(totalProgress);
        });
        r.on('fileSuccess', function(file) {
            jQuery(\"<input type='hidden' value='\" + file.fileName + \"' name='files[]' id='files[]'/>\").appendTo(jQuery(\"#upload-form\"));
            generalProgressBar.finish();
            var progressBarToUpdate = document.getElementById(file.uniqueIdentifier + \"-progress\");
            progressBarToUpdate.textContent = 'Upload Complete!';
        });
        r.on('complete', function(file) {
            var submitButton = jQuery('#new-entry-process-submit');
            submitButton.removeAttr('disabled');
            submitButton.css('visibility', 'visible');
            submitButton.css('display', 'block');
            generalProgressBar.finish();
            //alert('All files found us well! Click submit to finish the process');
        });

        var errorMsg = document.getElementById('errorMsg');

        r.on('cancel', function(file) {
            var anchors = filesSpace.getElementsByTagName('a');
            for (var i = anchors.length - 1; i >= 0; i--) {
                anchors[i].click();
            };
            errorMsg.textContent = 'Upload canceled';
        });
        var progress = document.getElementById('uploadprogress');
        r.on('progress', function() {
            progress.textContent = (Math.round((r.progress() * 100.00)*Math.pow(10,2))/Math.pow(10,2))+'%';
            if(r.progress()>0)
            {
                jQuery('.fileRemoveButton').css('visibility', 'hidden');
                jQuery('.fileRemoveButton').css('display', 'none');
            }
        });

        r.on('fileError', function(file, msg){
            var progressBarToUpdate = document.getElementById(file.uniqueIdentifier + \"-progress\");
            progressBarToUpdate.textContent = msg;
            errorMsg.textContent = msg;
        });

        r.on('fileRetry', function(file){
            var progressBarToUpdate = document.getElementById(file.uniqueIdentifier + \"-progress\");
            progressBarToUpdate.textContent = 'Retrying upload';
            retries++
            errorMsg.textContent = \"Retried \" + retries + \"time(s)\";
            if(retries > 10) {
                r.pause();
                errorMsg.textContent = 'Pausing file upload after ' + retries + ' attempts';
            }
        });
        r.on('error', function (message, file) {
            errorMsg.textContent = message;
        });
        r.on('catchAll', function(eventX) {
            console.log(eventX)
        });
    }

            function allowDrop(ev) {
                ev.preventDefault();
                jQuery('#dropTarget').attr('style', 'background-color: #e9eaee; width: 100%; min-height: 130px; border: 2px dashed #ccc; text-align: center; margin-bottom: 10px');
            }

            function drop(ev) {
                ev.preventDefault();
                jQuery('#dropTarget').attr('style', 'width: 100%; min-height: 130px; border: 2px dashed #ccc; text-align: center; margin-bottom: 10px');
            }

            function validateForm() {
                return validateEmail() && checkRulesHasBeenAccepted();
            }

            function validateEmail() {

                if(jQuery('#email').attr('type') == 'hidden')
                {
                    return true;
                }

                var email = jQuery('#email').val();
                var re = /.+@.+\..+/;
                var valid = re.test(email);
                if(!valid)
                {
                    alert('Please, introduce a valid email ' + email);
                }

                return valid;
            }

            function checkRulesHasBeenAccepted(){
                var rules = document.getElementById('lifeframer-rules').checked;
                if(!rules)
                {
                    alert('Before submitting your images, please read and accept the Life Framer rules ');
                }
                return rules;
            }
        </script>
        <form id=\"upload-form\" action=\"\" method=\"post\" class=\"wpcf7-form\" enctype=\"multipart/form-data\" onsubmit=\"return validateForm();\">
            <input type=\"hidden\" name=\"_wp_http_referer\" value=\"$redirect\"> ";

    if (!$isSeries) {
        $upload_html_code .= "<input type=\"hidden\" name=\"theme_id\" value=\"$current_theme->id\">";
    }

    $upload_html_code .= "<input type=\"hidden\" name=\"rt\" value=\"$redirect_target\">";

    if (isset($_SESSION["payment_reference"])) {
        $payment_reference = $_SESSION["payment_reference"];
        $upload_html_code .= "<input type=\"hidden\" name=\"payment_reference\" id=\"payment_reference\" value=\"$payment_reference\">";
    } else {
        if (isset($_GET["transaction"])) {
            $transaction = $wpdb->get_row($wpdb->prepare("SELECT * from $transaction_table WHERE transaction_id='%s' ORDER BY date DESC LIMIT 1", $_GET["transaction"]));
            $payment_reference = $transaction->payment_reference;
            $upload_html_code .= "<input type=\"hidden\" name=\"payment_reference\" id=\"payment_reference\" value=\"$payment_reference\">";
        }
    }


    if (isset($_SESSION["customer-name"])
        && isset($_SESSION["customer-email"])
    ) {
        $name = $_SESSION["customer-name"];
        $email = $_SESSION["customer-email"];

        $upload_html_code .= "<input type=\"hidden\" name=\"name\" id=\"name\" value=\"$name\">";
        $upload_html_code .= "<input type=\"hidden\" name=\"email\" id=\"email\" value=\"$email\">";

    } else {
        $upload_html_code .= "<p><label for=\"name\">Your name</label><br>
            <span class=\"wpcf7-form-control-wrap text-446\">
                <input name=\"name\" id=\"name\" value=\"\" size=\"40\" class=\"wpcf7-form-control wpcf7-text\"
                       type=\"text\">
            </span></p>
            <p><label for=\"email\">Your email address</label><br>
            <span class=\"wpcf7-form-control-wrap email-890\">
                <input name=\"email\" id=\"email\" value=\"\" size=\"40\"
                       class=\"wpcf7-form-control wpcf7-text wpcf7-email\"
                       type=\"email\" required></span>
            </p>";
    }

    $upload_html_code .= "
            <p><label for=\"portfolio\">Your portfolio website (if you have one)</label><br>
            <span class=\"wpcf7-form-control-wrap text-537\">
                <input name=\"portfolio\" id=\"portfolio\" value=\"\" size=\"40\"
                       class=\"wpcf7-form-control wpcf7-text\"
                       type=\"text\"></span></p>
            <p><label for=\"instagram\">Your Instagram (if you have one)</label><br>
            <span class=\"wpcf7-form-control-wrap text-537\">
                <input name=\"instagram\" id=\"instagram\" value=\"\" size=\"40\"
                       class=\"wpcf7-form-control wpcf7-text\"
                       type=\"text\"></span></p>
            <p><label for=\"additionalInformation\">";

    if ($isSeries) {
        $upload_html_code .= "Your series statement (max 400 words in English):"; // TODO Check the 400 words.
    } else {
        $upload_html_code .= "Tell us about your image(s) or series (optional - we share our favourites on our Instagram, with the hashtag #lifeframerstories):";
    }

    $upload_html_code .= "</label><br>
            <span>
                <textarea id=\"additionalInformation\"
                       name=\"additionalInformation\" rows=\"4\" cols=\"50\"></textarea>
            </span></p>
            <div class=\"rules\"><span class=\"wpcf7-form-control-wrap acceptance-971\">
                <input name=\"lifeframer-rules\" id=\"lifeframer-rules\" value=\"1\"
                       class=\"wpcf7-form-control wpcf7-acceptance\"
                       type=\"checkbox\"></span><span
                    style=\"margin-left:15px;\">I have read and accept the Life Framer </span><a
                    href=\"http://www.life-framer.com/rules\" target=\"_blank\">rules</a></div>
            <p>";

        $upload_html_code .= "
<!--</div>-->
<div class=\"progress hide\" id=\"upload-progress\">
    <div class=\"progress-bar progress-bar-success progress-bar-striped\" role=\"generalProgressBar\"   style=\"width: 0%\">
        <span class=\"sr-only\"></span>
    </div>
</div>
<div style='width: 100%; min-height: 130px; border: 2px dashed #ccc; text-align: center; margin-bottom: 10px'
 ondragover='allowDrop(event)' ondrop='drop(event)' id='dropTarget'>
    <h2>Drop files here or</h2>
    <p>
        <button type=\"button\" class=\"btn btn-success\" aria-label=\"Add file\" id=\"browseButton\">
                <span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> Add file
        </button>
        <div id='filestobeuploaded'>

        </div>
    </p>
</div>
<div style='min-height: 50px;'>
    <button type=\"button\" class=\"btn btn-info\" aria-label=\"Start upload\" id=\"upLoadButton\">
        <span class=\"glyphicon glyphicon-upload\" aria-hidden=\"true\"></span> Start upload
    </button>
</div>
<span id='errorMsg' style='color: red;'></span>
<div id='uploadprogress' style='font-size: large;'>0 %</div>

";

    $upload_html_code .= "</p>
            <input type=\"submit\" name=\"new-entry-process-submit\" style='visibility: hidden; display: none;' id=\"new-entry-process-submit\" value=\"Send\" disabled>
        </form>";

    return $upload_html_code;
}

error_reporting(E_ALL);

$app = new DropboxApp("etc0how3ybqqgl3", "nskqvdujjybfg9g", "WUXRfe3slp8AAAAAAAL-kGdd8BubLJYgLwDfKQ9K4S6oZcZFxsQvbDYgqZCudaFD");
$dropbox = new Dropbox($app);

function entry_process()
{
    global $wpdb;
    global $entry_table;
    global $photos_table;
    global $dropbox;

    check_and_print_file_errors();

    $wpdb->insert(
        $entry_table,
        array(
            'time' => current_time('mysql'),
            'name' => $_POST['name'],
            'payment' => 0, /* TODO remove */
            'no_images' => 0,
            'wp_user' => is_user_logged_in() ? wp_get_current_user()->user_login : NULL,
            'email_address' => $_POST['email'],
            'portfolio' => $_POST['portfolio'],
            'instagram' => $_POST['instagram'],
            'additional_information' => $_POST['additionalInformation'],
            'payment_reference' => $_POST['payment_reference'],
            'theme_id' => $_POST['theme_id']
        )
    );

    $entry_id = $wpdb->insert_id;

    if ($entry_id == 0) {
        // This should only happens if the DB is not able
        // to save our information (usually that means that the
        // DB is down.
        handleDatabaseError();
//            saveEntryDataInErrorFile();
    }

    $is_series = !isset($_POST['theme_id']);
    if ($is_series) {
        $dropbox_path = "/Series";
    } else {
        $theme = get_theme_name($_POST['theme_id']);
        $dropbox_path = "/Themes/$theme";
    }

    $user_name = $_POST['name'];

    $i = 0;

    $file_index = "file-$i";
    $no_uploaded_images = 0;

    try {
        while (!empty($_FILES[$file_index])) {

            if ($_FILES[$file_index]['size'] != 0) {

                $upload_name = sanitize_file_name($_FILES[$file_index]["name"]);

                $dropboxFile = new DropboxFile($_FILES[$file_index]["tmp_name"]);
                $dropbox->upload($dropboxFile, $dropbox_path . "/" . $entry_id . '-' . $upload_name, ['autorename' => true]);

                /*
                 * id INT NOT NULL AUTO_INCREMENT ,
                    entry_id mediumint(9) NOT NULL,
                    path VARCHAR(255) NOT NULL ,
                    additional_info TEXT NOT NULL ,
                 */

                $wpdb->insert(
                    $photos_table,
                    array(
                        'entry_id' => $entry_id,
                        'path' => $dropbox_path . "/" . $entry_id . '-' . $upload_name,
                        'additional_info' => "Not available"
                    ),
                    array(
                        '%d',
                        '%s',
                        '%s'
                    )
                );

                $no_uploaded_images++;
            }
            $i++;
            $file_index = "file-$i";
        }
    } catch (Exception $e) {
        handleDropboxError();
    }

    // update no_images on the denormalized column
    $wpdb->update(
        $entry_table,
        array(
            'no_images' => $no_uploaded_images
        ),
        array('id' => $entry_id),
        array(
            '%d'
        ),
        array('%d')
    );

    sendCustomerEmail($user_name, $is_series);
    sendEmailToLifeframer($user_name, $dropbox_path);

    if (!isset ($_POST['_wp_http_referer']))
        die('Missing target.');

    $url = get_permalink(get_page_by_title($_POST['rt']));
    wp_safe_redirect($url);
    exit;
}

function new_entry_process()
{
    global $wpdb;
    global $entry_table;
    global $photos_table;
    global $dropbox;

    check_and_print_file_errors();

    $wpdb->insert(
        $entry_table,
        array(
            'time' => current_time('mysql'),
            'name' => $_POST['name'],
            'payment' => 0, /* TODO remove */
            'no_images' => 0,
            'wp_user' => is_user_logged_in() ? wp_get_current_user()->user_login : NULL,
            'email_address' => $_POST['email'],
            'portfolio' => $_POST['portfolio'],
            'instagram' => $_POST['instagram'],
            'additional_information' => $_POST['additionalInformation'],
            'payment_reference' => $_POST['payment_reference'],
            'theme_id' => $_POST['theme_id']
        )
    );

    $entry_id = $wpdb->insert_id;

    if ($entry_id == 0) {
        // This should only happens if the DB is not able
        // to save our information (usually that means that the
        // DB is down.
        handleDatabaseError();
//            saveEntryDataInErrorFile();
    }

    $is_series = !isset($_POST['theme_id']);
    if ($is_series) {
        $dropbox_path = "/Series";
    } else {
        $theme = get_theme_name($_POST['theme_id']);
        $dropbox_path = "/Themes/$theme";
    }

    $user_name = $_POST['name'];

    $i = 0;

    $no_uploaded_images = 0;
    $upload_path = get_home_path() . "/upload-system/temp";

    try {
        foreach($_POST['files'] as $current_file){

            $upload_name = sanitize_file_name($current_file);

            $dropboxFile = new DropboxFile($upload_path ."/".$current_file);
            $dropbox->upload($dropboxFile, $dropbox_path . "/" . $entry_id . '-' . $upload_name, ['autorename' => true]);

            $wpdb->insert(
                $photos_table,
                array(
                    'entry_id' => $entry_id,
                    'path' => $dropbox_path . "/" . $entry_id . '-' . $upload_name,
                    'additional_info' => "Not available"
                ),
                array(
                    '%d',
                    '%s',
                    '%s'
                )
            );

            $no_uploaded_images++;
            //}
            $i++;
            unlink($upload_path ."/".$current_file);
        }
    } catch (Exception $e) {
        handleDropboxErrorInNewSystem();
    }

    // update no_images on the denormalized column
    $wpdb->update(
        $entry_table,
        array(
            'no_images' => $no_uploaded_images
        ),
        array('id' => $entry_id),
        array(
            '%d'
        ),
        array('%d')
    );

    sendCustomerEmail($user_name, $is_series);
    sendEmailToLifeframer($user_name, $dropbox_path);

    if (!isset ($_POST['_wp_http_referer']))
        die('Missing target.');

    $url = get_permalink(get_page_by_title($_POST['rt']));
    wp_safe_redirect($url);
    exit;
}

/**
 * @return array
 */
function check_and_print_file_errors()
{
    $i = 0;
    $errors = array();
    $detected_errors = 0;
    $file_index = "file-$i";
    while (!empty($_FILES[$file_index])) {
        if ($_FILES[$file_index]['size'] != 0) {
            $detected_errors = checkForErrors($file_index, $errors, $detected_errors);
        }
        $i++;
        $file_index = "file-$i";
    }
    if ($detected_errors > 0) {
        $current_error = 0;
        echo '<h2>Sorry, we found the following errors on your files:</h2>';
        echo '<ul>';
        while ($current_error < $detected_errors) {
            echo '<li>' . $errors[$current_error] . '</li>';
            $current_error++;
        }
        echo '</ul>';
        echo '<p>Please, <a href="javascript:history.back()">click here to go back</a> and correct the errors before submit your photos again</p>';
        die();
    }
}

/**
 * @param $mail_headers
 */
function sendCustomerEmail($customer_name, $is_series)
{
    $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
    $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';

    $to = $_POST['email'];
    $subject = "Your images were uploaded successfully!";
    if ($is_series) {
        $content = get_series_entry_customer_email_template();
    } else {
        $content = get_theme_entry_customer_email_template();
    }
    $content = str_replace("[NAME]", $customer_name, $content);
    return wp_mail($to, $subject, $content, $mail_headers);
}

/**
 * @param $mail_headers
 */
function sendEmailToLifeframer($customerName, $dropbox_path)
{
    if (receiveEmailSettingEnabled()) {
        $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
        $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';

        $subject = "$customerName uploaded new photos to $dropbox_path!";
        $message = "Hello. $customerName just uploaded some photos. You can find them under $dropbox_path. ";
        $to = "ralph@life-framer.com,amauri@life-framer.com";
        wp_mail($to, $subject, $message, $mail_headers);
    }
}


function handleDatabaseError()
{
    $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
    $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';

    $to = "ralph@life-framer.com,amauri@life-framer.com,hello@cleanblocks.co.uk";
    //$to = "ajmolinagutierrez@gmail.com,mmarigigena@gmail.com";
    $subject = "Life-Framer ERROR saving data to the database.";

    $message = "Hi, we have tried to save an entry to the database but it look like BlueHost is having problems in their infrastructure.
    \nThe data we tried to save is below:\n";
    $message .= "Name: " . $_POST['name'] . "\n";
    $message .= "Email: " . $_POST['email'] . "\n";
    $message .= "Portfolio: " . $_POST['portfolio'] . "\n";
    $message .= "Instragram: " . $_POST['instagram'] . "\n";
    $message .= "Additional Information: " . $_POST['additionalInformation'] . "\n";
    $message .= "Braintree Payment Reference: " . $_POST['payment_reference'] . "\n";

    $attachments = get_photos_for_attachment();
    wp_mail($to, $subject, $message, $mail_headers, $attachments);
}

function handleDropboxErrorInNewSystem()
{
    $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
    $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';

    $to = "ralph@life-framer.com,amaury@life-framer.com,hello@cleanblocks.co.uk";
//    $to = "ajmolinagutierrez@gmail.com,mmarigigena@gmail.com";
    $subject = "Life-Framer ERROR uploading images to Dropbox.";

    $message = "Hi, we have tried to upload the images to Dropbox using the new system but it look like we are having problems.
    \nThis email has attached the images and the data for the entry:\n";
    $message .= "Name: " . $_POST['name'] . "\n";
    $message .= "Email: " . $_POST['email'] . "\n";
    $message .= "Portfolio: " . $_POST['portfolio'] . "\n";
    $message .= "Instragram: " . $_POST['instagram'] . "\n";
    $message .= "Additional Information: " . $_POST['additionalInformation'] . "\n";
    $message .= "Braintree Payment Reference: " . $_POST['payment_reference'] . "\n";

    $attachments = get_photos_for_attachment_new_upload_system();
    wp_mail($to, $subject, $message, $mail_headers, $attachments);
}

function handleDropboxError()
{
    $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
    $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';

    $to = "ralph@life-framer.com,amaury@life-framer.com,hello@cleanblocks.co.uk";
//    $to = "ajmolinagutierrez@gmail.com,mmarigigena@gmail.com";
    $subject = "Life-Framer ERROR uploading images to Dropbox.";

    $message = "Hi, we have tried to upload the images to Dropbox but it look like we are having problems.
    \nThis email has attached the images and the data for the entry:\n";
    $message .= "Name: " . $_POST['name'] . "\n";
    $message .= "Email: " . $_POST['email'] . "\n";
    $message .= "Portfolio: " . $_POST['portfolio'] . "\n";
    $message .= "Instragram: " . $_POST['instagram'] . "\n";
    $message .= "Additional Information: " . $_POST['additionalInformation'] . "\n";
    $message .= "Braintree Payment Reference: " . $_POST['payment_reference'] . "\n";

    $attachments = get_photos_for_attachment();
    wp_mail($to, $subject, $message, $mail_headers, $attachments);
}

/**
 * @param $file_index
 * @param $attachments
 * @param $i
 * @return array
 */
function get_photos_for_attachment()
{
    $i = 0;
    $file_index = "file-$i";
    $attachments = array();

    while (!empty($_FILES[$file_index])) {

        if ($_FILES[$file_index]['size'] != 0) {

            $tmp_filename = $_FILES[$file_index]["tmp_name"];
            $filename = $_FILES[$file_index]['name'];

            $folder = WP_CONTENT_DIR . '/uploads/dropbox_errors/';
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $destination = $folder . basename($filename);
            move_uploaded_file($tmp_filename, $destination);
            $attachments[] = $destination;
        }

        $i++;
        $file_index = "file-$i";

    }
    return $attachments;
}

/**
 * @param $file_index
 * @param $attachments
 * @param $i
 * @return array
 */
function get_photos_for_attachment_new_upload_system()
{
    $attachments = array();

    foreach($_POST['files'] as $current_file){
        $attachments[] = get_home_path() . "/upload-system/temp" ."/".$current_file;
    }

    return $attachments;
}


/**
 * @param $file_index
 * @param $errors
 * @param $detected_errors
 * @return mixed
 */
function checkForErrors($file_index, &$errors, $detected_errors)
{
    if (hasUploadErrors($file_index)) {
        $errors[$detected_errors] = "The file " . $_FILES[$file_index]['name'] . " is corrupt. Please, try again.";
        $detected_errors++;
    }

    if (!hasValidSize($file_index)) {
        $errors[$detected_errors] = "The file " . $_FILES[$file_index]['name'] . " is too big. Maximum allowed size is 2MB. Please, adjust the size and try again.";
        $detected_errors++;
    }

    if (!isValidMimetype($file_index)) {
        $errors[$detected_errors] = "The file " . $_FILES[$file_index]['name'] . " have an unsupported type. We only accept 'image/jpeg', 'image/png', 'image/gif' mimetypes.";
        $detected_errors++;
    }
    return $detected_errors;
}
