<?php
/*
Plugin Name: Cherry Plugin
Plugin URI: http://www.cherryframework.com/
Description: About cherry plugin.
Version: 0.1
Author: Cherry Team
Author URI: http://www.cherryframework.com/
*/
//init plugin
	if(!function_exists('cherry_plugin_init')){
		function cherry_plugin_init(){
			global $wpdb;

			@define('CHERRY_PLUGIN_DB', $wpdb->prefix.cherry_plugin);
			@define('CHERRY_PLUGIN_DIR', plugin_dir_path(__FILE__));
			@define('CHERRY_PLUGIN_URL', plugin_dir_url(__FILE__));
			@define('CHERRY_PLUGIN_DOMIN', 'CHERRY_PLUGIN_DOMIN');
			@define('CHERRY_PLUGIN_VERSION', '0.1');
			@define('CHERRY_PLUGIN_NAME', 'Cherry Plugin');

			load_plugin_textdomain(CHERRY_PLUGIN_DOMIN, false,CHERRY_PLUGIN_DIR.'languages');

			if(is_admin()){
				include_once (CHERRY_PLUGIN_DIR . 'admin/admin.php');
			}else{
				include_once (CHERRY_PLUGIN_DIR . 'includes/init.php');
			}
		}
		add_action('init', 'cherry_plugin_init');
	};
//activate plugin
	if(!function_exists('cherry_plugin_activate')){
		function cherry_plugin_activate(){
			//r_dump(CHERRY_PLUGIN_DB);
		}
		register_activation_hook( __FILE__, 'cherry_plugin_activate' );
	};
//deactivate plugin
	if(!function_exists('cherry_plugin_deactivate')){
		function cherry_plugin_deactivate(){
			//echo "cherry_plugin_deactivate";
		}
		register_deactivation_hook( __FILE__, 'cherry_plugin_deactivate' );
	};
//delete plugin
	if(!function_exists('cherry_plugin_uninstall')){
		function cherry_plugin_uninstall(){
			//echo "cherry_plugin_uninstall";
		}
		register_uninstall_hook(__FILE__, 'cherry_plugin_uninstall');
	};