<?php
class cherry_plugin_components {
	public function get_header($attr){
		$default = array('title' => '', 'wrapper_class' => '', 'wrapper_id' => '');
		extract(array_merge($default, $attr));
		$language = get_bloginfo("language");

		switch ($language) {
			case 'ru-RU':
				$locals = '_'.$language;
				break;
			case 'es-ES':
				$locals = '_'.$language;
				break;
			case 'de-DE':
				$locals = '_'.$language;
				break;
			default:
				$locals = '';
				break;
		}
		$get_remote_info = cherry_plugin_remote_query(array('data_type' => 'info'));
		$support_link = $get_remote_info['support_url'.$locals];
		$doc_link = $get_remote_info['document_url'];

		if (class_exists('Woocommerce')) {
			$doc_link = $get_remote_info['document_url_woocommerce'];
		} elseif (function_exists('jigoshop_init')) {
			$doc_link = $get_remote_info['document_url_jigoshop'];
		}

		$output = '<div class="wrap cherry-plugin">';
		$output .= '<header><h2>';
		$output .= ($title) ? $title : '' ;
		$output .= '<span class="extern-links">';
		$output .= '<a href="'.$doc_link.'" target="_blank"><span class="icon-book"></span>'.__('Documentation', CHERRY_PLUGIN_DOMAIN).'</a>';
		$output .= '<a href="'.$support_link.'" target="_blank"><span class="icon-wrench"></span>'.__('Support', CHERRY_PLUGIN_DOMAIN).'</a>';
		$output .= '</span></h2>';
		$output_wrapper_id = ($wrapper_id) ? 'id="'.$wrapper_id.'"' : '' ;
		$output .= '</header><div class="clear"></div><div '.$output_wrapper_id.' class="'.$wrapper_class.' stuffbox postbox">';

		echo $output;
	}
	public function get_footer(){
		$output = '<div class="clear"></div></div></div>';

		echo $output;
	}
}