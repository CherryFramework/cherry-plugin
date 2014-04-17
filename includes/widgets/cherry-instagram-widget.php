<?php
	class Cherry_Instagram_Widget extends WP_Widget {
		/* constructor */
		public function Cherry_Instagram_Widget() {
			parent::WP_Widget(false, __('Cherry - Instagram', CHERRY_PLUGIN_DOMAIN), array('description' => __('Widget for popular social network Instagram', CHERRY_PLUGIN_DOMAIN)));
		}

		/** @see WP_Widget::widget */
		public function widget($args, $instance) {
			extract(array_merge($args , $instance));

			$button_text = apply_filters( 'cherry_text_translate', $instance['button_text'], $instance['title'] . ' button_text' );

			$user_name = strtolower(trim($user_name));
			$date_format = get_option('date_format');
			$img_list = $this->get_user_profile($user_name, $image_counter);
			$images_size_class = 'img_'.$image_size ;

			$output = $before_widget;
			$output .= $title ? $before_title . $title . $after_title : '' ;

			if(!empty($img_list)){
				$output .= '<div class="imgs_wrapper '.$images_size_class.'"><ul class="clearfix">';
				foreach ($img_list as $img_key => $img_value) {
					$class = ($img_key%2 == 0) ? 'odd' : 'even' ;
					$class .= isset($link) ? ' inst_link' : '' ;
					$class .= isset($img_list[$img_key+1]) ? '' : ' last_child' ;

					$output .= '<li class="'.$class.'"><figure>';
					$output .= '<div class="img_wrapp">';
					$output .= isset($link) ? '<a href="'.$img_value['link'].'" title="'.$img_value['description'].'" target="_blank" >' : '' ;
					$output .= '<img class="inst_img" src="'.$img_value[$image_size]['url'].'" alt="'.$img_value['description'].'">';
					$output .= isset($display_comments) ? '<span class="img_likes"><i class="icon-heart"></i><span class="counter">'.$img_value['likes'].'</span></span>' : '' ;
					$output .= isset($display_likes) ? '<span class="img_commetn_count"><i class="icon-comment"></i><span class="counter">'.$img_value['comments'].'</span></span>' : '' ;
					$output .= isset($link) ? '</a>' : '' ;
					$output .= '</div>';
					$output .= isset($display_time) ? '<span class="img_public_date"><i class="icon-calendar"></i> '.date($date_format, $img_value['time']).'</span>' : '' ;
					$output .= isset($display_description) && $img_value['description'] ? '<span class="img_description"><i class="icon-pencil"></i> '.$img_value['description'].'</span>' : '' ;
					$output .= '</figure></li>';
				}
				$output .= '</ul></div>';
			}
			$output .= $button_text ? '<a href="http://instagram.com/'.$user_name.'" class="btn btn-primary" title="Instagram" target="_blank">'.$button_text.'</a>' : '' ;
			$output .= $after_widget;

			echo $output;
		}

		/** @see WP_Widget::update */
		public function update( $new_instance, $old_instance ) {
			return $new_instance;
		}

		/** @see WP_Widget::form */
		public function form($instance) {
			$defaults = array(
				'title' => '',
				'user_name' => '',
				'image_counter' => '10',
				'image_size' => 'large',
				'display_description' => 'on',
				'display_comments' => 'on',
				'display_likes' => 'on',
				'display_time' => 'on',
				'link' => 'on',
				'button_text' => ''
			);
			extract(array_merge($defaults, $instance));

			$form_field_type = array(
				'title' => array('type' => 'text', 'class' => 'widefat', 'inline_style' => '',  'title' => __('Widget Title', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $title),
				'user_name' => array('type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('User Name', CHERRY_PLUGIN_DOMAIN), 'description' => 'Widget will work only for users who have full rights opened in Instagram account.', 'value' => $user_name),
				'image_counter' => array('type' => 'text', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Number of displayed images', CHERRY_PLUGIN_DOMAIN), 'description' => __('Max value: 20 images.', CHERRY_PLUGIN_DOMAIN), 'value' => $image_counter),
				'image_size' => array('type' => 'select', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Image dimentions', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $image_size, 'value_options' => array('large' => __('Large', CHERRY_PLUGIN_DOMAIN), 'thumbnail' => __('Thumbnail', CHERRY_PLUGIN_DOMAIN)) ),
				'display_description' => array('type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show  image description', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_description),
				'display_comments' => array('type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show comments number', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_comments),
				'display_likes' => array('type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show likes number', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_likes),
				'display_time' => array('type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Show image publication date', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $display_time),
				'link' => array('type' => 'checkbox', 'class' => '', 'inline_style' => '', 'title' => __('Image URL', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $link),
				'button_text' => array('type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('User account button text', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $button_text)
			);
			$output = '';

			foreach ($form_field_type as $key => $args) {
				$field_id = esc_attr($this->get_field_id($key));
				$field_name = esc_attr($this->get_field_name($key));
				$field_class = $args['class'];
				$field_title = $args['title'];
				$field_description = $args['description'];
				$field_value = $args['value'];
				$field_options = isset($args['value_options']) ? $args['value_options'] : array() ;
				$inline_style = $args['inline_style'] ? 'style="'.$args['inline_style'].'"' : '' ;

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
		function get_user_profile($user_name, $img_counter){
			$counter= 0;
			$response = wp_remote_get('http://instagram.com/'.$user_name);

			if(is_wp_error($response) || empty($response) || $response ['response']['code'] != "200"){
				return array();
			}

			$get_images_array = explode('window._sharedData = ', $response['body']);
			$get_images_array = explode(';</script>', $get_images_array[1]);
			$get_images_array = json_decode($get_images_array[0], TRUE);
			$get_images_array = $get_images_array['entry_data']['UserProfile'][0]['userMedia'];

			$images_array = array();
			foreach ($get_images_array as $image) {
				if($img_counter<=$counter){
					continue;
				}else{
					if ($image['type'] == 'image' && $image['user']['username'] == $user_name) {
						$images_array[] = array(
							'description' 	=> $image['caption']['text'],
							'link' 			=> $image['link'],
							'time'			=> $image['created_time'],
							'comments' 		=> $image['comments']['count'],
							'likes' 		=> $image['likes']['count'],
							'thumbnail' 	=> $image['images']['thumbnail'],
							'large' 		=> $image['images']['standard_resolution']
						);
						$counter++;
					}
				}
			}
			return $images_array;
		}
	}
?>