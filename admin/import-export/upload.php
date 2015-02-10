<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die('Error');
}

/**
 * Process file uploads for importer
 */
function cherry_plugin_process_upload() {

	// verify nonce
	check_ajax_referer( 'cherry_plugin_upload', '_wpnonce' );

	// check user caps
	if ( !current_user_can( 'import' ) ) {
		wp_die( 'You don\'t have permissions to do this', 'Error' );
	}

	if ( strtolower( $_SERVER['REQUEST_METHOD'] ) != 'post' ) {
		wp_die( 'Wrong method', 'Error' );
	}

	if ( array_key_exists( 'file', $_FILES ) ) {
		$upload_dir  = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : $upload_dir ;
		$file_name   = basename($_FILES['file']['name']);
		$upload_file = $upload_dir . $file_name;
		$result      = move_uploaded_file($_FILES['file']['tmp_name'], $upload_file);
	}
	die();
}

add_action( 'wp_ajax_cherry_import_files', 'cherry_plugin_process_upload' );