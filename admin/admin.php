<?php
	@include_once (CHERRY_PLUGIN_DIR . 'admin/plugin_components.php');
	@include_once (CHERRY_PLUGIN_DIR . 'admin/plugin_pages.php');
//includ js files
	if(!function_exists('cherry_include_admin_scripts')){
		function cherry_include_admin_scripts(){
			wp_enqueue_script('cherry_plugin_script', CHERRY_PLUGIN_URL.'admin/js/cherry_admin_plugin.js', array('jquery'), '0.1', true);
		}
		add_action( 'admin_enqueue_scripts', 'cherry_include_admin_scripts' );
	}
//includ css files
	if(!function_exists('cherry_include_admin_style')){
		function cherry_include_admin_style(){
			wp_enqueue_style('cherry_plugin_stylesheet', CHERRY_PLUGIN_URL.'admin/css/cherry_admin_plugin.css', false, '0.1', 'all');
			wp_enqueue_style('font-awesome', 'http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css', false, '3.2.1', 'all');
		}
		add_action( 'admin_enqueue_scripts', 'cherry_include_admin_style' );
	}
//added menu item
	if(!function_exists('cherry_plugin_menu')){
		function cherry_plugin_menu() {
			global $cherry_plugin_menu, $submenu;
			$cherry_plugin_menu = 'cherry-plugin-page';
			$capability = 'activate_plugins';

			$plugin_menu_title = __('Cherry plugin', CHERRY_PLUGIN_DOMIN);

			add_menu_page($plugin_menu_title, $plugin_menu_title, $capability, $cherry_plugin_menu, 'cherry_plugin_main_page', '', 62);

			$main_page_menu_title = __('Main Page', CHERRY_PLUGIN_DOMIN);
			add_submenu_page($cherry_plugin_menu, $main_page_menu_title, $main_page_menu_title, $capability, 'plugin-main-page', 'cherry_plugin_main_page');

			/*$shortcode_menu_title = __('Shortcode', CHERRY_PLUGIN_DOMIN);
			add_submenu_page($cherry_plugin_menu, $shortcode_menu_title, $shortcode_menu_title, $capability, 'shortcode-settings', 'cherry_plugin_shortcode_page');

			$widgets_menu_title = __('Widgets', CHERRY_PLUGIN_DOMIN);
			add_submenu_page($cherry_plugin_menu, $widgets_menu_title, $widgets_menu_title, $capability, 'widgets-settings', 'cherry_plugin_widgets_page');
*			*/
			$import_menu_title = __('Import', CHERRY_PLUGIN_DOMIN);
			add_submenu_page($cherry_plugin_menu, $import_menu_title, $import_menu_title, $capability, 'import-page', 'cherry_plugin_import_page');

			$export_menu_title = __('Export', CHERRY_PLUGIN_DOMIN);
			add_submenu_page($cherry_plugin_menu, $export_menu_title, $export_menu_title, $capability, 'export-page', 'cherry_plugin_export_page');

			unset($submenu[$cherry_plugin_menu][0]);
		}
		add_action('admin_menu', 'cherry_plugin_menu');
	}
/* settings link in plugin management screen */
	if(!function_exists('cherry_plugin_settings_link')){
		function cherry_plugin_settings_link($actions, $file) {
			global $cherry_plugin_menu;
			if(false !== strpos($file, strtolower('cherry-plugin')))
				$actions['settings'] = '<a href="admin.php?page='.$cherry_plugin_menu.'">'.__('Settings', CHERRY_PLUGIN_DOMIN).'</a>';
			return $actions;
		}
		add_filter('plugin_action_links', 'cherry_plugin_settings_link', 2, 2);
	}
