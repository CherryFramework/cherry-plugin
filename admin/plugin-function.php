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
		$out =  '<div id="help_import" style="display:none; text-align:center;">';
		$out .= '<div style="text-align:center">';
		$out .= '<p>'.__('CTRL+A (Command-A) to select all files', CHERRY_PLUGIN_DOMAIN).'</p>';
		//$out .=	'<img  class="demo_gif" src="'. CHERRY_PLUGIN_URL . 'admin/help/demo_import.gif'.'" alt="'.__('Files import', CHERRY_PLUGIN_DOMAIN).'">';
		$out .= '<iframe width="585" height="440" src="//www.youtube.com/embed/V0Z7asyfzJI" frameborder="0" allowfullscreen></iframe>';
		$out .= '</div>';
		$out .= '</div>';

		return $out;
	}
	//help_export
	function cherry_plugin_help_export_popup(){
		$out =  '<div id="help_export" style="display:none;">';
		$out .= '<div style="text-align:center">';
		$out .= '<p>'.__('Files are downloaded as a .zip archive', CHERRY_PLUGIN_DOMAIN).'</p>';
		//$out .= '<img  class="demo_gif" src="'. CHERRY_PLUGIN_URL . 'admin/help/demo_export.gif' .'" alt="'.__('Files export', CHERRY_PLUGIN_DOMAIN).'">';
		$out .= '<iframe width="585" height="440" src="//www.youtube.com/embed/c5oxrUvNDeQ" frameborder="0" allowfullscreen></iframe>';
		$out .= '</div>';
		$out .= '</div>';

		return $out;
	}