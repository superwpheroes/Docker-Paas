<?php

/**

 * Header-1 template.

 *

 * @author     ThemeFusion

 * @copyright  (c) Copyright by ThemeFusion

 * @link       http://theme-fusion.com

 * @package    Avada

 * @subpackage Core

 */



// Do not allow directly accessing this file.

if ( ! defined( 'ABSPATH' ) ) {

    exit( 'Direct script access denied.' );

}

?>

<div class="fusion-header-sticky-height"></div>







<div class="fusion-header">

    <!--Show red edit banner if user matching criteria -->

    <?php if( is_user_logged_in() && !user_has_completed_profile() && !isset($_SESSION["hide_red_edit_banner"]) ) : ?>

       <div class="looged-in-banner">

            <div class="fusion-row">

                Build your profile, share your work, join the community -
                <a href="<?php if(function_exists('um_edit_profile_url')){ echo um_edit_profile_url(); }?>">Edit now</a>

                </div>

            <span class="close-logged-in-banner"><i class="fa fa-times-thin"></i></span>

       </div>

    <?php endif; ?>



    <div class="fusion-row">

        <?php avada_logo(); ?>

        <?php avada_main_menu(); ?>

    </div>



</div>
