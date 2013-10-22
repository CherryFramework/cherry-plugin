<?php 
	if(!function_exists('cherry_admin_assets')){
		function cherry_assets(){
			wp_register_style('cherry_plugin_stylesheet', CHERRY_PLUGIN_URL.'assets/css/cherry_plugin.css', false, '0.1', 'all');
			wp_enqueue_style('cherry_plugin_stylesheet');

			wp_register_script('cherry_plugin_script', CHERRY_PLUGIN_URL.'assets/js/cherry_plugin.js', array('jquery'), '0.1', true);
			wp_enqueue_script('cherry_plugin_script');
		}
		add_action( 'wp_enqueue_scripts', 'cherry_assets' );
	}