<?php
/**
 * Banner
 *
 */
if ( !function_exists( 'banner_shortcode' ) ) {

	function banner_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract( shortcode_atts(
			array(
				'img'          => '',
				'banner_link'  => '',
				'title'        => '',
				'text'         => '',
				'btn_text'     => '',
				'target'       => '',
				'custom_class' => ''
		), $atts));

		$uploads          = wp_upload_dir();
		$uploads_dir_name = end( ( explode( '/', $uploads['baseurl'] ) ) );

		$img_path = explode( 'uploads', $img );
		if ( 1 == count( $img_path ) ) {
			$img_path = explode( $uploads_dir_name, $img );
		}
		$_img = end( $img_path );

		if ( 1 < count( $img_path ) ) {
			$img = $uploads['baseurl'] . $_img;
		}

		$output =  '<div class="banner-wrap '.$custom_class.'">';
		if ($img !="") {
			$output .= '<figure class="featured-thumbnail">';
			if ($banner_link != "") {
				$output .= '<a href="'. $banner_link .'" title="'. $title .'"><img src="' . $img .'" title="'. $title .'" alt="" /></a>';
			} else {
				$output .= '<img src="' . $img .'" title="'. $title .'" alt="" />';
			}
			$output .= '</figure>';
		}
		if ($title!="") {
			$output .= '<h5>';
			$output .= $title;
			$output .= '</h5>';
		}
		if ($text!="") {
			$output .= '<p>';
			$output .= $text;
			$output .= '</p>';
		}
		if ($btn_text!="") {
			$output .=  '<div class="link-align banner-btn"><a href="'.$banner_link.'" title="'.$btn_text.'" class="btn btn-link" target="'.$target.'">';
			$output .= $btn_text;
			$output .= '</a></div>';
		}
		$output .= '</div><!-- .banner-wrap (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('banner', 'banner_shortcode');

} ?>