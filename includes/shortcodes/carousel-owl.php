<?php
/**
 * Carousel OWL
 */
if ( !function_exists('shortcode_carousel_owl') ) {
	function shortcode_carousel_owl( $atts, $content = null, $shortcodename = '' ) {
		wp_enqueue_script( 'owl-carousel', CHERRY_PLUGIN_URL . 'lib/js/owl-carousel/owl.carousel.min.js', array('jquery'), '1.31', true );

		extract( shortcode_atts( array(
			'title'              => '',
			'posts_count'        => 10,
			'post_type'          => 'blog',
			'post_status'        => 'publish',
			'visibility_items'   => 5,
			'thumb'              => 'yes',
			'thumb_width'        => 220,
			'thumb_height'       => 180,
			'more_text_single'   => '',
			'categories'         => '',
			'excerpt_count'      => 15,
			'date'               => 'yes',
			'author'             => 'yes',
			'comments'           => 'no',
			'auto_play'          => 0,
			'display_navs'       => 'yes',
			'display_pagination' => 'yes',
			'custom_class'       => ''
		), $atts ) );

		$random_ID          = uniqid();
		$posts_count        = intval( $posts_count );
		$thumb              = $thumb == 'yes' ? true : false;
		$thumb_width        = absint( $thumb_width );
		$thumb_height       = absint( $thumb_height );
		$excerpt_count      = absint( $excerpt_count );
		$visibility_items   = absint( $visibility_items );
		$auto_play          = absint( $auto_play );
		$date               = $date == 'yes' ? true : false;
		$author             = $author == 'yes' ? true : false;
		$comments           = $comments == 'yes' ? true : false;
		$display_navs       = $display_navs == 'yes' ? 'true' : 'false';
		$display_pagination = $display_pagination == 'yes' ? 'true' : 'false';
		$itemcounter = 0;

		switch ( strtolower( str_replace(' ', '-', $post_type) ) ) {
			case 'blog':
				$post_type = 'post';
				break;
			case 'portfolio':
				$post_type = 'portfolio';
				break;
			case 'testimonial':
				$post_type = 'testi';
				break;
			case 'services':
				$post_type = 'services';
				break;
			case 'our-team':
				$post_type = 'team';
			break;
		}

		$get_category_type = $post_type == 'post' ? 'category' : $post_type.'_category';
		$categories_ids = array();
		foreach ( explode(',', str_replace(', ', ',', $categories)) as $category ) {
			$get_cat_id = get_term_by( 'name', $category, $get_category_type );
			if ( $get_cat_id ) {
				$categories_ids[] = $get_cat_id->term_id;
			}
		}
		$get_query_tax = $categories_ids ? 'tax_query' : '';

		$suppress_filters = get_option('suppress_filters'); // WPML filter

		// WP_Query arguments
		$args = array(
			'post_status'         => $post_status,
			'posts_per_page'      => $posts_count,
			'ignore_sticky_posts' => 1,
			'post_type'           => $post_type,
			'suppress_filters'    => $suppress_filters,
			"$get_query_tax"      => array(
				array(
					'taxonomy' => $get_category_type,
					'field'    => 'id',
					'terms'    => $categories_ids
					)
				)
		);

		// The Query
		$carousel_query = new WP_Query( $args );
		$output = '';

		if ( $carousel_query->have_posts() ) :

			$output .= '<div class="carousel-wrap ' . $custom_class . '">';
				$output .= $title ? '<h2>' . $title . '</h2>' : '';
				$output .= '<div id="owl-carousel-' . $random_ID . '" class="owl-carousel-' . $post_type . ' owl-carousel" data-items="' . $visibility_items . '" data-auto-play="' . $auto_play . '" data-nav="' . $display_navs . '" data-pagination="' . $display_pagination . '">';

				while ( $carousel_query->have_posts() ) : $carousel_query->the_post();
					$post_id         = $carousel_query->post->ID;
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

					$output .= '<div class="item ' . $format . ' item-list-'.$itemcounter.'">';

						// post thumbnail
						if ( $thumb ) :

							if ( has_post_thumbnail( $post_id ) ) {
								$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
								$url            = $attachment_url['0'];
								$image          = aq_resize($url, $thumb_width, $thumb_height, true);

								$output .= '<figure>';
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

										$output .= '<figure>';
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
							$output .= $date ? '<time datetime="' . get_the_time( 'Y-m-d\TH:i:s', $post_id ) . '">' . get_the_date() . '</time>' : '';

							// post author
							$output .= $author ? '<em class="author">&nbsp;<span>' . __('by ', CHERRY_PLUGIN_DOMAIN) . '</span>&nbsp;<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ).'">' . get_the_author_meta( 'display_name' ) . '</a> </em>' : '';

							// post comment count
							if ( $comments == 'yes' ) {
								$comment_count = $carousel_query->post->comment_count;
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
					$output .= '</div>';
					$itemcounter++;
				endwhile;
			$output .= '</div></div>';
		endif;

		// Restore original Post Data
		wp_reset_postdata();

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode( 'carousel_owl', 'shortcode_carousel_owl' );
} ?>