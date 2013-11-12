<?php
/**
 * Loads up all the widgets defined by this theme. Note that this function will not work for versions of WordPress 2.7 or lower
 *
 */
	function load_my_widgets() {
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-recent-posts.php');
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-comment-widget.php');
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-social-widget.php');
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-posts-type-widget.php');
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-flickr-widget.php');
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-banners-widget.php');
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-vcard-widget.php');
		include_once (CHERRY_PLUGIN_DIR . '/includes/widgets/my-facebook-widget.php');

		register_widget("MY_PostWidget");
		register_widget("MY_CommentWidget");
		register_widget("My_SocialNetworksWidget");
		register_widget("MY_PostsTypeWidget");
		register_widget("MY_FlickrWidget");
		register_widget("Ad_125_125_Widget");
		register_widget("MY_Vcard_Widget");
		register_widget("My_Facebook_Widget");
	}
	add_action("widgets_init", "load_my_widgets");
?>
