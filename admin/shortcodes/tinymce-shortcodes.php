<?php
/**
 *
 * TinyMCE Shortcode Integration
 *
 */
class TinyMCE_Shortcodes {
	// Constructor
	function TinyMCE_Shortcodes () {
		//admin_init
		add_action( 'admin_init', array( &$this, 'init' ) );
		
		//Only use wp_ajax if user is logged in
		add_action( 'wp_ajax_check_url_action', array( &$this, 'ajax_action_check_url' ) );
	}

	// Get everything started
	function init() {
		if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && get_user_option('rich_editing') == 'true' ) {
			//Pass URL to plugin's js file
			$name = 'BALLS';
			wp_localize_script( 'MyTheme', 'custom', array('name' => $name));
			//TinyMCE plugin stuff
			add_filter( 'mce_buttons', array( &$this, 'filter_mce_buttons' ) );
			add_filter( 'mce_external_plugins', array( &$this, 'filter_mce_external_plugins' ) );

			wp_enqueue_style( 'tinymce-shortcodes', CHERRY_PLUGIN_URL.'admin/css/tinymce-shortcodes.css', false, '0.1', 'all');
		}
	}
	
	// Filter mce buttons
	function filter_mce_buttons( $buttons ) {
		array_push( $buttons, '|', 'shortcodes_button' );

		return $buttons;
	}

	// Actually add tinyMCE plugin attachment
	function filter_mce_external_plugins( $plugins ) {
		$plugins['MyThemeShortcodes'] = CHERRY_PLUGIN_URL.'admin/shortcodes/editor-plugin.php';

		return $plugins;
	}

	// Ajax Check
	function ajax_action_check_url() {
		$hadError = true;
		$url = isset( $_REQUEST['url'] ) ? $_REQUEST['url'] : '';
	
		if ( strlen( $url ) > 0  && function_exists( 'get_headers' ) ) {
				
			$file_headers = @get_headers( $url );
			$exists       = $file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found';
			$hadError     = false;
		}
	
		echo '{ "exists": '. ($exists ? '1' : '0') . ($hadError ? ', "error" : 1 ' : '') . ' }';
		die();
	}
}
// end TinyMCE_Shortcodes class
$mytheme_shortcode_tinymce = new TinyMCE_Shortcodes();
?>