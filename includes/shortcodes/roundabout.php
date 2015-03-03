<?php
/**
 * Roundabout shortcode.
 */
if ( !function_exists( 'shortcode_roundabout' ) ) {
	function shortcode_roundabout( $atts, $content = null, $shortcodename = '' ) {
		extract( shortcode_atts( array(
			'title'            => '',
			'num'              => '3',
			'type'             => '',
			'thumb_width'      => '375',
			'thumb_height'     => '250',
			'category'         => '',
			'custom_category'  => '',
			'more_button_text' => '',
			'more_button_link' => '',
			'custom_class'     => '',
		), $atts ) );

		wp_enqueue_script( 'roundabout_script', CHERRY_PLUGIN_URL . 'lib/js/roundabout/jquery.roundabout.min.js', array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'roundabout_shape', CHERRY_PLUGIN_URL . 'lib/js/roundabout/jquery.roundabout-shapes.min.js', array( 'jquery' ), '1.0' );

		$ra_id       = uniqid();
		$itemcounter = 0;

		// Check what type of post user selected.
		switch ( $type ) {
			case 'blog':
				$type_post = 'post';
				break;

			case 'portfolio':
				$type_post = 'portfolio';
				break;

			case 'testimonial':
				$type_post = 'testi';
				break;

			case 'our team':
				$type_post = 'team';
				break;

			default:
				$type_post = 'post';
				break;
		}

		$output = '<div class="roundabout-holder ' . $custom_class . '">';

		if ( $title != '' ) {
			$output .= '<h2>' . $title . '</h2>';
		}

		$output .= '<ul id="roundabout-list-' . $ra_id . '" class="unstyled">';

		global $post;

		// WPML filter.
		$suppress_filters = get_option( 'suppress_filters' );

		$args = array(
			'post_type'              => $type_post,
			'category_name'          => $category,
			$type_post . '_category' => $custom_category,
			'posts_per_page'         => -1,
			'orderby'                => 'post_date',
			'order'                  => 'DESC',
			'suppress_filters'       => $suppress_filters,
		);

		$posts = get_posts( $args );
		$i     = 1;

		foreach ( $posts as $key => $post ) {

			// Check if WPML is activated.
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				global $sitepress;

				$post_lang = $sitepress->get_language_for_element($post->ID, 'post_' . $type_post);
				$curr_lang = $sitepress->get_current_language();

				// Unset not translated posts.
				if ( $post_lang != $curr_lang ) {
					unset( $posts[$key] );
				}

				// Post ID is different in a second language Solution.
				if ( function_exists( 'icl_object_id' ) ) {
					$post = get_post( icl_object_id( $post->ID, $type_post, true ) );
				}
			}

			setup_postdata( $post );

			if ( $num == -1 ) {
				$num = count( $posts );
			}

			if ( $num >= $i ) {
				if ( has_post_thumbnail( $post->ID ) ) {
					$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
					$url            = $attachment_url['0'];
					$image          = aq_resize($url, $thumb_width, $thumb_height, true);
					$output .= '<li class="list-item-'.$itemcounter.'">';
					$output .= '<a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
					$output .= '<img src="'.$image.'" alt="'.get_the_title($post->ID).'" />';
					$output .= '</a>';
					$output .= '</li>';
					$i++;
					$itemcounter++;
				}
			}
		}

		// Restore the global $post variable.
		wp_reset_postdata();
		$output .= '</ul>';

		if (($more_button_text != '') && ($more_button_link != '')) {
			$output .= '<a class="btn btn-primary" href="' . $more_button_link . '">' . $more_button_text . '</a>';
		}

		$output .= '<script>
				jQuery(document).ready(function() {
					jQuery("#roundabout-list-'.$ra_id.'").roundabout({
						minOpacity: 1,
						minScale: 0.6,
						minZ: 0,
						shape: "square",
						responsive: true
					});
					jQuery(window).bind("resize", function() {
						jQuery("#roundabout-list-'.$ra_id.' li").removeAttr("style");
						jQuery("#roundabout-list-'.$ra_id.'").roundabout({
							minOpacity: 1,
							minScale: 0.6,
							responsive: false
						});
					});
				});';
		$output .= '</script>';
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode( 'roundabout', 'shortcode_roundabout' );
} ?>