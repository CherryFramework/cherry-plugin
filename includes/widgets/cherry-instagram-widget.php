<?php
	class Cherry_Instagram_Widget extends WP_Widget {
		/* constructor */
		public function Cherry_Instagram_Widget() {
			parent::__construct(false, __('Cherry - Instagram', CHERRY_PLUGIN_DOMAIN), array('description' => __('Widget for popular social network Instagram', CHERRY_PLUGIN_DOMAIN)));
		}

		/** @see WP_Widget::widget */
		public function widget( $args, $instance ) {
			extract( array_merge( $args, $instance ) );

			if ( 'hashtag' == $endpoints ) {
				if ( empty( $tag ) ) {
					return print $before_widget . __( 'Please, enter #hashtag.', CHERRY_PLUGIN_DOMAIN ) . $after_widget;
				}
			}

			if ( 'self' == $endpoints ) {
				if ( empty( $user_name ) ) {
					return print $before_widget . __( 'Please, enter your username.', CHERRY_PLUGIN_DOMAIN ) . $after_widget;
				}
			}

			if ( empty( $client_id ) ) {
				return print $before_widget . __( 'Please, enter your Instagram CLIENT ID.', CHERRY_PLUGIN_DOMAIN ) . $after_widget;
			}

			if ( intval( $image_counter ) <= 0 ) {
				return '';
			}

			$config = array();

			if ( isset( $link ) ) $config[] = 'link';
			if ( isset( $display_comments ) ) $config[] = 'comments';
			if ( isset( $display_likes ) ) $config[] = 'likes';
			if ( isset( $display_time ) ) $config[] = 'time';
			if ( isset( $display_description ) ) $config[] = 'description';
			if ( isset( $image_size ) ) $config['thumb'] = $image_size;

			if ( 'self' == $endpoints ) {
				$user_id = $this->get_user_id( $user_name, $client_id );

				if ( ! $user_id ) {
					return print $before_widget . __( 'Please, enter a valid username and CLIENT ID.' ) . $after_widget;
				}

				$data = $user_id;

			} else {
				$data = $tag;
			}

			$config['endpoints'] = $endpoints;
			$photos = $this->get_photos( $data, $client_id, $image_counter, $config );

			if ( ! $photos ) {
				return print $before_widget . __( 'Please, enter a valid CLIENT ID.' ) . $after_widget;
			}

			$button_text = apply_filters( 'cherry_text_translate', $instance['button_text'], $instance['title'] . ' button_text' );

			$user_name         = strtolower( trim( $user_name ) );
			$date_format       = get_option( 'date_format' );
			$images_size_class = 'img_' . $image_size;

			$output = $before_widget;
				$output .= $title ? $before_title . $title . $after_title : '' ;

				$output .= '<div class="imgs_wrapper '.$images_size_class.'"><ul class="clearfix">';

				foreach ( (array) $photos as $key => $photo ) {

					$class = ( $key % 2 == 0 ) ? 'odd' : 'even';
					$class .= isset( $link ) ? ' inst_link' : '';
					$class .= isset( $photos[ $key + 1 ] ) ? '' : ' last_child';
					$desc  = ( isset( $photo['description'] ) ) ? $photo['description'] : '';

					$output .= '<li class="' . $class . '"><figure>';

						$output .= '<div class="img_wrapp">';
							$output .= isset( $link ) ? '<a href="' . esc_url( $photo['link'] ) . '" title="' . esc_attr( $desc ) . '" target="_blank" >' : '';
								$output .= '<img class="inst_img" src="' . esc_url( $photo['thumb'] ) . '" alt="' . esc_attr( $desc ) . '">';
								$output .= isset( $display_comments ) ? '<span class="img_likes"><i class="icon-heart"></i><span class="counter">' . $photo['comments'] . '</span></span>' : '';
								$output .= isset( $display_likes ) ? '<span class="img_commetn_count"><i class="icon-comment"></i><span class="counter">' . $photo['likes'] . '</span></span>' : '';
							$output .= isset( $link ) ? '</a>' : '';
						$output .= '</div>';

						$output .= isset( $display_time ) ? '<time datetime="' . esc_attr( date( 'Y-m-d\TH:i:sP' ), $photo['time'] ) . '" class="img_public_date"><i class="icon-calendar"></i> ' . date( $date_format, $photo['time'] ) . '</time>' : '';
						$output .= isset( $display_description ) && $desc ? '<span class="img_description"><i class="icon-pencil"></i> ' . $photo['description'] . '</span>' : '';

					$output .= '</figure></li>';
				}

				$output .= '</ul></div>';

				$output .= $button_text ? '<a href="//instagram.com/' . $user_name . '" class="btn btn-primary" title="Instagram" target="_blank">' . $button_text . '</a>' : '';
			$output .= $after_widget;

			echo $output;
		}

		/** @see WP_Widget::update */
		public function update( $new_instance, $old_instance ) {
			delete_transient( 'cherry_plugin_instagram_user_id' );
			delete_transient( 'cherry_plugin_instagram_photos' );
			return $new_instance;
		}

		/** @see WP_Widget::form */
		public function form($instance) {
			$defaults = array(
				'title'               => '',
				'endpoints'           => 'hashtag', // hashtag or self
				'user_name'           => '',
				'tag'                 => '',
				'client_id'           => '',
				'image_counter'       => '4',
				'image_size'          => 'thumbnail',
				'display_description' => 'on',
				'display_comments'    => 'on',
				'display_likes'       => 'on',
				'display_time'        => 'on',
				'link'                => 'on',
				'button_text'         => '',
			);

			extract( array_merge( $defaults, $instance ) );

			$form_field_type = array(
				'title' => array(
					'type' => 'text', 'class' => 'widefat', 'inline_style' => '',  'title' => __('Widget Title', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $title
					),
				'endpoints' => array(
					'type' => 'select', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Content type', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $endpoints, 'value_options' => array('self' => __('My Photos', CHERRY_PLUGIN_DOMAIN), 'hashtag' => __('Tagged photos', CHERRY_PLUGIN_DOMAIN))
					),
				'user_name' => array(
					'type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('User Name', CHERRY_PLUGIN_DOMAIN), 'description' => __('Widget will work only for users who have full rights opened in Instagram account.', CHERRY_PLUGIN_DOMAIN), 'value' => $user_name
					),
				'tag' => array(
					'type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('Hashtag', CHERRY_PLUGIN_DOMAIN), 'description' => __('Enter without #-symbol.', CHERRY_PLUGIN_DOMAIN), 'value' => $tag
					),
				'client_id' => array(
					'type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('Client ID', CHERRY_PLUGIN_DOMAIN), 'description' => __('Follow this <a href="https://instagram.com/developer/clients/manage/" target="_blank">link</a> and create the application. After that you will get your applications data where you will see the CLIENT ID.', CHERRY_PLUGIN_DOMAIN), 'value' => $client_id
					),
				'image_counter' => array(
					'type' => 'text', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Number of displayed images', CHERRY_PLUGIN_DOMAIN), 'description' => __('Max value: 20 images.', CHERRY_PLUGIN_DOMAIN), 'value' => $image_counter
					),
				'image_size' => array(
					'type' => 'select', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Image dimentions', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $image_size, 'value_options' => array('large' => __('Large', CHERRY_PLUGIN_DOMAIN), 'thumbnail' => __('Thumbnail', CHERRY_PLUGIN_DOMAIN))
					),
				'display_description' => array(
					'type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show  image description', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_description
					),
				'display_comments' => array(
					'type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show comments number', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_comments
					),
				'display_likes' => array(
					'type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show likes number', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_likes
					),
				'display_time' => array(
					'type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show image publication date', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_time
					),
				'link' => array(
					'type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Image URL', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $link
					),
				'button_text' => array(
					'type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('User account button text', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $button_text
					)
			);

			$output = '';

			foreach ($form_field_type as $key => $args) {
				$field_id          = esc_attr($this->get_field_id($key));
				$field_name        = esc_attr($this->get_field_name($key));
				$field_class       = $args['class'];
				$field_title       = $args['title'];
				$field_description = $args['description'];
				$field_value       = $args['value'];
				$field_options     = isset($args['value_options']) ? $args['value_options'] : array() ;
				$inline_style      = $args['inline_style'] ? 'style="'.$args['inline_style'].'"' : '' ;

				$output .= '<p>';
				switch ($args['type']) {
					case 'text':
						$output .= '<label for="'.$field_id.'">'.$field_title.': <input '.$inline_style.' class="'.$field_class.'" id="'.$field_id.'" name="'.$field_name.'" type="text" value="'.esc_attr($field_value).'" /></label>';
					break;
					case 'checkbox':
						$checked = isset($instance[$key]) ? 'checked' : '' ;
						$output .= '<label for="'.$field_id.'"><input value="on" '.$inline_style.' class="'.$field_class.'" id="'.$field_id.'" name="'.$field_name.'" type="checkbox" '.$checked.' />'.$field_title.'</label>';

					break;
					case 'select':
						$output .= '<label for="'.$field_id.'">'.$field_title.':</label>';
						$output .= '<select id="'.$field_id.'" name="'.$field_name.'" '.$inline_style.' class="'.$field_class.'">';
							if(!empty($field_options)){
								foreach ($field_options as $key_options => $value_options) {
									$selected = $key_options == $field_value ? ' selected' : '' ;
									$output .= '<option value="'.$key_options.'" '.$selected.'>'.$value_options.'</option>';
								}
							}
						$output .= '</select>';
					break;
				}
				$output .= '<br><small>'.$field_description.'</small>';
				$output .= '</p>';
			}
			echo $output;
		}

		function get_user_id( $user_name, $client_id ) {
			$cached = get_transient( 'cherry_plugin_instagram_user_id' );

			if ( false !== $cached ) {
				return $cached;
			}

			$url = add_query_arg(
				array( 'q' => esc_attr( $user_name ), 'client_id' => esc_attr( $client_id ) ),
				'https://api.instagram.com/v1/users/search/'
			);
			$response = wp_remote_get( $url );

			if ( is_wp_error( $response ) || empty( $response ) || $response ['response']['code'] != '200' ) {
				set_transient( 'cherry_plugin_instagram_user_id', false, HOUR_IN_SECONDS );
				return false;
			}

			$result  = json_decode( wp_remote_retrieve_body( $response ), true );
			$user_id = false;

			foreach ( $result['data'] as $key => $data ) {

				if ( $user_name != $data['username'] ) {
					continue;
				}

				$user_id = $data['id'];
			}

			set_transient( 'cherry_plugin_instagram_user_id', $user_id, HOUR_IN_SECONDS );

			return $user_id;
		}

		function get_photos( $data, $client_id, $img_counter, $config ) {
			$cached = get_transient( 'cherry_plugin_instagram_photos' );

			if ( false !== $cached ) {
				return $cached;
			}

			if ( 'self' == $config['endpoints'] ) {
				$old_url = 'https://api.instagram.com/v1/users/' . $data . '/media/recent/';
			} else {
				$old_url = 'https://api.instagram.com/v1/tags/' . $data . '/media/recent/';
			}

			$url = add_query_arg(
				array( 'client_id' => esc_attr( $client_id ) ),
				$old_url
			);

			$response = wp_remote_get( $url );

			if ( is_wp_error( $response ) || empty( $response ) || $response ['response']['code'] != '200' ) {
				set_transient( 'cherry_plugin_instagram_photos', false, HOUR_IN_SECONDS );
				return false;
			}

			$result  = json_decode( wp_remote_retrieve_body( $response ), true );
			$photos  = array();
			$counter = 1;

			foreach ( $result['data'] as $photo ) {

				if ( $counter > $img_counter ) {
					break;
				}

				if ( 'image' != $photo['type'] ) {
					continue;
				}

				$_photo = array();

				if ( in_array( 'link', $config ) )
					$_photo = array_merge( $_photo, array( 'link' => esc_url( $photo['link'] ) ) );

				if ( in_array( 'comments', $config ) )
					$_photo = array_merge( $_photo, array( 'comments' => absint( $photo['comments']['count'] ) ) );

				if ( in_array( 'likes', $config ) )
					$_photo = array_merge( $_photo, array( 'likes' => absint( $photo['likes']['count'] ) ) );

				if ( in_array( 'time', $config ) )
					$_photo = array_merge( $_photo, array( 'time' => sanitize_text_field( $photo['created_time'] ) ) );

				if ( in_array( 'description', $config ) )
					$_photo = array_merge( $_photo, array( 'description' => wp_trim_words( $photo['caption']['text'], 10 ) ) );

				if ( array_key_exists( 'thumb', $config ) ) {
					$size   = ( 'large' == $config['thumb'] ) ? 'standard_resolution' : 'thumbnail';
					$_photo = array_merge( $_photo, array( 'thumb' => $photo['images'][ $size ]['url'] ) );
				}

				if ( ! empty( $_photo ) ) {
					array_push( $photos, $_photo );
				}

				$counter++;
			}

			set_transient( 'cherry_plugin_instagram_photos', $photos, HOUR_IN_SECONDS );

			return $photos;
		}
	}
?>