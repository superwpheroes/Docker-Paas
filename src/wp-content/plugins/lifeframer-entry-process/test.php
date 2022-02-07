<?php
/**
 * Created by PhpStorm.
 * User: antoniomolina
 * Date: 29/09/2017
 * Time: 21:33
 */
require_once( dirname(__FILE__) . '/vendor/autoload.php' );

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

//Configure Dropbox Application
$app = new DropboxApp("yhzed7on5o6ucsh", "qbm813nnam2fun1", "uHzy5bBkQpAAAAAAAAAA-V_wbKqtD5TEQfS3ET-glDWcIU-5XTLK4sm4v29Tl3O5");

//Configure Dropbox service
$dropbox = new Dropbox($app);

$dropboxFile = new DropboxFile("//home/lifefram/public_html/wp-content/uploads/dropbox_errors/MassimilianoFiumefreddo4.JPG");
$file = $dropbox->upload($dropboxFile, "/test-plugin/MassimilianoFiumefreddo4.JPG", ['autorename' => true]);

//File Name
print $file->getName();