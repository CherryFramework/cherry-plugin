<?php
	if(!function_exists('get_cherry_plugin_header')){
		function get_cherry_plugin_header($attr = array('title'      => '', 'icon_class' => '')){
			extract($attr);

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
			$output .= '<header>';
			$output .= ($icon_class) ? '<div class="'.$icon_class.' icon32"><br></div>' : '';
			$output .= ($title) ? '<h2>'.$title.'</h2>' : '' ;
			$output .= '<div class="extern-links">';
			$output .= '<a href="'.$doc_link.'" target="_blank"><span class="icon-book"></span>'.__('Documentation', CHERRY_PLUGIN_DOMIN).'</a>';
			$output .= '<a href="'.$support_link.'" target="_blank"><span class="icon-wrench"></span>'.__('Support', CHERRY_PLUGIN_DOMIN).'</a>';
			$output .= '</div>';
			$output .= '</header><div class="clear"></div>';

			echo $output;
		}
	}
	if(!function_exists('get_cherry_plugin_footer')){
		function get_cherry_plugin_footer(){
			$output = '</div>';

			echo $output;
		}
	}