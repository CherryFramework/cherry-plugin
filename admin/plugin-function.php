<?php
//added javascript variables
	function cherry_plugin_add_js_variables(){
		$variables = array(
						'remove_widgets_text = "'.__('Remove Inactive Widgets', CHERRY_PLUGIN_DOMAIN).'"'
					);

		$out = "<script>\nvar " ;
		$out .= implode(', ', $variables);
		$out .= "; \n</script>\n";
		echo $out;
	}
	add_action('admin_footer', 'cherry_plugin_add_js_variables');
	
//help popaps
	//help_import
	function cherry_plugin_help_import_popup(){
		$video_src = cherry_plugin_remote_query(array('data_type' => 'info'));
		$video_src = $video_src['video_help_import'];

		$out =  '<div id="help_import" style="display:none; text-align:center;">';
		$out .= '<div style="text-align:center">';
		$out .= '<p>'.__('CTRL+A (Command-A) to select all files', CHERRY_PLUGIN_DOMAIN).'</p>';
		$out .= '<iframe width="585" height="440" src="'.$video_src.'" frameborder="0" allowfullscreen></iframe>';
		$out .= '</div>';
		$out .= '</div>';

		return $out;
	}
	//help_export
	function cherry_plugin_help_export_popup(){
		$video_src = cherry_plugin_remote_query(array('data_type' => 'info'));
		$video_src = $video_src['video_help_export'];

		$out =  '<div id="help_export" style="display:none;">';
		$out .= '<div style="text-align:center">';
		$out .= '<p>'.__('Files are downloaded as a .zip archive', CHERRY_PLUGIN_DOMAIN).'</p>';
		$out .= '<iframe width="585" height="440" src="'.$video_src.'" frameborder="0" allowfullscreen></iframe>';
		$out .= '</div>';
		$out .= '</div>';

		return $out;
	}