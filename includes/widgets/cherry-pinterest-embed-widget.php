<?php
$error = array();
	class Cherry_Pinterest_Embed_Widget extends WP_Widget {

		/* constructor */
		public function Cherry_Pinterest_Embed_Widget() {
			parent::__construct(false, __('Cherry - Pinterest Embed', CHERRY_PLUGIN_DOMAIN), array('description' => __('Widget for Pinterest embed content', CHERRY_PLUGIN_DOMAIN)));
		}

		/** @see WP_Widget::widget */
		public function widget($args, $instance) {
			extract(array_merge($args , $instance));

			$url = strtolower(esc_html($url));
			$lang = get_bloginfo('language');

			$output = $before_widget;
			$output .= $title ? $before_title . $title . $after_title : '' ;

			if($instance['error'] === '') {
				$output .= '<div id="pin-container">
								<script type="text/javascript">

									jQuery(document).ready(function() {
										var boardWidth, scaleWidth;

										jQuery(window).resize(
											function(){
												boardWidth = jQuery("#pin-container").width();
												scaleWidth = Math.floor(boardWidth/4.1111);
											}
										).trigger(\'resize\');
									});

								</script>
								<a data-pin-do="embedUser" href="'.$url.'"></a>
								<script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
							</div>';
			} else {
				$output .= '<div style="margin:20px 0 15px;padding:10px;background:#ff9b9b">'.__('There is some errors. Please check widgets page.', CHERRY_PLUGIN_DOMAIN).'</div>';
			}
			$output .= $after_widget;

			echo $output;
		}

		/** @see WP_Widget::update */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			// keep all of the fields passed from the new instance
			foreach ( $new_instance as $key => $value ){
				$instance[ $key ] = strip_tags($value);
			}

			if ( empty( $instance['url'] ) ) {
				$instance['error'] = __('Field is empty', CHERRY_PLUGIN_DOMAIN);
			} else {
				$instance['error'] = '';
			}

			return $instance;
		}

		/** @see WP_Widget::form */
		public function form($instance) {
			$defaults = array(
				'title' => '',
				'url' => '',
				'error' => ''
			);

			extract(array_merge($defaults, $instance));

			$form_field_type = array(
				'title' => array('type' => 'text', 'class' => 'widefat', 'inline_style' => '',  'title' => __('Widget Title', CHERRY_PLUGIN_DOMAIN), 'description' => '', 'value' => $title),
				'url' => array('type' => 'text', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Pinterest User URL', CHERRY_PLUGIN_DOMAIN), 'description' => __('Enter Pinterest User URL.', CHERRY_PLUGIN_DOMAIN), 'value' => $url),
			);
			$output = '';

			$title = esc_attr($title);

			if ($error) {
				$output .= '<div style="margin:20px 0 15px;padding:10px;background:#ff9b9b">'.$error.'</div>';
			}

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
						$output .= '<label for="'.$field_id.'">'.$field_title.': <input '.$inline_style.' class="'.$field_class.'" id="'.$field_id.'" name="'.$field_name.'" type="text" value="'.esc_attr($field_value).'"/></label>';
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
	}
?>