<?php

defined('ABSPATH') or die('No script kiddies please!');


register_activation_hook(__FILE__, 'entry_process_install');

global $wpdb;

global $entry_process_db_version;
$entry_process_db_version = '1.0';

global $entry_table;
$entry_table = $wpdb->prefix . "lf_entry";

global $theme_table;
$theme_table = $wpdb->prefix . "lf_themes";

global $photos_table;
$photos_table = $wpdb->prefix . "lf_photos";

global $lf_settings_table;
$lf_settings_table = $wpdb->prefix . "lf_settings";

global $payment_table;
$payment_table = $wpdb->prefix . "lf_payments";

global $transaction_table;
$transaction_table = $wpdb->prefix . "lf_transactions";

function entry_process_install()
{
    global $wpdb;
    global $entry_table;
    global $payment_table;
    global $transaction_table;
    global $theme_table;
    global $photos_table;
    global $lf_settings_table;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $entry_table (
  		id mediumint(9) NOT NULL AUTO_INCREMENT,
  		time datetime DEFAULT NULL,
  		name text NOT NULL,
  		payment mediumint(9) NOT NULL,
  		no_images mediumint(9) NOT NULL,
  		wp_user VARCHAR(255) NULL,
  		email_address text NOT NULL,
  		instagram varchar(255) NULL,
  		portfolio varchar(255) DEFAULT '' NOT NULL,
  		additional_information text DEFAULT '' NOT NULL,
  		theme_id mediumint(9) NULL,
  		payment_reference VARCHAR(255) NULL,
  		deleted boolean NOT NULL DEFAULT FALSE
  		UNIQUE KEY id (id)
	) $charset_collate;

	CREATE TABLE $photos_table (
	    id INT NOT NULL AUTO_INCREMENT ,
	    entry_id mediumint(9) NOT NULL,
	    path VARCHAR(255) NOT NULL ,
	    additional_info TEXT NOT NULL ,
	    PRIMARY KEY (id)
	    ) $charset_collate;

	CREATE TABLE $theme_table (
  		id mediumint(9) NOT NULL AUTO_INCREMENT,
  		start datetime NOT NULL,
  		end datetime NOT NULL,
  		name text NOT NULL,
  		deleted boolean NOT NULL DEFAULT FALSE,
  		UNIQUE KEY id (id)
	) $charset_collate;
	CREATE TABLE $payment_table (
  		id mediumint(9) NOT NULL AUTO_INCREMENT,
  		date datetime NOT NULL,
  		name text NOT NULL,
  		referer text NULL,
  		wp_user VARCHAR(255) NULL,
  		email_address text NOT NULL,
  		amount DOUBLE NOT NULL,
  		vat DOUBLE NOT NULL DEFAULT 0,
  		country VARCHAR(255) NULL,
  		payment_ref VARCHAR(255) NOT NULL,
  		description VARCHAR(255) NOT NULL,
  		UNIQUE KEY id (id)
	) $charset_collate;
	CREATE TABLE $transaction_table (
  		id mediumint(9) NOT NULL AUTO_INCREMENT,
  		date datetime NOT NULL,
  		transaction_id VARCHAR(255) NOT NULL,
  		wp_user VARCHAR(255) NULL,
  		amount DOUBLE NOT NULL,
  		description VARCHAR(255) NOT NULL,
  		promote_to_member BOOLEAN NOT NULL DEFAULT false,
  		after_payment VARCHAR(255) NULL,
  		in_flight BOOLEAN NOT NULL DEFAULT true,
  		errors text NULL,
  		referer text NULL,
  		UNIQUE KEY id (id)
	) $charset_collate;
	CREATE TABLE $lf_settings_table (
  		id mediumint(9) NOT NULL AUTO_INCREMENT,
  		theme_entry_customer_email text NOT NULL,
  		series_entry_customer_email text NOT NULL,
  		receive_email_enabled BOOLEAN NOT NULL DEFAULT true,
  		UNIQUE KEY id (id)
	) $charset_collate;";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}