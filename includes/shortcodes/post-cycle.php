<?php
/**
 * Post Cycle
 *
 */
if (!function_exists('shortcode_post_cycle')) {

	function shortcode_post_cycle( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
				'num'              => '5',
				'type'             => 'post',
				'meta'             => '',
				'effect'           => 'slide',
				'thumb'            => 'true',
				'thumb_width'      => '200',
				'thumb_height'     => '180',
				'more_text_single' => __('Read more', CHERRY_PLUGIN_DOMAIN),
				'category'         => '',
				'custom_category'  => '',
				'excerpt_count'    => '15',
				'pagination'       => 'true',
				'navigation'       => 'true',
				'custom_class'     => ''
		), $atts));

		$type_post         = $type;
		$slider_pagination = $pagination;
		$slider_navigation = $navigation;
		$random            = gener_random(10);
		$i                 = 0;
		$rand              = rand();
		$count             = 0;
		if ( is_rtl() ) {
			$is_rtl = 'true';
		} else {
			$is_rtl = 'false';
		}

		$output = '<script type="text/javascript">
						jQuery(window).load(function() {
							jQuery("#flexslider_'.$random.'").flexslider({
								animation: "'.$effect.'",
								smoothHeight : true,
								directionNav: '.$slider_navigation.',
								controlNav: '.$slider_pagination.',
								rtl: '.$is_rtl.'
							});
						});';
		$output .= '</script>';
		$output .= '<div id="flexslider_'.$random.'" class="flexslider no-bg '.$custom_class.'">';
			$output .= '<ul class="slides">';

			global $post;
			global $my_string_limit_words;

			// WPML filter
			$suppress_filters = get_option('suppress_filters');

			$args = array(
				'post_type'              => $type_post,
				'category_name'          => $category,
				$type_post . '_category' => $custom_category,
				'numberposts'            => $num,
				'orderby'                => 'post_date',
				'order'                  => 'DESC',
				'suppress_filters'       => $suppress_filters
			);

			$latest = get_posts($args);

			foreach($latest as $key => $post) {
				//Check if WPML is activated
				if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
					global $sitepress;

					$post_lang = $sitepress->get_language_for_element($post->ID, 'post_' . $type_post);
					$curr_lang = $sitepress->get_current_language();
					// Unset not translated posts
					if ( $post_lang != $curr_lang ) {
						unset( $latest[$key] );
					}
					// Post ID is different in a second language Solution
					if ( function_exists( 'icl_object_id' ) ) {
						$post = get_post( icl_object_id( $post->ID, $type_post, true ) );
					}
				}
				setup_postdata($post);
				$excerpt        = get_the_excerpt();
				$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
				$url            = $attachment_url['0'];
				$image          = aq_resize($url, $thumb_width, $thumb_height, true);

				$output .= '<li class="list-item-'.$count.'">';

					if ($thumb == 'true') {

						if ( has_post_thumbnail($post->ID) ){
							$output .= '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
							$output .= '<img  src="'.$image.'" alt="'.get_the_title($post->ID).'" />';
							$output .= '</a></figure>';
						} else {

							$thumbid = 0;
							$thumbid = get_post_thumbnail_id($post->ID);

							$images = get_children( array(
								'orderby'        => 'menu_order',
								'order'          => 'ASC',
								'post_type'      => 'attachment',
								'post_parent'    => $post->ID,
								'post_mime_type' => 'image',
								'post_status'    => null,
								'numberposts'    => -1
							) );

							if ( $images ) {

								$k = 0;
								//looping through the images
								foreach ( $images as $attachment_id => $attachment ) {
									// $prettyType = "prettyPhoto-".$rand ."[gallery".$i."]";
									//if( $attachment->ID == $thumbid ) continue;

									$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
									$img = aq_resize( $image_attributes[0], $thumb_width, $thumb_height, true ); //resize & crop img
									$alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
									$image_title = $attachment->post_title;

									if ( $k == 0 ) {
										$output .= '<figure class="featured-thumbnail">';
										$output .= '<a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
										$output .= '<img  src="'.$img.'" alt="'.get_the_title($post->ID).'" />';
										$output .= '</a></figure>';
									} break;
									$k++;
								}
							}
						}
					}

					$output .= '<h5><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
					$output .= get_the_title($post->ID);
					$output .= '</a></h5>';

					if($meta == 'true'){
						$output .= '<span class="meta">';
						$output .= '<span class="post-date">';
						$output .= get_the_date();
						$output .= '</span>';
						$output .= '<span class="post-comments">'.__('Comments', CHERRY_PLUGIN_DOMAIN).": ";
						$output .= '<a href="'.get_comments_link($post->ID).'">';
						$output .= get_comments_number($post->ID);
						$output .= '</a>';
						$output .= '</span>';
						$output .= '</span>';
					}
					//display post options
					$output .= '<div class="post_options">';

					switch( $type_post ) {

						case "team":
							$teampos    = get_post_meta( $post->ID, 'my_team_pos', true );
							$team_email = sanitize_email( get_post_meta( $post->ID, 'my_team_email', true ) );
							$teaminfo   = get_post_meta( $post->ID, 'my_team_info', true );

							if ( !empty( $teampos ) ) {
								$output .= "<span class='page-desc'>" . $teampos . "</span><br>";
							}

							if ( !empty( $team_email ) && is_email( $team_email ) ) {
								$output .= '<span class="team-email"><a href="mailto:' . antispambot( $team_email, 1 ) . '">' . antispambot( $team_email ) . ' </a></span><br>';
							}

							if ( !empty( $teaminfo ) ) {
								$output .= '<span class="team-content post-content team-info">' . esc_html( $teaminfo ) . '</span>';
							}

							$output .= cherry_get_post_networks(array('post_id' => $post->ID, 'display_title' => false, 'output_type' => 'return'));
							break;

						case "testi":
							$testiname  = get_post_meta( $post->ID, 'my_testi_caption', true );
							$testiurl   = esc_url( get_post_meta( $post->ID, 'my_testi_url', true ) );
							$testiinfo  = get_post_meta( $post->ID, 'my_testi_info', true );
							$testiemail = sanitize_email( get_post_meta($post->ID, 'my_testi_email', true ) );

							if ( !empty( $testiname ) ) {
								$output .= '<span class="user">' . $testiname . '</span>, ';
							}

							if ( !empty( $testiinfo ) ) {
								$output .= '<span class="info">' . $testiinfo . '</span><br>';
							}

							if ( !empty( $testiurl ) ) {
								$output .= '<a class="testi-url" href="' . $testiurl . '" target="_blank">' . $testiurl . '</a><br>';
							}

							if ( !empty( $testiemail ) && is_email( $testiemail ) ) {
								$output .= '<a class="testi-email" href="mailto:' . antispambot( $testiemail, 1 ) . '">' . antispambot( $testiemail ) . ' </a>';
							}
							break;

						case "portfolio":
							$portfolioClient = (get_post_meta($post->ID, 'tz_portfolio_client', true)) ? get_post_meta($post->ID, 'tz_portfolio_client', true) : "";
							$portfolioDate = (get_post_meta($post->ID, 'tz_portfolio_date', true)) ? get_post_meta($post->ID, 'tz_portfolio_date', true) : "";
							$portfolioInfo = (get_post_meta($post->ID, 'tz_portfolio_info', true)) ? get_post_meta($post->ID, 'tz_portfolio_info', true) : "";
							$portfolioURL = (get_post_meta($post->ID, 'tz_portfolio_url', true)) ? get_post_meta($post->ID, 'tz_portfolio_url', true) : "";
							$output .="<strong class='portfolio-meta-key'>".__('Client', CHERRY_PLUGIN_DOMAIN).": </strong><span> ".$portfolioClient."</span><br>";
							$output .="<strong class='portfolio-meta-key'>".__('Date', CHERRY_PLUGIN_DOMAIN).": </strong><span> ".$portfolioDate."</span><br>";
							$output .="<strong class='portfolio-meta-key'>".__('Info', CHERRY_PLUGIN_DOMAIN).": </strong><span> ".$portfolioInfo."</span><br>";
							$output .="<a href='".$portfolioURL."'>".__('Launch Project', CHERRY_PLUGIN_DOMAIN)."</a><br>";
							break;

						default:
							$output .="";
					};
					$output .= '</div>';

					if($excerpt_count >= 1){
						$output .= '<p class="excerpt">';
						$output .= my_string_limit_words($excerpt,$excerpt_count);
						$output .= '</p>';
					}

					if($more_text_single!=""){
						$output .= '<a href="'.get_permalink($post->ID).'" class="btn btn-primary" title="'.get_the_title($post->ID).'">';
						$output .= $more_text_single;
						$output .= '</a>';
					}

				$output .= '</li>';
				$count++;
			}
			wp_reset_postdata(); // restore the global $post variable
			$output .= '</ul>';
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('post_cycle', 'shortcode_post_cycle');

}?>