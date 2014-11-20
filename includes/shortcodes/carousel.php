<?php
/**
 * Carousel Elastislide
 */
if ( !function_exists('shortcode_carousel') ) {
	function shortcode_carousel( $atts, $content = null, $shortcodename = '' ) {
		extract( shortcode_atts( array(
			'title'            => '',
			'num'              => 8,
			'type'             => 'post',
			'thumb'            => 'true',
			'thumb_width'      => 220,
			'thumb_height'     => 180,
			'more_text_single' => '',
			'category'         => '',
			'custom_category'  => '',
			'excerpt_count'    => 12,
			'date'             => '',
			'author'           => '',
			'comments'         => '',
			'min_items'        => 3,
			'spacer'           => 18,
			'custom_class'     => ''
		), $atts) );

		switch ( strtolower( str_replace(' ', '-', $type) ) ) {
			case 'blog':
				$type = 'post';
				break;
			case 'portfolio':
				$type = 'portfolio';
				break;
			case 'testimonial':
				$type = 'testi';
				break;
			case 'services':
				$type = 'services';
				break;
			case 'our-team':
				$type = 'team';
			break;
		}

		$carousel_uniqid = uniqid();
		$thumb_width     = absint( $thumb_width );
		$thumb_height    = absint( $thumb_height );
		$excerpt_count   = absint( $excerpt_count );
		$itemcount = 0;

		$output = '<div class="carousel-wrap ' . $custom_class . '">';
			if ( !empty( $title{0} ) ) {
				$output .= '<h2>' . esc_html( $title ) . '</h2>';
			}
			$output .= '<div id="carousel-' . $carousel_uniqid . '" class="es-carousel-wrapper">';
			$output .= '<div class="es-carousel">';
				$output .= '<ul class="es-carousel_list unstyled clearfix">';

					// WPML filter
					$suppress_filters = get_option( 'suppress_filters' );

					$args = array(
						'post_type'         => $type,
						'category_name'     => $category,
						$type . '_category' => $custom_category,
						'numberposts'       => $num,
						'orderby'           => 'post_date',
						'order'             => 'DESC',
						'suppress_filters'  => $suppress_filters
					);

					global $post; // very important
					$carousel_posts = get_posts( $args );

					foreach ( $carousel_posts as $key => $post ) {
						$post_id = $post->ID;

						//Check if WPML is activated
						if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
							global $sitepress;

							$post_lang = $sitepress->get_language_for_element( $post_id, 'post_' . $type );
							$curr_lang = $sitepress->get_current_language();
							// Unset not translated posts
							if ( $post_lang != $curr_lang ) {
								unset( $carousel_posts[$j] );
							}
							// Post ID is different in a second language Solution
							if ( function_exists( 'icl_object_id' ) ) {
								$post = get_post( icl_object_id( $post_id, $type, true ) );
							}
						}
						setup_postdata( $post ); // very important
						$post_title      = esc_html( get_the_title( $post_id ) );
						$post_title_attr = esc_attr( strip_tags( get_the_title( $post_id ) ) );
						$format          = get_post_format( $post_id );
						$format          = (empty( $format )) ? 'format-standart' : 'format-' . $format;
						if ( get_post_meta( $post_id, 'tz_link_url', true ) ) {
							$post_permalink = ( $format == 'format-link' ) ? esc_url( get_post_meta( $post_id, 'tz_link_url', true ) ) : get_permalink( $post_id );
						} else {
							$post_permalink = get_permalink( $post_id );
						}
						if ( has_excerpt( $post_id ) ) {
							$excerpt = wp_strip_all_tags( get_the_excerpt() );
						} else {
							$excerpt = wp_strip_all_tags( strip_shortcodes (get_the_content() ) );
						}

						$output .= '<li class="es-carousel_li ' . $format . ' clearfix list-item-'.$itemcount.'">';

							if ( $thumb == 'true' ) :

								if ( has_post_thumbnail( $post_id ) ) {
									$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
									$url            = $attachment_url['0'];
									$image          = aq_resize($url, $thumb_width, $thumb_height, true);

									$output .= '<figure class="featured-thumbnail">';
										$output .= '<a href="' . $post_permalink . '" title="' . $post_title . '">';
											$output .= '<img src="' . $image . '" alt="' . $post_title . '" />';
										$output .= '</a>';
									$output .= '</figure>';

								} else {

									$attachments = get_children( array(
										'orderby'        => 'menu_order',
										'order'          => 'ASC',
										'post_type'      => 'attachment',
										'post_parent'    => $post_id,
										'post_mime_type' => 'image',
										'post_status'    => null,
										'numberposts'    => 1
									) );
									if ( $attachments ) {
										foreach ( $attachments as $attachment_id => $attachment ) {
											$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' );
											$img              = aq_resize( $image_attributes[0], $thumb_width, $thumb_height, true );
											$alt              = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );

											$output .= '<figure class="featured-thumbnail">';
													$output .= '<a href="' . $post_permalink.'" title="' . $post_title . '">';
														$output .= '<img src="' . $img . '" alt="' . $alt . '" />';
												$output .= '</a>';
											$output .= '</figure>';
										}
									}
								}

							endif;

							$output .= '<div class="desc">';

								// post date
								if ( $date == 'yes' ) {
									$output .= '<time datetime="' . get_the_time( 'Y-m-d\TH:i:s', $post_id ) . '">' . get_the_date() . '</time>';
								}

								// post author
								if ( $author == 'yes' ) {
									$output .= '<em class="author">&nbsp;<span>' . __('by', CHERRY_PLUGIN_DOMAIN) . '</span>&nbsp;<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a> </em>';
								}

								// post comment count
								if ( $comments == 'yes' ) {
									$comment_count = $post->comment_count;
									if ( $comment_count >= 1 ) :
										$comment_count = $comment_count . ' <span>' . __( 'Comments', CHERRY_PLUGIN_DOMAIN ) . '</span>';
									else :
										$comment_count = $comment_count . ' <span>' . __( 'Comment', CHERRY_PLUGIN_DOMAIN ) . '</span>';
									endif;
									$output .= '<a href="'. $post_permalink . '#comments" class="comments_link">' . $comment_count . '</a>';
								}

								// post title
								if ( !empty($post_title{0}) ) {
									$output .= '<h5><a href="' . $post_permalink . '" title="' . $post_title_attr . '">';
										$output .= $post_title;
									$output .= '</a></h5>';
								}

								// post excerpt
								if ( !empty($excerpt{0}) ) {
									$output .= $excerpt_count > 0 ? '<p class="excerpt">' . wp_trim_words( $excerpt, $excerpt_count ) . '</p>' : '';
								}

								// post more button
								$more_text_single = esc_html( wp_kses_data( $more_text_single ) );
								if ( $more_text_single != '' ) {
									$output .= '<a href="' . get_permalink( $post_id ) . '" class="btn btn-primary" title="' . $post_title_attr . '">';
										$output .= __( $more_text_single, CHERRY_PLUGIN_DOMAIN );
									$output .= '</a>';
								}
							$output .= '</div>';
						$output .= '</li>';
						$itemcount++;
					}
					wp_reset_postdata(); // restore the global $post variable

				$output .= '</ul>';
			$output .= '</div></div>';
			$output .= '<script>
				jQuery(document).ready(function(){
					jQuery("#carousel-' . $carousel_uniqid . '").elastislide({
						imageW  : ' . $thumb_width . ',
						minItems: ' . $min_items . ',
						speed   : 600,
						easing  : "easeOutQuart",
						margin  : ' . $spacer . ',
						border  : 0
					});
				})';
			$output .= '</script>';
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('carousel', 'shortcode_carousel');
} ?>