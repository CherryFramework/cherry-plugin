<?php
//includ js files
	if(!function_exists('cherry_include_admin_scripts')){
		function cherry_include_admin_scripts(){
			wp_enqueue_script('cherry_plugin_script', CHERRY_PLUGIN_URL.'admin/js/cherry-admin-plugin.js', array('jquery'), '0.1', true);
		}
		add_action( 'admin_enqueue_scripts', 'cherry_include_admin_scripts' );
	}
//includ css files
	if(!function_exists('cherry_include_admin_style')){
		function cherry_include_admin_style(){
			wp_enqueue_style('cherry_plugin_stylesheet', CHERRY_PLUGIN_URL.'admin/css/cherry-admin-plugin.css', false, '0.1', 'all');
			wp_enqueue_style('font-awesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css', false, '3.2.1', 'all');
		}
		add_action( 'admin_enqueue_scripts', 'cherry_include_admin_style' );
	}
