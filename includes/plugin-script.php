<?php
//include stylesheet files
	if ( !function_exists('cherry_include_stylesheet') ) {
		function cherry_include_stylesheet() {
			wp_enqueue_style( 'cherry-plugin', CHERRY_PLUGIN_URL . 'includes/css/cherry-plugin.css', array(), CHERRY_PLUGIN_VERSION, 'all' );
		}
		add_action( 'wp_enqueue_scripts', 'cherry_include_stylesheet', 9);
	}

//include script files
	if ( !function_exists('cherry_include_script') ) {
		function cherry_include_script(){
			wp_enqueue_script( 'cherry-plugin', CHERRY_PLUGIN_URL . 'includes/js/cherry-plugin.js', array('jquery'), CHERRY_PLUGIN_VERSION, true );
		}
		add_action( 'wp_enqueue_scripts', 'cherry_include_script' );
	}