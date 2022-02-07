<?php

defined('ABSPATH') or die('No script kiddies please!');


add_action('admin_action_update_receive_email_settings', 'update_receive_email_settings');

function receiveEmailSettingEnabled()
{
    global $lf_settings_table;
    global $wpdb;
    $query = "SELECT receive_email_enabled FROM $lf_settings_table";
    return $wpdb->get_var($query);
}


function update_receive_email_settings()
{
    global $wpdb;
    global $lf_settings_table;
    $wpdb->update(
        $lf_settings_table,
        array(
            'receive_email_enabled' => isset($_POST['receive_email_enabled'])
        ),
        array('id' => 1),
        array(
            '%d'
        ),
        array('%d')
    );

    $url = urldecode($_POST['_wp_http_referer']);

    wp_safe_redirect($url);
}


/* ****** WP Heroes new add ****** */

add_action('admin_action_update_image_quality_settings', 'update_image_quality_settings');

function update_image_quality_settings()
{

    if ( get_option( 'image_quality_settings' ) !== false ) {

    // The option already exists, so we just update it.
    update_option( 'image_quality_settings', $_POST['image_quality_settings'] );

} else {

    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
    $deprecated = null;
    $autoload = 'no';
    add_option( 'image_quality_settings', $_POST['image_quality_settings'], $deprecated, $autoload );
}
// );

    $url = urldecode($_POST['_wp_http_referer']);

    wp_safe_redirect($url);
}

/* **** END WP Heroes new add **** */




add_action('admin_action_update_theme_entry_customer_email_template', 'update_theme_entry_customer_email_template');

function update_theme_entry_customer_email_template()
{
    global $wpdb;
    global $lf_settings_table;
    $wpdb->update(
        $lf_settings_table,
        array(
            'theme_entry_customer_email' => $_POST['theme_entry_customer_email']
        ),
        array('id' => 1),
        array(
            '%s'
        ),
        array('%d')
    );

    $url = urldecode($_POST['_wp_http_referer']);

    wp_safe_redirect($url);
}

add_action('admin_action_update_series_entry_customer_email_template', 'update_series_entry_customer_email_template');

function update_series_entry_customer_email_template()
{
    global $wpdb;
    global $lf_settings_table;
    $wpdb->update(
        $lf_settings_table,
        array(
            'series_entry_customer_email' => $_POST['series_entry_customer_email']
        ),
        array('id' => 1),
        array(
            '%s'
        ),
        array('%d')
    );

    $url = urldecode($_POST['_wp_http_referer']);

    wp_safe_redirect($url);
}

/**
 * @param $lf_settings_table
 * @param $wpdb
 */
function get_theme_entry_customer_email_template()
{
    global $lf_settings_table;
    global $wpdb;
    $query = "SELECT theme_entry_customer_email FROM $lf_settings_table";
    return stripslashes($wpdb->get_var($query));
}

/**
 * @param $lf_settings_table
 * @param $wpdb
 */
function get_series_entry_customer_email_template()
{
    global $lf_settings_table;
    global $wpdb;
    $query = "SELECT series_entry_customer_email FROM $lf_settings_table";
    return stripslashes($wpdb->get_var($query));
}
