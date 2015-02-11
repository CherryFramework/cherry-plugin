<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die('Error');
}

/**
 * Process export file download
 */
function cherry_plugin_get_export_file() {
    
    check_ajax_referer( 'cherry_plugin_download_content', '_wpnonce' );

    if ( ! current_user_can( 'export' ) ) {
        wp_die( 'You do not have permissions to do this', 'Error' );
    }

    $file = isset($_GET["file"]) ? $_GET["file"] : '';

    if ( ! $file ) {
        wp_die( 'File not provided', 'Error' );
    }

    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'];

    if ( false === strpos( $file, $upload_dir ) && false === strpos( $file, str_replace( '\\', '/', $upload_dir ) ) ) {
        wp_die( 'Not allowed file path', 'Error' );
    }

    if ( file_exists( $file ) ) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    } else {
        echo 'error';
    };
}

add_action( 'wp_ajax_cherry_plugin_get_export_file', 'cherry_plugin_get_export_file' );