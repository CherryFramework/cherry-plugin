<?php
//Main plugin page
	if( !function_exists('cherry_plugin_main_page') ){
		function cherry_plugin_main_page(){
			get_cherry_plugin_header(array('title' => __('Cherry Plugin', CHERRY_PLUGIN_DOMIN), 'icon_class' => 'icon-generic'));

			get_cherry_plugin_footer();
		}
	}
//import settings page
	if( !function_exists('cherry_plugin_import_page') ){
		function cherry_plugin_import_page(){
			get_cherry_plugin_header(array('title' => __('Cherry Import', CHERRY_PLUGIN_DOMIN), 'icon_class' => 'icon-generic'));

			get_cherry_plugin_footer();
		}
	}
//export settings page
	if( !function_exists('cherry_plugin_export_page') ){
		function cherry_plugin_export_page(){
			get_cherry_plugin_header(array('title' => __('Cherry Ecxport', CHERRY_PLUGIN_DOMIN), 'icon_class' => 'icon-generic'));

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
			$page_include = CHERRY_PLUGIN_DIR.'admin/import-export/';

			switch ($step) {
				case 1:
				//export page step 1;
					$page_include .= 'export-step-1.php';
				break;
				case 2:
				//export page step 2;
					$page_include .= 'export-step-2.php';
				break;
				case 3:
				//finish export page;
					$page_include .= 'export-finish.php';
				break;
				default:
				//main export page;
					$page_include .= 'export.php';
				break;
			}
			@include_once ($page_include);
			
			get_cherry_plugin_footer();
		}
	}
//shortcode plugin page
	if( !function_exists('cherry_plugin_shortcode_page') ){
		function cherry_plugin_shortcode_page(){
			get_cherry_plugin_header(array('title' => __('Cherry Shotcode', CHERRY_PLUGIN_DOMIN), 'icon_class' => 'icon-generic'));

			get_cherry_plugin_footer();
		}
	}
//widgets plugin page
	if( !function_exists('cherry_plugin_widgets_page') ){
		function cherry_plugin_widgets_page(){
			get_cherry_plugin_header(array('title' => __('Cherry Widgets', CHERRY_PLUGIN_DOMIN), 'icon_class' => 'icon-generic'));

			

			get_cherry_plugin_footer();
		}
	}