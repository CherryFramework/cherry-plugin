<?php
// =============================== My Post Cycle widget ======================================
class MY_CycleWidget extends WP_Widget {
	/* constructor */
	function MY_CycleWidget() {
		parent::WP_Widget(false, $name = __('Cherry - Post Cycle', CHERRY_PLUGIN_DOMAIN));
	}

	/* @see WP_Widget::widget */
	function widget($args, $instance) {
		extract( $args );
		$title            = apply_filters('widget_title', $instance['title']);
		$post_type        = $instance['post_type'];
		$categories       = $instance['categories'];
		$count            = $instance['count'];
		$thumb_width      = $instance['thumb_width'];
		$thumb_height     = $instance['thumb_height'];
		$show_title       = $instance['show_title'] == 'true' ? true : false;
		$show_date        = $instance['show_date'] == 'true' ? true : false;
		$show_author      = $instance['show_author'] == 'true' ? true : false;
		$show_comments    = $instance['show_comments'] == 'true' ? true : false;
		$show_excerpt     = $instance['show_excerpt'] == 'true' ? true : false;
		$excerpt_count    = $instance['excerpt_count'];
		$more_text_single = apply_filters( 'cherry_text_translate', $instance['more_text_single'], $instance['title'] . ' more_text_single' );
		$control_nav      = $instance['control_nav'];
		$direction_nav    = $instance['direction_nav'];
		$speed            = $instance['speed'];
		$speed            = (int)$speed*1000;

		$random = uniqid();

		$get_category_type = $post_type == 'post' ? 'category' : $post_type.'_category' ;
		$categories_ids = array();
		foreach ( explode(',', str_replace(', ', ',', $categories)) as $category ) {
			$get_cat_id = get_term_by( 'name', $category, $get_category_type );
			if ( $get_cat_id ) {
				$categories_ids[] = $get_cat_id->term_id;
			}
		}
		$get_query_tax = $categories_ids ? 'tax_query' : '' ;

		if ( !$count ) $count = -1;
		$args = array(
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => 1,
			'post_type'           => $post_type,
			"$get_query_tax"      => array(
				array(
					'taxonomy' => $get_category_type,
					'field'    => 'id',
					'terms'    => $categories_ids
					)
				)
		);
		$post_cycle = new WP_Query( $args );

		if ( $post_cycle->have_posts() ) :

			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;
			} ?>

			<script type="text/javascript">
				jQuery(window).load(function() {
					jQuery('#flexslider_<?php echo $random ?>').flexslider({
						animation: "slide",
						smoothHeight: true,
						slideshow: true,
						slideshowSpeed: <?php echo $speed; ?>,
						controlNav: <?php echo $control_nav; ?>,
						directionNav: <?php echo $direction_nav; ?>,
						prevText: '',
						nextText: ''
					});
				});
			</script>
			<div id="flexslider_<?php echo $random ?>" class="flexslider widget-flexslider">
				<ul class="slides unstyled">
				<?php
					while ( $post_cycle->have_posts() ) :
						$post_cycle->the_post();
						if ( has_post_thumbnail() ) {
							$post_id         = $post_cycle->post->ID;
							$post_title      = esc_html( get_the_title( $post_id ) );
							$post_title_attr = esc_attr( strip_tags( get_the_title( $post_id ) ) );
							$format          = get_post_format( $post_id );
							$format          = (empty( $format )) ? 'format-standart' : 'format-' . $format;
							$thumb           = get_post_thumbnail_id();
							$img_url         = wp_get_attachment_url( $thumb, 'full' );
							$image           = aq_resize( $img_url, $thumb_width, $thumb_height, true );
							$post_permalink  = ( $format == 'format-link' ) ? esc_url( get_post_meta( $post_id, 'tz_link_url', true ) ) : get_permalink( $post_id ) ;
							if ( has_excerpt( $post_id ) ) {
								$excerpt = get_the_excerpt();
							} else {
								$excerpt = get_the_content();
							}
						?>
						<li class="slide <?php echo $format; ?> clearfix">
							<figure class="thumbnail">
								<a href="<?php echo $post_permalink; ?>" title="<?php echo $post_title; ?>">
									<img src="<?php echo $image ?>" alt="<?php echo $post_title; ?>" />
								</a>
							</figure>
							<div class="desc">
							<?php

								// post date
								if ( $show_date ) { ?>
								<time datetime="<?php echo get_the_time( 'Y-m-d\TH:i:s', $post_id );?>"><?php echo get_the_date(); ?></time>

							<?php }

								// post author
								if ( $show_author ) { ?>
								<em class="author">
									<span><?php _e('by', CHERRY_PLUGIN_DOMAIN); ?></span>
									<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author_meta( 'display_name' ); ?></a>
								</em>
							<?php }

								// post comment count
								if ( $show_comments ) {
									$comment_count = $post_cycle->post->comment_count;
									if ( $comment_count > 1 ) :
										$comment_count = $comment_count . ' <span>' . __( 'Comments', CHERRY_PLUGIN_DOMAIN ) . '</span>';
									else :
										$comment_count = $comment_count . ' <span>' . __( 'Comment', CHERRY_PLUGIN_DOMAIN ) . '</span>';
									endif;
									echo '<a href="'. $post_permalink . '#comments" class="comments_link">' . $comment_count . '</a>';
								}

								// post title
								if ( $show_title ) { ?>
									<h5>
										<a href ="<?php echo $post_permalink; ?>" title="<?php echo $post_title_attr; ?>"><?php echo $post_title; ?></a>
									</h5>
								<?php }

								// post excerpt
								if ( $show_excerpt ) {
									if ( $excerpt_count > 0 ) { ?>
										<p class="excerpt">
											<?php echo my_string_limit_words( $excerpt, $excerpt_count ); ?>
										</p>
									<?php }
								}
							?>
							</div>
							<?php
							// post more button
							$more_text_single = esc_html( wp_kses_data( $more_text_single ) );
							if ( $more_text_single != '' ) { ?>
								<a href="<?php echo get_permalink( $post_id ); ?>" title="<?php echo $post_title_attr; ?>" class="btn btn-primary"><?php _e( $more_text_single, CHERRY_PLUGIN_DOMAIN ); ?></a>
							<?php } ?>
						</li><!-- .slide -->
						<?php }
					endwhile;
					?>
				</ul><!-- .slides -->
			</div><!-- .flexslider -->
			<?php echo $after_widget;
		endif;
		wp_reset_postdata();
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$defaults = array( 'title' => '', 'post_type' => 'post', 'categories' => '', 'count' => 5, 'thumb_width' => 370, 'thumb_height' => 270, 'show_title' => 'true', 'show_date' => 'false', 'show_author' => 'false', 'show_comments' => 'false', 'show_excerpt' => 'false', 'excerpt_count' => 5, 'more_text_single' => '', 'control_nav' => 'false', 'direction_nav' => 'true', 'speed' => 7 );
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title            = esc_attr($instance['title']);
		$post_type        = esc_attr($instance['post_type']);
		$categories       = esc_attr($instance['categories']);
		$count            = esc_attr($instance['count']);
		$thumb_width      = esc_attr($instance['thumb_width']);
		$thumb_height     = esc_attr($instance['thumb_height']);
		$show_title       = esc_attr($instance['show_title']);
		$show_date        = esc_attr($instance['show_date']);
		$show_author      = esc_attr($instance['show_author']);
		$show_comments    = esc_attr($instance['show_comments']);
		$show_excerpt     = esc_attr($instance['show_excerpt']);
		$excerpt_count    = esc_attr($instance['excerpt_count']);
		$more_text_single = esc_attr($instance['more_text_single']);
		$control_nav      = esc_attr($instance['control_nav']);
		$direction_nav    = esc_attr($instance['direction_nav']);
		$speed            = esc_attr($instance['speed']);
	?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', CHERRY_PLUGIN_DOMAIN); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Select post type:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$post_types = get_post_types( array(), 'names' );

				foreach ( $post_types as $key => $pt ) {
					$label_obj = get_post_type_object($pt);
					$labels    = $label_obj->labels->name;

					if ( $key=='page'
						|| $key=='revision'
						|| $key=='attachment'
						|| $key=='nav_menu_item'
						|| $key=='optionsframework' ) {
						continue;
					}

					$option = '<option value="' . $pt . '"';
					if ( $post_type === $pt ) {
						$option .= ' selected="selected"';
					}
					$option .= '>';
					$option .= $labels;
					$option .= '</option>';
					echo $option;
				}
			?>
		</select></label>
	</p>

	<p><label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories (names separated by ","):', CHERRY_PLUGIN_DOMAIN); ?><input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo $categories; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Posts count:', CHERRY_PLUGIN_DOMAIN); ?><input class="widefat" style="width:60px; display:block; text-align:center" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" value="<?php echo $count; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('thumb_width'); ?>"><?php _e('Thumbnail Width (in pixels):', CHERRY_PLUGIN_DOMAIN); ?><input class="widefat" style="width:60px; display:block; text-align:center" id="<?php echo $this->get_field_id('thumb_width'); ?>" name="<?php echo $this->get_field_name('thumb_width'); ?>" type="number" value="<?php echo $thumb_width; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('thumb_height'); ?>"><?php _e('Thumbnail Height (in pixels):', CHERRY_PLUGIN_DOMAIN); ?><input class="widefat" style="width:60px; display:block; text-align:center" id="<?php echo $this->get_field_id('thumb_height'); ?>" name="<?php echo $this->get_field_name('thumb_height'); ?>" type="number" value="<?php echo $thumb_height; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Show titles:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$option = '<option value="true"';
				if ($show_title == 'true') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('yes', CHERRY_PLUGIN_DOMAIN) . '</option>';
				$option .= '<option value="false"';
				if ($show_title == 'false') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('no', CHERRY_PLUGIN_DOMAIN) . '</option>';
				echo $option;
			?>
		</select>
		</label>
	</p>

	<p><label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show date:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$option = '<option value="true"';
				if ($show_date == 'true') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('yes', CHERRY_PLUGIN_DOMAIN) . '</option>';
				$option .= '<option value="false"';
				if ($show_date == 'false') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('no', CHERRY_PLUGIN_DOMAIN) . '</option>';
				echo $option;
			?>
		</select>
		</label>
	</p>

	<p><label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Show author:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$option = '<option value="true"';
				if ($show_author == 'true') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('yes', CHERRY_PLUGIN_DOMAIN) . '</option>';
				$option .= '<option value="false"';
				if ($show_author == 'false') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('no', CHERRY_PLUGIN_DOMAIN) . '</option>';
				echo $option;
			?>
		</select>
		</label>
	</p>

	<p><label for="<?php echo $this->get_field_id('show_comments'); ?>"><?php _e('Show comments:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('show_comments'); ?>" name="<?php echo $this->get_field_name('show_comments'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$option = '<option value="true"';
				if ($show_comments == 'true') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('yes', CHERRY_PLUGIN_DOMAIN) . '</option>';
				$option .= '<option value="false"';
				if ($show_comments == 'false') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('no', CHERRY_PLUGIN_DOMAIN) . '</option>';
				echo $option;
			?>
		</select>
		</label>
	</p>

	<p><label for="<?php echo $this->get_field_id('show_excerpt'); ?>"><?php _e('Show excerpt:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$option = '<option value="true"';
				if ($show_excerpt == 'true') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('yes', CHERRY_PLUGIN_DOMAIN) . '</option>';
				$option .= '<option value="false"';
				if ($show_excerpt == 'false') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('no', CHERRY_PLUGIN_DOMAIN) . '</option>';
				echo $option;
			?>
		</select>
		</label>
	</p>

	<p><label for="<?php echo $this->get_field_id('excerpt_count'); ?>"><?php _e('Excerpt word count:', CHERRY_PLUGIN_DOMAIN); ?><input class="widefat" style="width:60px; display:block; text-align:center" id="<?php echo $this->get_field_id('excerpt_count'); ?>" name="<?php echo $this->get_field_name('excerpt_count'); ?>" type="number" value="<?php echo $excerpt_count; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('more_text_single'); ?>"><?php _e('More Button Text:', CHERRY_PLUGIN_DOMAIN); ?> <input class="widefat" id="<?php echo $this->get_field_id('more_text_single'); ?>" name="<?php echo $this->get_field_name('more_text_single'); ?>" type="text" value="<?php echo $more_text_single; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Slideshow cycling speed (in sec.):', CHERRY_PLUGIN_DOMAIN); ?><input class="widefat" style="width:50px; display:block; text-align:center" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="number" value="<?php echo $speed; ?>" /></label></p>

	<p><label for="<?php echo $this->get_field_id('direction_nav'); ?>"><?php _e('Arrows:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('direction_nav'); ?>" name="<?php echo $this->get_field_name('direction_nav'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$option = '<option value="true"';
				if ($direction_nav == 'true') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('yes', CHERRY_PLUGIN_DOMAIN) . '</option>';
				$option .= '<option value="false"';
				if ($direction_nav == 'false') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('no', CHERRY_PLUGIN_DOMAIN) . '</option>';
				echo $option;
			?>
		</select>
		</label>
	</p>

	<p><label for="<?php echo $this->get_field_id('control_nav'); ?>"><?php _e('Pagination:', CHERRY_PLUGIN_DOMAIN); ?><br />
		<select id="<?php echo $this->get_field_id('control_nav'); ?>" name="<?php echo $this->get_field_name('control_nav'); ?>" style="width:100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" >
			<?php
				$option = '<option value="true"';
				if ($control_nav == 'true') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('yes', CHERRY_PLUGIN_DOMAIN) . '</option>';
				$option .= '<option value="false"';
				if ($control_nav == 'false') {
					$option .= ' selected="selected"';
				}
				$option .= '>' . __('no', CHERRY_PLUGIN_DOMAIN) . '</option>';
				echo $option;
			?>
		</select>
		</label>
	</p>
	<?php
	}
} // class Cycle Widget
?>