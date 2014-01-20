<?php
class cherry_plugin_components {
	public function get_header($attr){
		$default = array('title' => '', 'wrapper_class' => '', 'wrapper_id' => '');
		extract(array_merge($default, $attr));

		switch (get_bloginfo("language")) {
			case 'ru_RU':
				$locals = 'ru';
				break;
			case 'es_ES':
				$locals = 'es';
				break;
			case 'de_DE':
				$locals = 'de';
				break;
			default:
				$locals = '';
				break;
		}
		$support_link = 'http://info.template-help.com/help/'.$locals.'cms-blog-templates/wordpress/wordpress-tutorials/';

		$doc_link = 'http://info.template-help.com/help/quick-start-guide/';
		if (class_exists('Woocommerce')) {
			$doc_link .= 'woocommerce/';
		} elseif (function_exists('jigoshop_init')) {
			$doc_link .= 'jigoshop-cherry-framework/';
		} else {
			$doc_link .= 'cherry-framework/';
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