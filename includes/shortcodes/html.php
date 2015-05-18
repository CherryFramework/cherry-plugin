<?php
/**
 *
 * HTML Shortcodes
 *
 */

// Frames
if (!function_exists('frame_shortcode')) {
	function frame_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<figure class="frame thumbnail align' . $atts['align'] . ' clearfix">';
		$output .= do_shortcode($content);
		$output .= '</figure>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('frame', 'frame_shortcode');
}
// Button
if (!function_exists('button_shortcode')) {
	function button_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(
			array(
				'link'    => 'http://www.google.com',
				'text'    => __('Read more', CHERRY_PLUGIN_DOMAIN),
				'size'    => 'normal',
				'style'   => '',
				'target'  => '_self',
				'display' => '',
				'class'   => '',
				'icon'    => 'no'
		), $atts));

		$output =  '<a href="'.$link.'" title="'.$text.'" class="btn btn-'.$style.' btn-'.$size.' btn-'.$display.' '.$class.'" target="'.$target.'">';
		if ($icon != 'no') {
			$output .= '<i class="icon-'.$icon.'"></i>';
		}
		$output .= $text;
		$output .= '</a><!-- .btn -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('button', 'button_shortcode');
}

// Map
if (!function_exists('map_shortcode')) {
	function map_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(
			array(
				'src'    => '',
				'width'  => '',
				'height' => ''
		), $atts));

		$output =  '<div class="google-map">';
			$output .= '<iframe src="'.$src.'" frameborder="0" width="'.$width.'" height="'.$height.'" marginwidth="0" marginheight="0" scrolling="no">';
			$output .= '</iframe>';
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('map', 'map_shortcode');
}
// google_api_map
if ( !function_exists('google_map_api_shortcode') ) {
	function google_map_api_shortcode( $atts, $content = null ) {
		extract(shortcode_atts(array(
				'lat_value'      => '41.850033'
			,	'lng_value'      => '-87.6500523'
			,	'zoom_value'     => '8'
			,	'zoom_wheel'     => 'no'
			,	'custom_class'  => ''
		), $atts));

		$random_id        = rand();
		$lat_value        = floatval( $lat_value );
		$lng_value        = floatval( $lng_value );
		$zoom_value       = intval( $zoom_value );
		$zoom_wheel       = $zoom_wheel=='yes' ? 'true' : 'false';

		$output = '<div class="google-map-api '.$custom_class.'">';
		$output .= '<div id="map-canvas-'.$random_id.'" class="gmap"></div>';
		$output .= '</div>';
		$output .= '<script type="text/javascript">
				google_api_map_init_'.$random_id.'();
				function google_api_map_init_'.$random_id.'(){
					var map;
					var coordData = new google.maps.LatLng(parseFloat('.$lat_value.'), parseFloat('.$lng_value.'));
					var marker;
					var isDraggable = jQuery(document).width() > 768 ? true : false;

					function initialize() {
						var mapOptions = {
							zoom: '.$zoom_value.',
							center: coordData,
							draggable: isDraggable,
							scrollwheel: '.$zoom_wheel.'
						}
						var map = new google.maps.Map(document.getElementById("map-canvas-'.$random_id.'"), mapOptions);
						marker = new google.maps.Marker({
							map: map,
							draggable: false,
							position: coordData
						});
					}
					google.maps.event.addDomListener(window, "load", initialize);
				}

		</script>';
		return $output;
	}
	add_shortcode('google_map_api', 'google_map_api_shortcode');
}

// Dropcaps
if (!function_exists('dropcap_shortcode')) {
	function dropcap_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<span class="dropcap">';
		$output .= do_shortcode($content);
		$output .= '</span><!-- .dropcap (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('dropcap', 'dropcap_shortcode');
}

// Horizontal Rule
if (!function_exists('hr_shortcode')) {
	function hr_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="hr"></div><!-- .hr (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('hr', 'hr_shortcode');
}


// Small Horizontal Rule
if (!function_exists('sm_hr_shortcode')) {
	function sm_hr_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="sm_hr"></div><!-- .sm_hr (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('sm_hr', 'sm_hr_shortcode');
}

// Spacer
if (!function_exists('spacer_shortcode')) {
	function spacer_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="spacer"></div><!-- .spacer (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('spacer', 'spacer_shortcode');
}

// Blockquote
if (!function_exists('blockquote_shortcode')) {
	function blockquote_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<blockquote>';
		$output .= do_shortcode($content);
		$output .= '</blockquote><!-- blockquote (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('blockquote', 'blockquote_shortcode');
}

// Row
if (!function_exists('row_shortcode')) {
	function row_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'custom_class'  => ''
		), $atts));
		// add divs to the content
		$output = '<div class="row '.$custom_class.'">';
		$output .= do_shortcode($content);
		$output .= '</div><!-- .row (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('row', 'row_shortcode');
}

// Row Inner
if (!function_exists('row_inner_shortcode')) {
function row_inner_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'custom_class'  => ''
		), $atts));
		// add divs to the content
		$output = '<div class="row '.$custom_class.'">';
		$output .= do_shortcode($content);
		$output .= '</div> <!-- .row (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('row_in', 'row_inner_shortcode');
}

// Row Fluid
if (!function_exists('row_fluid_shortcode')) {
	function row_fluid_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'custom_class'  => ''
		), $atts));
		// add divs to the content
		$output = '<div class="row-fluid '.$custom_class.'">';
		$output .= do_shortcode($content);
		$output .= '</div> <!-- .row-fluid (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('row_fluid', 'row_fluid_shortcode');
}
// Clear
if (!function_exists('clear_shortcode')) {
	function clear_shortcode() {
		return '<div class="clear"></div><!-- .clear (end) -->';
	}
	add_shortcode('clear', 'clear_shortcode');
}

// Address
if (!function_exists('address_shortcode')) {
	function address_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<address>';
		$output .= do_shortcode($content);
		$output .= '</address><!-- address (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('address', 'address_shortcode');
}

/**
 * Lists
 */

// Unstyled
if ( !function_exists('list_un_shortcode') ) {
	function list_un_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list unstyled">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('list_un', 'list_un_shortcode');
}

// Check List
if ( !function_exists('check_list_shortcode') ) {
	function check_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled check-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('check_list', 'check_list_shortcode');
}

// OK-circle List
if ( !function_exists('ok_circle_list_shortcode') ) {
	function ok_circle_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled ok-circle-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('ok_circle_list', 'ok_circle_list_shortcode');
}

// OK-sign List
if ( !function_exists('ok_sign_list_shortcode') ) {
	function ok_sign_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled ok-sign-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('ok_sign_list', 'ok_sign_list_shortcode');
}

// Check2 List
if ( !function_exists('check2_list_shortcode') ) {
	function check2_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled check2-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('check2_list', 'check2_list_shortcode');
}

// Arrow List
if ( !function_exists('arrow_list_shortcode') ) {
	function arrow_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled arrow-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('arrow_list', 'arrow_list_shortcode');
}

// Arrow2 List
if ( !function_exists('arrow2_list_shortcode') ) {
	function arrow2_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled arrow2-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('arrow2_list', 'arrow2_list_shortcode');
}

// Circle-Arrow List
if ( !function_exists('circle_arrow_list_shortcode') ) {
	function circle_arrow_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled circle-arrow-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('circle_arrow_list', 'circle_arrow_list_shortcode');
}

// Caret List
if ( !function_exists('caret_list_shortcode') ) {
	function caret_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled caret-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('caret_list', 'caret_list_shortcode');
}

// Angle List
if ( !function_exists('angle_list_shortcode') ) {
	function angle_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled angle-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('angle_list', 'angle_list_shortcode');
}

// Double-Angle List
if ( !function_exists('double_angle_list_shortcode') ) {
	function double_angle_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled double-angle-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('double_angle_list', 'double_angle_list_shortcode');
}

// Star List
if ( !function_exists( 'star_list_shortcode') ) {
	function star_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled star-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('star_list', 'star_list_shortcode');
}

// Plus List
if ( !function_exists('plus_list_shortcode') ) {
	function plus_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled plus-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('plus_list', 'plus_list_shortcode');
}

// Minus List
if ( !function_exists('minus_list_shortcode') ) {
	function minus_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled minus-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('minus_list', 'minus_list_shortcode');
}

// Circle List
if ( !function_exists('circle_list_shortcode') ) {
	function circle_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled circle-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('circle_list', 'circle_list_shortcode');
}

// Circle Blank List
if ( !function_exists('circle_blank_list_shortcode') ) {
	function circle_blank_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled circle-blank-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('circle_blank_list', 'circle_blank_list_shortcode');
}

// Custom List
if ( !function_exists('custom_list_shortcode') ) {
	function custom_list_shortcode( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="list styled custom-list">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('custom_list', 'custom_list_shortcode');
}

// Vertical Rule
if (!function_exists('vr_shortcode')) {
	function vr_shortcode( $atts, $content = null, $shortcodename = '' ) {

		$output = '<div class="vertical-divider">';
		$output .= do_shortcode($content);
		$output .= '</div><!-- divider (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('vr', 'vr_shortcode');
}


// Label
if (!function_exists('label_shortcode')) {
	function label_shortcode( $atts, $content = null, $shortcodename = '' ) {

		extract(shortcode_atts(
			array(
				'style' => '',
				'icon'  => ''
		), $atts));

		$output = '<span class="label label-'.$style.'">';
		if ($icon != "") {
			$output .= '<i class="'.$icon.'"></i>';
		}
		$output .= $content .'</span>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('label', 'label_shortcode');
}


// Text Highlight
if (!function_exists('highlight_shortcode')) {
	function highlight_shortcode( $atts, $content = null, $shortcodename = '' ) {

		$output = '<span class="text-highlight">';
		$output .= do_shortcode($content);
		$output .= '</span><!-- .highlight (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('highlight', 'highlight_shortcode');
}

// Icon
if (!function_exists('icon_shortcode')) {
	function icon_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(
			array(
				'icon_type'       => '',
				'icon'            => 'alert',
				'icon_font'       => '',
				'icon_font_size'  => '',
				'icon_font_color' => '',
				'custom_class'    => '',
				'align'           => ''
		), $atts));

		if ($icon_type == 'Images' || $icon_type == '') {
			$icon = isset($icon_images) ? $icon_images : $icon ;
			$icon_url = CHERRY_PLUGIN_URL . 'includes/images/iconSweets/' . strtolower($icon) . '.png' ;
			if( defined ('CHILD_DIR') ) {
				if(file_exists(CHILD_DIR.'/images/iconSweets/'.strtolower($icon).'.png')){
					$icon_url = CHILD_URL.'/images/iconSweets/'.strtolower($icon).'.png';
				}
			}
			$output = '<figure class="align'. $align ." ".$custom_class.'"><img src="'. $icon_url .'" alt=""></figure>';

			$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

			return $output;
		}else{
			$icon_font = ($icon_font != '')? strtolower($icon_font) : "icon-question";
			$icon_font_size = ($icon_font_size != '')? $icon_font_size : "14px";
			if(stripos($icon_font_size, "px")===false && stripos($icon_font_size, "em")===false){
				$icon_font_size = (int) $icon_font_size . "px";
			}
			$icon_font_color = ($icon_font_color != '')? $icon_font_color : "#00000";
			$output = '<figure class="align'.$align.' aligntext'.$align.' "><i class="'.$icon_font.' '.$custom_class.'" style="color:'.$icon_font_color.'; font-size:'.$icon_font_size.'; line-height:1.2em;"></i></figure>';

			$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

			return $output;
		}
	}
	add_shortcode('icon', 'icon_shortcode');
}

// Template URL
if ( !function_exists( 'template_url_shortcode' ) ) {

	function template_url_shortcode( $atts, $content = null ) {

		// Get the URL to the content area.
		$content_url = untrailingslashit( content_url() );

		// Find latest '/' in content URL.
		$last_slash_pos = strrpos( $content_url, '/' );

		if ( false === $last_slash_pos ) {

			return $content_url;

		} else {

			$template_url = substr( $content_url, 0, $last_slash_pos );

			return $template_url;
		}
	}
	add_shortcode( 'template_url', 'template_url_shortcode' );

}
// Extra Wrap
if (!function_exists('extra_wrap_shortcode')) {
	function extra_wrap_shortcode( $atts, $content = null, $shortcodename = '' ) {

		$output = '<div class="extra-wrap">';
			$output .= do_shortcode($content);
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('extra_wrap', 'extra_wrap_shortcode');
}
?>
