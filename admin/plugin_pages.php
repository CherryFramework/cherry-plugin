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
			echo '<div class="impotr_export_wrapper">';

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
			$page_include = CHERRY_PLUGIN_DIR.'admin/import-export/';
			switch ($step) {
				case 1:
				//import page step 1;
					$page_include .= 'import-step-1.php';
				break;
				case 2:
				//import page step 2;
					$page_include .= 'import-step-2.php';
				break;
				case 3:
				//finish import page;
					$page_include .= 'import-finish.php';
				break;
				default:
				//main import page;
					$page_include .= 'import.php';
				break;
			}
			include_once ($page_include);
			
			echo '<div class="clear"></div></div>';
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
				// export page step 1;
					$page_include .= 'export-step-1.php';
				break;
				case 2:
				// export page step 2;
					$page_include .= 'export-step-2.php';
				break;
				case 3:
				// finish export page;
					$page_include .= 'export-finish.php';
				break;
				default:
				// main export page;
					$page_include .= 'export.php';
				break;
			}
			include_once ($page_include);
			get_cherry_plugin_footer();
		}
	}