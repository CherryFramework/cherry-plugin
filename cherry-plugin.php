<?php
/*
	Plugin Name: Cherry Plugin
	Version: 1.2.8.1
	Plugin URI: http://www.cherryframework.com/update/meet-the-cherry-plugin-bare-functionalities-no-strings-attached/
	Description: Cherry team has already created a Cherry framework that can be reasonably called perfect, but we are always looking for more improvements. Meet the Cherry Plugin. This is an extension for our Cherry framework where we've included all shortcodes and widgets you will ever need. The plugin is fully compatible with any WordPress theme powered by Cherry Framework. So far the plugin is a beta release, but we're going to keep on improving it, to deliver even more cool features.
	Author: Cherry Team.
	Author URI: http://www.cherryframework.com/
	Text Domain: cherry-plugin
	Domain Path: languages/
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
//plugin settings
	if(!function_exists('cherry_plugin_settings')){
		function cherry_plugin_settings(){
			global $wpdb;

			if ( !function_exists( 'get_plugin_data' ) ) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$upload_dir = wp_upload_dir();
			$plugin_data = get_plugin_data(plugin_dir_path(__FILE__).'cherry-plugin.php');

			//Cherry plugin constant variables
			define('CHERRY_PLUGIN_DIR', plugin_dir_path(__FILE__));
			define('CHERRY_PLUGIN_URL', plugin_dir_url(__FILE__));
			define('CHERRY_PLUGIN_DOMAIN', $plugin_data['TextDomain']);
			define('CHERRY_PLUGIN_DOMAIN_DIR', $plugin_data['DomainPath']);
			define('CHERRY_PLUGIN_VERSION', $plugin_data['Version']);
			define('CHERRY_PLUGIN_NAME', $plugin_data['Name']);
			define('CHERRY_PLUGIN_SLUG', plugin_basename( __FILE__ ));
			define('CHERRY_PLUGIN_DB', $wpdb->prefix.CHERRY_PLUGIN_DOMAIN);
			define('CHERRY_PLUGIN_REMOTE_SERVER', esc_url( 'http://tmbhtest.com/cherryframework.com/components_update/' ));

			//Other constant variables
			define('CURRENT_THEME_DIR', get_stylesheet_directory());
			define('CURRENT_THEME_URI', get_stylesheet_directory_uri());
			define('UPLOAD_BASE_DIR', str_replace("\\", "/", $upload_dir['basedir']));
			define('UPLOAD_DIR', str_replace("\\", "/", $upload_dir['path'].'/'));

			// if ( !defined('API_URL') ) {
			// 	define( 'API_URL', esc_url( 'http://updates.cherry.template-help.com/cherrymoto/v3/api/' ) );
			// }

			load_plugin_textdomain( CHERRY_PLUGIN_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/'.CHERRY_PLUGIN_DOMAIN_DIR);

			do_action( 'cherry_plugin_settings' );
		}
		add_action('plugins_loaded', 'cherry_plugin_settings', 0);
	};
//init plugin
	if(!function_exists('cherry_plugin_init')){
		function cherry_plugin_init(){

			include_once (CHERRY_PLUGIN_DIR . 'includes/plugin-assets.php');
			if(is_admin()){
				include_once (CHERRY_PLUGIN_DIR . 'admin/admin.php');
			}else{
				include_once (CHERRY_PLUGIN_DIR . 'includes/plugin-includes.php');
			}
			do_action( 'cherry_plugin_init' );
		}
		add_action('init', 'cherry_plugin_init', 0);
	};

//upgrade plugin's version
	if(!function_exists('cherry_plugin_upgrade')){
		function cherry_plugin_upgrade() {
			$opt = get_option( 'cherry_plugin' );

			if ( ! is_array( $opt ) )
				$opt = array();

			$old_ver = isset( $opt['version'] ) ? (string) $opt['version'] : '0';
			$new_ver = CHERRY_PLUGIN_VERSION;

			if ( $old_ver == $new_ver )
				return;

			do_action( 'cherry_plugin_upgrade_ver', $new_ver, $old_ver );

			$opt['version'] = $new_ver;

			update_option( 'cherry_plugin', $opt );
		}
		add_action( 'admin_init', 'cherry_plugin_upgrade' );
	};

//activate plugin
	if(!function_exists('cherry_plugin_activate')){
		function cherry_plugin_activate(){
			do_action( 'cherry_plugin_activate' );
		}
		register_activation_hook( __FILE__, 'cherry_plugin_activate' );
	};

//deactivate plugin
	if(!function_exists('cherry_plugin_deactivate')){
		function cherry_plugin_deactivate(){
			//echo "cherry_plugin_deactivate";
			do_action( 'cherry_plugin_deactivate' );
		}
		register_deactivation_hook( __FILE__, 'cherry_plugin_deactivate' );
	};

//delete plugin
	if(!function_exists('cherry_plugin_uninstall')){
		function cherry_plugin_uninstall(){
			//echo "cherry_plugin_uninstall";
			do_action( 'cherry_plugin_uninstall' );
		}
		register_uninstall_hook(__FILE__, 'cherry_plugin_uninstall');
	};
/**
 * Not use `wptexturize` in content and excerpt.
 * Removed temporary.
 *
 * @since 1.2.5
 * @link  https://core.trac.wordpress.org/ticket/29557
 */
remove_filter( 'the_content', 'wptexturize' );
remove_filter( 'the_excerpt', 'wptexturize' );