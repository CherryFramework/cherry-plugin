<?php
/**
 * Loads up all the widgets defined by this theme. Note that this function will not work for versions of WordPress 2.7 or lower
 *
 */
function cherry_load_widgets() {
	$widget_files = array(
		'MY_PostWidget'                 => 'my-recent-posts.php',
		'MY_CommentWidget'              => 'my-comment-widget.php',
		'My_SocialNetworksWidget'       => 'my-social-widget.php',
		'MY_PostsTypeWidget'            => 'my-posts-type-widget.php',
		'MY_FlickrWidget'               => 'my-flickr-widget.php',
		'Ad_125_125_Widget'             => 'my-banners-widget.php',
		'MY_Vcard_Widget'               => 'my-vcard-widget.php',
		'My_Facebook_Widget'            => 'my-facebook-widget.php',
		'MY_CycleWidget'                => 'my-post-cycle-widget.php',
		'Cherry_Instagram_Widget'       => 'cherry-instagram-widget.php',
		'Cherry_Banner_Widget'          => 'cherry-banner-widget.php',
		'Cherry_Twitter_Embed_Widget'   => 'cherry-twitter-embed-widget.php',
		'Cherry_Pinterest_Embed_Widget' => 'cherry-pinterest-embed-widget.php',
	);
	foreach ( $widget_files as $class_name => $file_name ) {
		$widget_dir = file_exists( CURRENT_THEME_DIR . '/includes/widgets/' . $file_name ) ? CURRENT_THEME_DIR . '/includes/widgets/' . $file_name : CHERRY_PLUGIN_DIR . 'includes/widgets/' . $file_name ;
		include_once ( $widget_dir );
		if ( class_exists( $class_name ) ) {
			register_widget( $class_name );
		}
	}
}
add_action( 'widgets_init', 'cherry_load_widgets' );
?>