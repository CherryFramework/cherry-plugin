<?php
//Main plugin page
	if( !function_exists('cherry_plugin_main_page') ){
		function cherry_plugin_main_page(){
			$cherry_plugin_components = new cherry_plugin_components;
			$cherry_plugin_components -> get_header(array('title' => __('Cherry Plugin', CHERRY_PLUGIN_DOMAIN), 'wrapper_class' => 'cherry_plugin_main_page'));

			add_thickbox();
			?>

			<p><?php _e( 'Since version 3.0.1 of CherryFramework shortcodes, widgets and import/export options have been moved to CherryPlugin. It is installed automatically on CherryFramework installation and is required for the framework correct work. ', CHERRY_PLUGIN_DOMAIN); ?></p><br>
				<div class="cols cols-2">
					<div class="col">
					<div class="plugin-option">
						<a href="#TB_inline?width=600&height=510&inlineId=help_import" class="demo-vid thickbox" title="<?php _e('Files Import demo', CHERRY_PLUGIN_DOMAIN); ?>"><i class="icon-facetime-video"></i></a>
						<div class="thumb-icon">
							<i class="icon-download-alt"></i>
						</div>
						<h4><a href="?page=import-page"><?php _e('Import', CHERRY_PLUGIN_DOMAIN); ?></a></h4>
						<p><small><?php _e('Option to import website content from the backup. Select all files from the backup archive and drag them to the upload files area to start uploading.', CHERRY_PLUGIN_DOMAIN); ?></small></p>
						</div>
					</div>

					<div class="col">
						<div class="plugin-option">
							<a href="#TB_inline?width=600&height=510&inlineId=help_export" class="demo-vid thickbox" title="<?php _e('Files Export demo', CHERRY_PLUGIN_DOMAIN); ?>"><i class="icon-facetime-video"></i></a>
							<div class="thumb-icon">
								<i class="icon-upload-alt"></i>
							</div>
							<h4><a href="?page=export-page"><?php _e('Export', CHERRY_PLUGIN_DOMAIN); ?></a></h4>
							<p><small><?php _e('Option to backup your website data creating a downloadable archive. Use this option to keep your website data on performing some serious modifications or moving website to other hosting.', CHERRY_PLUGIN_DOMAIN); ?></small></p>
						</div>
				</div>
			</div>
			<?php
			echo cherry_plugin_help_import_popup();
			echo cherry_plugin_help_export_popup();
			$cherry_plugin_components -> get_footer();
		}
	}
//import settings page
	if( !function_exists('cherry_plugin_import_page') ){
		function cherry_plugin_import_page(){
			?>
			<script>
				if (typeof(window.FileReader) == 'undefined' && window.location.search.indexOf('not_supported=true')==-1) { 
					window.location.search = '?page=import-page&not_supported=true'; 
				}
			</script>
			<?php
			$response = wp_check_browser_version();
			$browser_not_supported = isset($_GET['not_supported']) 
									|| $response['name'] == 'Internet Explorer' && $response['version'] <= 9
									|| $response['name'] == 'Safari' && $response['version'] <= 6 ? true : false ;
			$holder_id = $browser_not_supported ? 'browser_nag' : '' ;
			$cherry_plugin_components = new cherry_plugin_components;
			$cherry_plugin_components -> get_header(array('title' => __('Cherry Content Import', CHERRY_PLUGIN_DOMAIN), 'wrapper_class' => 'impotr_export_wrapper', 'wrapper_id' => $holder_id));

			include_once (CHERRY_PLUGIN_DIR.'admin/import-export/import-check-settings.php');

			$cherry_plugin_components -> get_footer();
		}
	}
//export settings page
	if( !function_exists('cherry_plugin_export_page') ){
		function cherry_plugin_export_page(){
			$cherry_plugin_components = new cherry_plugin_components;
			$cherry_plugin_components -> get_header(array('title' => __('Cherry Content Export', CHERRY_PLUGIN_DOMAIN), 'wrapper_class' => 'impotr_export_wrapper'));

			include_once (CHERRY_PLUGIN_DIR.'admin/import-export/export.php');
			$cherry_plugin_components -> get_footer();
		}
	}