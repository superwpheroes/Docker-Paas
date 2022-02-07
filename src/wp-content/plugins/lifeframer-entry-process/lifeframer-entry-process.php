<?php

/*
Plugin Name: Life-Framer Entry Process
Plugin URI: http://www.life-framer.com/
Description: Collects the data from the entry form and save the images on Life-framer's Dropbox.Allows Life-Framer to export the data in a spreadsheet and automatically downloading the images from each entrant.
Version: 1.0
Author: Clean Blocks Software Consultants
Author URI: http://www.cleanblocks.co.uk
*/

defined('ABSPATH') or die('No script kiddies please!');

require_once('lifeframer-actions.php');
require_once('lifeframer-admin.php');
require_once('lifeframer-admin-award.php');
require_once('lifeframer-admin-email.php');
require_once('lifeframer-dropbox.php');
require_once('lifeframer-install.php');
require_once('lifeframer-payments.php');
require_once('lifeframer-upload.php');
require_once('lifeframer-utils.php');




