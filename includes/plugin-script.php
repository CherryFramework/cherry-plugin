<?php
//includ js files
	if(!function_exists('cherry_include_stylesheet')){
		function cherry_include_stylesheet(){
			wp_enqueue_style('stylesheet', CHERRY_PLUGIN_URL.'includes/css/cherry-plugin.css', false, '1.0', 'all');
		}
		add_action( 'wp_enqueue_scripts', 'cherry_include_stylesheet', 20);
	}

//includ css files
	if(!function_exists('cherry_include_script')){
		function cherry_include_script(){
			wp_enqueue_script('script', CHERRY_PLUGIN_URL.'includes/js/cherry-plugin.js', array('jquery'), '1.0', true);
		}
		add_action( 'wp_enqueue_scripts', 'cherry_include_script' );
	}