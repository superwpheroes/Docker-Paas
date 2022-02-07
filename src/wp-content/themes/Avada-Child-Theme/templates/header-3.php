<?php

/**

 * Header-3 template.

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

                Build your profile, share your work, join the community - <a href="<?php if(function_exists('um_edit_profile_url')){ echo um_edit_profile_url(); }?>">Edit now</a>

            </div>

            <span class="close-logged-in-banner"><i class="fa fa-times-thin"></i></span>

        </div>

    <?php endif; ?>





    <div class="fusion-row">

        <div class="fusion-header-v6-content">

            <?php

            avada_logo();

            $menu = avada_main_menu( true );

            ?>



            <div class="fusion-flyout-menu-icons">

                <?php if ( class_exists( 'WooCommerce' ) && Avada()->settings->get( 'woocommerce_cart_link_main_nav' ) ) :

                    global $woocommerce;



                    $cart_link_text  = '';

                    $cart_link_class = '';

                    if ( Avada()->settings->get( 'woocommerce_cart_counter' ) && $woocommerce->cart->get_cart_contents_count() ) {

                        $cart_link_text  = '<span class="fusion-widget-cart-number">' . $woocommerce->cart->get_cart_contents_count() . '</span>';

                        $cart_link_class = ' fusion-widget-cart-counter';

                    }

                    ?>

                    <div class="fusion-flyout-cart-wrapper">

                        <a href="<?php echo esc_attr( get_permalink( get_option( 'woocommerce_cart_page_id' ) ) ); ?>" class="fusion-icon fusion-icon-shopping-cart<?php echo esc_attr( $cart_link_class ); ?>" aria-hidden="true"><?php echo wp_kses_post( $cart_link_text ); ?></a>

                    </div>

                <?php endif; ?>



                <?php if ( Avada()->settings->get( 'main_nav_search_icon' ) ) : ?>

                    <div class="fusion-flyout-search-toggle">

                        <div class="fusion-toggle-icon">

                            <div class="fusion-toggle-icon-line"></div>

                            <div class="fusion-toggle-icon-line"></div>

                            <div class="fusion-toggle-icon-line"></div>

                        </div>

                        <a class="fusion-icon fusion-icon-search" aria-hidden="true"></a>

                    </div>

                <?php endif; ?>



                <div class="fusion-flyout-menu-toggle" aria-hidden="true">

                    <div class="fusion-toggle-icon-line"></div>

                    <div class="fusion-toggle-icon-line"></div>

                    <div class="fusion-toggle-icon-line"></div>

                </div>

            </div>

        </div>



        <div class="fusion-main-menu fusion-flyout-menu" role="navigation" aria-label="Main Menu">

            <?php echo wp_kses_post( $menu ); ?>

        </div>



        <?php if ( Avada()->settings->get( 'main_nav_search_icon' ) ) : ?>

            <div class="fusion-flyout-search">

                <?php get_search_form(); ?>

            </div>

        <?php endif; ?>



        <div class="fusion-flyout-menu-bg"></div>

    </div>

</div>

