<?php
	include_once (CHERRY_PLUGIN_DIR . 'admin/plugin-admin-script.php');
	include_once (CHERRY_PLUGIN_DIR . 'admin/plugin-components.php');
	include_once (CHERRY_PLUGIN_DIR . 'admin/plugin-pages.php');
	include_once (CHERRY_PLUGIN_DIR . 'admin/plugin-function.php');

	//xml parser class
	include_once (CHERRY_PLUGIN_DIR . 'lib/php/parsers.php');

	//import ajax function
	include_once (CHERRY_PLUGIN_DIR . 'admin/import-export/import-functions.php');
	include_once (CHERRY_PLUGIN_DIR . 'admin/import-export/export-functions.php');

	//Shortcodes tinyMCE includes
	include_once (CHERRY_PLUGIN_DIR . 'admin/shortcodes/tinymce-shortcodes.php');

	//Plugin updater
	include_once (CHERRY_PLUGIN_DIR . 'admin/plugin-updater.php');

//added menu item
	if(!function_exists('cherry_plugin_menu')){
		function cherry_plugin_menu() {
			global $cherry_plugin_menu, $submenu, $main_page_link;
			$cherry_plugin_menu = 'cherry-plugin-page';
			$capability = 'activate_plugins';
			$main_page_link = 'plugin-main-page';

			$plugin_menu_title = __('Cherry plugin', CHERRY_PLUGIN_DOMAIN);
			add_menu_page($plugin_menu_title, $plugin_menu_title, $capability, $cherry_plugin_menu, 'cherry_plugin_main_page', 'none', 62);

			$main_page_menu_title = __('Summary', CHERRY_PLUGIN_DOMAIN);
			add_submenu_page($cherry_plugin_menu, $main_page_menu_title, $main_page_menu_title, $capability, 'plugin-main-page', 'cherry_plugin_main_page');

			$under_construction_menu_title = __('Maintenance Mode', CHERRY_PLUGIN_DOMAIN);
			add_submenu_page($cherry_plugin_menu, $under_construction_menu_title, $under_construction_menu_title, $capability, 'maintenance-mode-page', 'cherry_maintenance_mode_admin_page');

			$import_menu_title = __('Import Content', CHERRY_PLUGIN_DOMAIN);
			add_submenu_page($cherry_plugin_menu, $import_menu_title, $import_menu_title, $capability, 'import-page', 'cherry_plugin_import_page');

			$export_menu_title = __('Export Content', CHERRY_PLUGIN_DOMAIN);
			add_submenu_page($cherry_plugin_menu, $export_menu_title, $export_menu_title, $capability, 'export-page', 'cherry_plugin_export_page');

			unset($submenu[$cherry_plugin_menu][0]);
		}
		add_action('admin_menu', 'cherry_plugin_menu');
	}
/* settings link in plugin management screen */
	if(!function_exists('cherry_plugin_settings_link')){
		function cherry_plugin_settings_link($actions, $file) {
			global $cherry_plugin_menu, $main_page_link;
			if(false !== strpos($file, strtolower('cherry-plugin')))
				$actions['summary'] = '<a href="admin.php?page='.$main_page_link.'">'.__('Summary', CHERRY_PLUGIN_DOMAIN).'</a>';
			return $actions;
		}
		add_filter('plugin_action_links', 'cherry_plugin_settings_link', 2, 2);
	}
	if(!function_exists('cherry_plugin_update')){
		function cherry_plugin_update() {
			
			//var_dump($plugin_update -> plugins_api());
		}
		add_action('init', 'cherry_plugin_update');
	}