<?php
	/**
	 * Check server settings
	 */
	// correct settings for server
	$must_settings = array(
		'safe_mode'           => 'off',
		'file_uploads'        => 'on',
		'memory_limit'        => 128,
		'post_max_size'       => 8,
		'upload_max_filesize' => 8,
		'max_input_time'      => 45,
		'max_execution_time'  => 30
	);

	// curret server settings
	$current_settings = array();

	//result array
	$result = array();

	if ( ini_get('safe_mode') ) $current_settings['safe_mode'] = 'on';
		else $current_settings['safe_mode'] = 'off';
	if ( ini_get('file_uploads') ) $current_settings['file_uploads'] = 'on';
		else $current_settings['file_uploads'] = 'off';
	$current_settings['memory_limit'] = (int)ini_get('memory_limit');
	$current_settings['post_max_size'] = (int)ini_get('post_max_size');
	$current_settings['upload_max_filesize'] = (int)ini_get('upload_max_filesize');
	$current_settings['max_input_time'] = (int)ini_get('max_input_time');
	$current_settings['max_execution_time'] = (int)ini_get('max_execution_time');

	$diff = array_diff_assoc($must_settings, $current_settings);

	if ( strcmp($must_settings["safe_mode"], $current_settings["safe_mode"]) )
		$result["safe_mode"] = $must_settings["safe_mode"];
	if ( strcmp($must_settings["file_uploads"], $current_settings["file_uploads"]) )
		$result["file_uploads"] = $must_settings["file_uploads"];

	foreach ($diff as $key => $value) {
		if ( $current_settings[$key] < $value ) {
			$result[$key] = $value;
		}
	}
	if($browser_not_supported){
		do_action( 'browser_not_supported_import' );
		$notice = '<h3 class="hndle"><span>'.__( 'Your browser is out of date!' ).'</span></h3>';

		if ( $response['insecure'] ) {
			$msg = sprintf( __( "It looks like you're using an insecure version of <a href='%s'>%s</a>. Using an outdated browser makes your computer unsafe. For the best WordPress experience, please update your browser." ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ) );
		} else {
			$msg = sprintf( __( "It looks like you're using an old version of <a href='%s'>%s</a>. For the best WordPress experience, please update your browser." ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ) );
		}

		$browser_nag_class = '';
		if ( !empty( $response['img_src'] ) ) {
			$img_src = ( is_ssl() && ! empty( $response['img_src_ssl'] ) )? $response['img_src_ssl'] : $response['img_src'];

			$notice .= '<div class="alignright browser-icon"><a href="' . esc_attr($response['update_url']) . '"><img src="' . esc_attr( $img_src ) . '" alt="" /></a></div>';
			$browser_nag_class = ' has-browser-icon';
		}
		$notice .= "<p class='browser-update-nag{$browser_nag_class}'>{$msg}</p>";

		$browsehappy = 'http://browsehappy.com/';
		$locale = get_locale();
		if ( 'en_US' !== $locale )
			$browsehappy = add_query_arg( 'locale', $locale, $browsehappy );

		$notice .= '<p>' . sprintf( __( '<a href="%1$s" class="update-browser-link">Update %2$s</a> or learn how to <a href="%3$s" class="browse-happy-link">browse happy</a>' ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ), esc_url( $browsehappy ) ) . '</p>';
		$notice .= '<div class="clear"></div>';

		echo $notice;
	}else if (!empty($result) && !isset($_GET['import'])) {
		do_action( 'cherry_plugin_fail_server_settings' );
		echo '<h3>Caution!</h3>';
		echo "<h4 class='title'>" . __('Some of your server settings do not meet the requirements for installing the sample data. Please, consult with your hosting provider on how to increase the required values.', CHERRY_PLUGIN_DOMAIN) . "</h4>";
		echo "<table width='100%' border='0' cellspacing='0' cellpadding='4' style='margin-bottom: 15px;' class='wp-list-table widefat fixed'>";
		echo "<thead><tr>";
		echo "<th>" . __('Server Settings', CHERRY_PLUGIN_DOMAIN) . "</th>";
		echo "<th>" . __('Current', CHERRY_PLUGIN_DOMAIN) . "</th>";
		echo "<th>" . __('Required', CHERRY_PLUGIN_DOMAIN) . "</th>";
		echo "</tr></thead>";
		echo "<tbody>";
		$count = 0;
		foreach ($result as $key => $value) {
			$tr_class= $count%2 == 0 ? "class='alternate'" : "" ;
			$units = '';
			if ( $key=='memory_limit' || $key=='post_max_size' || $key=='upload_max_filesize' ) {
				$units = ' (Mb)';
			}
			if ( $key=='max_input_time' || $key=='max_execution_time' ) {
				$units = ' (s)';
			}
			echo "<tr ".$tr_class." >";
			echo "<td style='padding: 15px 10px;'>" . $key . $units . "</td>";
			echo "<td style='padding: 15px 10px;'>" . $current_settings[$key] . "</td>";
			echo "<td style='padding: 15px 10px;'>" . $must_settings[$key] . "</td>";
			$count++;
			if ( $count == 3 ) {
				echo "</tr>";
			}
		}
		echo "</tbody>";
		echo "</table>";
		echo '<a class="button button-primary" href="admin.php?page=import-page&amp;import=true">'.__('Continue', CHERRY_PLUGIN_DOMAIN).'</a>';
		$_SESSION['import']=true;
	}else{
		include_once (CHERRY_PLUGIN_DIR.'admin/import-export/import.php');
	}
	do_action( 'cherry_plugin_server_settings' );
	do_action( 'check_shop_activation' );
?>