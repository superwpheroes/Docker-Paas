<?php

defined('ABSPATH') or die('No script kiddies please!');

const MAXIMUM_FILE_SIZE_2MB = 2097152;

function lf_validate_file($file_index)
{
    try {
        hasUploadErrors($file_index);
        hasValidSize($file_index);
        isValidMimetype($file_index);
        return true;

    } catch (RuntimeException $e) {

        echo $e->getMessage();

    }
}

function initialize_session()
{
    if (!session_id()) {
        session_start();
    }
}

/**
 * @param $file_index
 */
function hasUploadErrors($file_index)
{
// Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES[$file_index]['error']) ||
        is_array($_FILES[$file_index]['error'])
    ) {
        return true;
    }

    // Check $_FILES[$file_index]['error'] value.
    switch ($_FILES[$file_index]['error']) {
        case UPLOAD_ERR_OK:
            break;
//        case UPLOAD_ERR_NO_FILE:
//            throw new RuntimeException('No file sent.');
//        case UPLOAD_ERR_INI_SIZE:
//        case UPLOAD_ERR_FORM_SIZE:
//            throw new RuntimeException('Exceeded filesize limit.');
        default:
            return true;
    }

    return false;
}

/**
 * @param $file_index
 */
function hasValidSize($file_index)
{
    if ($_FILES[$file_index]['size'] > MAXIMUM_FILE_SIZE_2MB) {
        return false;
    }
    return true;
}

/**
 * @param $file_index
 */
function isValidMimetype($file_index)
{
// DO NOT TRUST $_FILES[$file_index]['mime'] VALUE !!
    // Check MIME Type by yourself.
//    $finfo = new finfo(FILEINFO_MIME_TYPE);
//    if (false === $ext = array_search(
//            $finfo->file($_FILES[$file_index]['tmp_name']),
//            array(
//                'jpg' => 'image/jpeg',
//                'png' => 'image/png',
//                'gif' => 'image/gif',
//            ),
//            true
//        )
//    ) {
//        return false;
//    }
    return true;
}
