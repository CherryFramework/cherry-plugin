<?php
/**
 * Service Box
 *
 */
if (!function_exists('service_box_shortcode')) {

	function service_box_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(
			array(
				'title'        => '',
				'subtitle'     => '',
				'icon'         => '',
				'icon_link'    => '',
				'text'         => '',
				'btn_text'     => __('Read more', CHERRY_PLUGIN_DOMAIN),
				'btn_link'     => '',
				'btn_size'     => '',
				'target'       => '',
				'custom_class' => '',
		), $atts));

		$output =  '<div class="service-box '.$custom_class.'">';

		if($icon != 'no'){
			$icon_url = CHERRY_PLUGIN_URL . 'includes/images/' . strtolower($icon) . '.png' ;
			if( defined ('CHILD_DIR') ) {
				if(file_exists(CHILD_DIR.'/images/'.strtolower($icon).'.png')){
					$icon_url = CHILD_URL.'/images/'.strtolower($icon).'.png';
				}
			}
			if ( empty( $icon_link ) ) {
				$output .= '<figure class="icon"><img src="'.$icon_url.'" alt="" /></figure>';
			} else {
				$output .= '<figure class="icon"><a href="' . esc_url( $icon_link ) . '"><img src="' . $icon_url . '" alt="" /></a></figure>';
			}
		}

		$output .= '<div class="service-box_body">';

		if ($title!="") {
			$output .= '<h2 class="title">';
			$output .= $title;
			$output .= '</h2>';
		}
		if ($subtitle!="") {
			$output .= '<h5 class="sub-title">';
			$output .= $subtitle;
			$output .= '</h5>';
		}
		if ($text!="") {
			$output .= '<div class="service-box_txt">';
			$output .= $text;
			$output .= '</div>';
		}
		if ($btn_link!="") {
			$output .=  '<div class="btn-align"><a href="'.$btn_link.'" title="'.$btn_text.'" class="btn btn-inverse btn-'.$btn_size.' btn-primary " target="'.$target.'">';
			$output .= $btn_text;
			$output .= '</a></div>';
		}
		$output .= '</div>';
		$output .= '</div><!-- /Service Box -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('service_box', 'service_box_shortcode');

}?>