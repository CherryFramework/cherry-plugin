<?php
	include_once (ABSPATH . '/wp-admin/includes/class-pclzip.php');
	require_once(ABSPATH . '/wp-admin/includes/dashboard.php');
	require_once(ABSPATH . '/wp-admin/includes/export.php');

	add_action('wp_ajax_export_content', 'cherry_plugin_export_content');
	function cherry_plugin_export_content() {
		$exclude_files = array('xml', 'json');
		/**
		 * Filters folders to exclude from export parser
		 * @var array
		 */
		$exclude_folder = apply_filters( 'cherry_export_exclude_folders', array( 'woocommerce_uploads', 'wc-logs' ) );
		$response = array(
			'what'=>'status',
			'action'=>'export_content',
			'id'=>'1',
			'data'=>__('Export content done', CHERRY_PLUGIN_DOMAIN),
		);
		$response_file = array(
			'what'=>'file',
			'action'=>'export_content',
			'id'=>'2'
		);

		$zip_name = UPLOAD_BASE_DIR.'/sample_data.zip';
		cherry_plugin_delete_file($zip_name);

		if(is_dir(UPLOAD_BASE_DIR)){
			$file_string = cherry_plugin_scan_dir(UPLOAD_BASE_DIR, $exclude_folder, $exclude_files);
		}

		$zip = new PclZip($zip_name);
		$result = $zip->create($file_string, PCLZIP_OPT_REMOVE_ALL_PATH);

		//export json
		$json_file = cherry_plugin_export_json();
		if(is_wp_error($json_file)){
			$response['data'] = "Error : ".$json_file->get_error_message();
		}else{
			$zip->add($json_file, PCLZIP_OPT_REMOVE_ALL_PATH);
			cherry_plugin_delete_file($json_file);
		}

		//export xml
		$xml_file = cherry_plugin_export_xml();
		if(is_wp_error($xml_file)){
			$response['data'] = "Error : ".$xml_file->get_error_message();
		}else{
			$zip->add($xml_file, PCLZIP_OPT_REMOVE_ALL_PATH);
			cherry_plugin_delete_file($xml_file);
		}

		$nonce = wp_create_nonce( 'cherry_plugin_download_content' );

		$file_url = add_query_arg( array( 'action' => 'cherry_plugin_get_export_file', 'file' => $zip_name, '_wpnonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

		if ($result == 0) {
			$response['data'] = "Error : ".$zip->errorInfo(true);
		}else{
			$response_file['data'] = $file_url;
		}

		$xmlResponse = new WP_Ajax_Response($response);
		$xmlResponse->add($response_file);
		$xmlResponse->send();
		exit();
	}
	function cherry_plugin_export_xml(){
		ob_start();
		export_wp();
		$xml = ob_get_clean();
		$xml = iconv('utf-8', 'utf-8//IGNORE', $xml);
		$xml = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $xml);

		$xml_dir = UPLOAD_BASE_DIR.'/sample_data.xml';
		file_put_contents($xml_dir, $xml);

		return $xml_dir;
	}
	function cherry_plugin_export_json() {
		$themename = 'cherry';
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		$sidebar_export = array_filter($sidebars_widgets, 'sort_widget_array');

		$widgets = array( );
		foreach ($sidebar_export as $sidebar_widgets => $sidebar_widget) {
			foreach ($sidebar_widget as $k) {
				$widgets[] = array(
					'type'=>trim( substr( $k, 0, strrpos( $k, '-' ) ) ),
					'type-index'=>trim( substr( $k, strrpos( $k, '-' ) + 1 ) )
				);
			}
		}

		$widgets_array = array( );
		foreach ( $widgets as $widget ) {
			$widget_val = get_option( 'widget_' . $widget['type'] );
			$multiwidget_val = $widget_val['_multiwidget'];
			$widgets_array[$widget['type']][$widget['type-index']] = $widget_val[$widget['type-index']];
			if ( isset( $widgets_array[$widget['type']]['_multiwidget'] ) ) {
				unset( $widgets_array[$widget['type']]['_multiwidget'] );
			}
			$widgets_array[$widget['type']]['_multiwidget'] = $multiwidget_val;
			unset($widgets_array[$widget['type']][$widget['type-index']][$themename.'_widget_rules_type_'.$widget['type'].'-'.$widget['type-index']]);
			unset($widgets_array[$widget['type']][$widget['type-index']][$themename.'_widget_rules_'.$widget['type'].'-'.$widget['type-index']]);
			unset($widgets_array[$widget['type']][$widget['type-index']][$themename.'_widget_custom_class_'.$widget['type'].'-'.$widget['type-index']]);
			unset($widgets_array[$widget['type']][$widget['type-index']][$themename.'_widget_responsive_'.$widget['type'].'-'.$widget['type-index']]);
			unset($widgets_array[$widget['type']][$widget['type-index']][$themename.'_widget_users_'.$widget['type'].'-'.$widget['type-index']]);

			if ( isset($widgets_array[$widget['type']][$widget['type-index']]['nav_menu']) ) {
				$term = get_term_by( 'id', $widgets_array[$widget['type']][$widget['type-index']]['nav_menu'], 'nav_menu' );
				if ($term) {
					$widgets_array[$widget['type']][$widget['type-index']]['nav_menu_slug'] = $term->slug;
				}
			}
		}
		unset( $widgets_array['export'] );
		unset( $widgets_array['Widget-Settings'] );
		unset( $widgets_array[''] );

		$options_type = get_option($themename . '_widget_rules_type');
		$options      = get_option($themename . '_widget_rules');
		$custom_class = get_option($themename . '_widget_custom_class');
		$responsive   = get_option($themename . '_widget_responsive');
		$users        = get_option($themename  . '_widget_users');

		if ( !empty($options_type) && is_array($options_type) ) {
			$rules_array['widget_rules_type'] = array($options_type);
		}
		if ( !empty($options) && is_array($options) ) {
			$rules_array['widget_rules'] = array($options);
		}
		if ( !empty($custom_class) && is_array($custom_class) ) {
			$rules_array['widget_custom_class'] = array($custom_class);
		}
		if ( !empty($responsive) && is_array($responsive) ) {
			$rules_array['widget_responsive'] = array($responsive);
		}
		if ( !empty($users) && is_array($users) ) {
			$rules_array['widget_users'] = array($users);
		}
		if ( !isset($rules_array)) $rules_array = array();

		$export_array = array( $sidebar_export, $widgets_array, $rules_array );
		$json = json_encode( $export_array );
		$json = iconv('utf-8', 'utf-8//IGNORE', $json);
		$json = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $json);
		$json_dir = UPLOAD_BASE_DIR.'/widgets.json';
		file_put_contents($json_dir, $json);

		return $json_dir;
	}
	function sort_widget_array($array){
		return (!empty($array) && is_array($array));
	}
	function cherry_plugin_delete_file($file){
		if(is_readable($file)){
			unlink($file);
			return 'file deleted';
		}else{
			return 'file is missing';
		};
	}
	function cherry_plugin_scan_dir($dir, $exceptions_folder, $exceptions_files){
		$exceptions_folder =  array_merge(array('.', '..'), $exceptions_folder);
		$scand_dir = array_diff(scandir($dir), $exceptions_folder);
		$scan_dir_string = array();
		$extensionend_file = "";

		foreach ($scand_dir as $file) {
			$scan_file = $dir.'/'.$file;
			$file_extension = explode(".", $scan_file);
			$extensionend_file = end($file_extension);
			if(is_dir($scan_file)){
				$scan_file= cherry_plugin_scan_dir($scan_file, $exceptions_folder, $exceptions_files);
			}else if(in_array($extensionend_file, $exceptions_files)){
				$scan_file="";
			}
			array_push($scan_dir_string, $scan_file);
		}
		return implode(',', $scan_dir_string);
	}
?>