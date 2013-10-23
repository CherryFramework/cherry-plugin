<p><?php _e('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo
lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui. Quisque nec
mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam. Pellentesque rhoncus aliquam
mattis. Ut vulputate eros sed felis sodales nec vulputate justo hendrerit. Vivamus varius pretium ligula, a aliquam odio euismod sit amet. Quisque
laoreet sem sit amet orci ullamcorper at ultricies metus viverra. Pellentesque arcu mauris, malesuada quis ornare accumsan, blandit sed diam.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo
lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui. Quisque nec
mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam. Pellentesque rhoncus aliquam
mattis. Ut vulputate eros sed felis sodales nec vulputate justo hendrerit. Vivamus varius pretium ligula, a aliquam odio euismod sit amet. Quisque
laoreet sem sit amet orci ullamcorper at ultricies metus viverra. Pellentesque arcu mauris, malesuada quis ornare accumsan, blandit sed diam.', CHERRY_PLUGIN_DOMIN); ?></p>
<?php
	/**
	 * Check server settings
	 */
	// correct settings for server
	$must_settings = array(
		'safe_mode'           => 'on',
		'file_uploads'        => 'off',
		'memory_limit'        => 200000,
		'post_max_size'       => 500,
		'upload_max_filesize' => 400,
		'max_input_time'      => 60000,
		'max_execution_time'  => 30000
	);

	/*$must_settings = array(
		'safe_mode'           => 'off',
		'file_uploads'        => 'on',
		'memory_limit'        => 128,
		'post_max_size'       => 8,
		'upload_max_filesize' => 8,
		'max_input_time'      => 60,
		'max_execution_time'  => 30
	);*/

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
	if ( !empty($result) ) {
		echo '<h3>Server Settings</h3><hr>';
		echo "<h4 class='title'>" . __('Some of your server settings do not meet the requirements for installing the sample data. Please, consult with your hosting provider on how to increase the required values.', CHERRY_PLUGIN_DOMIN) . "</h4>";
		echo "<table width='100%' border='0' cellspacing='0' cellpadding='4' style='border-radius:3px; border-collapse: collapse; margin-bottom:10px;'>";
		echo "<thead><tr border='0' align='center' bgcolor='#87c1ee' style='color:#fff;'>";
		echo "<th style='border:1px solid #87c1ee;'>" . __('Server Settings', CHERRY_PLUGIN_DOMIN) . "</th>";
		echo "<th style='border:1px solid #87c1ee;'>" . __('Current', CHERRY_PLUGIN_DOMIN) . "</th>";
		echo "<th style='border:1px solid #87c1ee;'>" . __('Required', CHERRY_PLUGIN_DOMIN) . "</th>";
		echo "</tr></thead>";
		echo "<tbody>";
		$count = 0;
		foreach ($result as $key => $value) {
			$units = '';
			if ( $key=='memory_limit' || $key=='post_max_size' || $key=='upload_max_filesize' ) {
				$units = ' (Mb)';
			}
			if ( $key=='max_input_time' || $key=='max_execution_time' ) {
				$units = ' (s)';
			}
			echo "<tr>";
			echo "<td style='border:1px solid #9BCDF1;'>" . $key . $units . "</td>";
			echo "<td align='center' style='color:#BD362F; border:1px solid #9BCDF1;'>" . $current_settings[$key] . "</td>";
			echo "<td align='center' style='border:1px solid #9BCDF1;'>" . $must_settings[$key] . "</td>";
			$count++;
			if ( $count == 3 ) {
				echo "</tr>";
			}
		}
		echo "</tbody>";
		echo "</table>";
		//echo "<div class='note'><p><strong>" . __('NOTE', CHERRY_PLUGIN_DOMIN) . ": </strong>" . __('if for some reason those settings can not be adjusted, you may install the sample data using an <strong>alternative method</strong> - importing the <strong>.sql</strong> file directly into the database. Refer to the template documentation for instructions.', CHERRY_PLUGIN_DOMIN) . "</p>" . __('You can proceed with the template installation without updating server settings, however in this case you can get errors or only part of your content will be loaded.', CHERRY_PLUGIN_DOMIN) . "</div>";
		//echo "<p class='text-style'>" . theme_locals('template_installation') . "</p>";

		$href = '#';
		$class = 'not_active';
	}else{
		$href = 'admin.php?page=import-page&amp;step=1';
		$class = '';
	}

	echo '<a class="button button-primary '.$class.' buttin-left" href="'.$href.'">'.__('Next', CHERRY_PLUGIN_DOMIN).'</a>';
	do_action('check_shop_activation');
?>