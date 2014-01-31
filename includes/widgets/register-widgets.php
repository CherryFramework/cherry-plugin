<?php
/**
 * Loads up all the widgets defined by this theme. Note that this function will not work for versions of WordPress 2.7 or lower
 *
 */
function load_my_widgets(){
	$widget_files = array(
		'my-recent-posts.php',
		'my-comment-widget.php',
		'my-social-widget.php',
		'my-posts-type-widget.php',
		'my-flickr-widget.php',
		'my-banners-widget.php',
		'my-vcard-widget.php',
		'my-facebook-widget.php',
		'my-post-cycle-widget.php'
	);
	foreach ($widget_files as $files) {
		$widget_dir = file_exists(CURRENT_THEME_DIR . '/includes/widgets/' . $files) ? CURRENT_THEME_DIR . '/includes/widgets/' . $files : CHERRY_PLUGIN_DIR . 'includes/widgets/' . $files ;
		include_once ($widget_dir);
	}

	register_widget('MY_PostWidget');
	register_widget('MY_CommentWidget');
	register_widget('My_SocialNetworksWidget');
	register_widget('MY_PostsTypeWidget');
	register_widget('MY_FlickrWidget');
	register_widget('Ad_125_125_Widget');
	register_widget('MY_Vcard_Widget');
	register_widget('My_Facebook_Widget');
	register_widget('MY_CycleWidget');
}
add_action('widgets_init', 'load_my_widgets');
?>