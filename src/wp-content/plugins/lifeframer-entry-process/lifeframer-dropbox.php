<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * @param $dropbox
 * @param $folderName
 */
function createFolderIfNotExist($dropbox, $folderName)
{
    try {
        $dropbox->CreateFolder($folderName);
    } catch (DropboxExceptionLF $e) {
        // just ignore
    }
}

// ================================================================================
// store_token, load_token, delete_token are SAMPLE functions! please replace with your own!
function store_token($token, $name)
{
    file_put_contents(plugin_dir_path(__FILE__) . "tokens/$name.token", serialize($token));
}

function load_token($name)
{
    if (!file_exists(plugin_dir_path(__FILE__) . "tokens/$name.token")) return null;
    return @unserialize(@file_get_contents(plugin_dir_path(__FILE__) . "tokens/$name.token"));
}

function delete_token($name)
{
    @unlink(plugin_dir_path(__FILE__) . "tokens/$name.token");
}

// ================================================================================

add_action('admin_action_handle_dropbox_auth', 'handle_dropbox_auth');

function handle_dropbox_auth()
{
    global $dropbox;
    // first try to load existing access token
    $access_token = load_token("access");
    if (!empty($access_token)) {
        $dropbox->SetAccessToken($access_token);
    } elseif (!empty($_GET['auth_callback'])) // are we coming from dropbox's auth page?
    {
        // then load our previosly created request token
        $request_token = load_token($_GET['oauth_token']);
        if (empty($request_token)) die('Request token not found!');

        // get & store access token, the request token is not needed anymore
        $access_token = $dropbox->GetAccessToken($request_token);
        store_token($access_token, "access");
        delete_token($_GET['oauth_token']);
        wp_safe_redirect("options-general.php?page=lifeframer_options_plugin");
    }

    // checks if access token is required
    if (!$dropbox->IsAuthorized()) {
        // redirect user to dropbox auth page
        die("Ups! We currently have a problem in our systems. Please, send an email to info@life-framer.com and refer to error 23491198.");
    }
}