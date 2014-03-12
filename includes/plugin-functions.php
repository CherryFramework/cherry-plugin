<?php
//------------------------------------------------------
// The excerpt based on words
//------------------------------------------------------
if ( !function_exists('my_string_limit_words') ) {
	function my_string_limit_words($string, $word_limit) {
		if ( empty($string{0}) )
			return;

		$words = explode( ' ', $string, ( $word_limit + 1 ) );
		if ( count( $words ) > $word_limit ) {
			array_pop($words);
			return implode(' ', $words) . '&hellip;';
		} else
			return $string;
	}
}
//------------------------------------------------------
// Remove invalid tags
//------------------------------------------------------
if(!function_exists('remove_invalid_tags')){
	function remove_invalid_tags($str, $tags) {
		foreach($tags as $tag) {
			$str = preg_replace('#^<\/'.$tag.'>|<'.$tag.'>$#', '', trim($str));
		}
		return $str;
	}
}
//------------------------------------------------------
//  Get team social networks
//------------------------------------------------------
if(!function_exists('cherry_get_post_networks')){
	function cherry_get_post_networks($args = array()){
		global $post;
		extract(
			wp_parse_args(
				$args,
				array(
					'post_id' => get_the_ID(),
					'class' => 'post_networks',
					'before_title' => '<h4>',
					'after_title' => '</h4>',
					'display_title' => true,
					'output_type' => 'echo'
				)
			)
		);
		$networks_array = explode(" ", get_option('fields_id_value'.$post_id, ''));

		if($networks_array[0]!=''){
			$count = 0;
			$network_title = get_post_meta($post_id, 'network_title', true);

			$output = '<div class="'.$class.'">';
			$output .= $network_title && $display_title ? $before_title.$network_title.$after_title : '';
			$output .= '<ul class="clearfix unstyled">';
			foreach ($networks_array as $networks_id) {
				$network_array = explode(";", get_option('network_'.$post_id.'_'.$networks_id, array('','','')));
				$output .= '<li class="network_'.$count.'">';
				$output .= $network_array[2] ? '<a href="'.$network_array[2].'" title="'.$network_array[1].'">' : '' ;
				$output .= $network_array[0] ? '<span class="'.$network_array[0].'"></span>' :'';
				$output .= $network_array[1] ? '<span class="network_title">'.$network_array[1].'</span>' : '' ;
				$output .= $network_array[2] ? '</a>' : '' ;
				$output .= '</li>';
				++$count;
			}
			$output .= '</ul></div>';
			if($output_type == 'echo'){
				echo $output;
			}else{
				return $output;
			}
		}
	}
}
//------------------------------------------------------
//  Get team social networks
//------------------------------------------------------
if(!function_exists('gener_random')){
	function gener_random($length){
		srand((double)microtime()*1000000 );
		$random_id = "";
		$char_list = "abcdefghijklmnopqrstuvwxyz";
		for( $i = 0; $i < $length; $i++ ) {
			$random_id .= substr($char_list,(rand()%(strlen($char_list))), 1);
		}
		return $random_id;
	}
}
//------------------------------------------------------
// Remove Empty Paragraphs
//------------------------------------------------------
if ( !function_exists('shortcode_empty_paragraph_fix') ) {

	add_filter('the_content', 'shortcode_empty_paragraph_fix');
	function shortcode_empty_paragraph_fix($content) {
		$array = array(
				'<p>['    => '[',
				']</p>'   => ']',
				']<br />' => ']'
		);
		$content = strtr($content, $array);
		return $content;
	}
}