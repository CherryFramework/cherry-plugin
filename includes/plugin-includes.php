<?php
//includ plugin functions
	include_once (CHERRY_PLUGIN_DIR . 'includes/plugin-functions.php');

//includ js and css files
	include_once (CHERRY_PLUGIN_DIR . 'includes/plugin-script.php');

//includ Aqua Resizer
	if(!function_exists('aq_resize')){
		include_once (CHERRY_PLUGIN_DIR . 'lib/php/aq_resizer.php');
	}
//Shortcodes
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/columns.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/shortcodes.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/posts-grid.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/posts-list.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/mini-posts-list.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/mini-posts-grid.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/alert.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/tabs.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/toggle.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/html.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/misc.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/service-box.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/post-cycle.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/carousel.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/carousel-owl.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/progressbar.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/banner.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/table.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/hero-unit.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/roundabout.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/categories.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/media.php');
	include_once (CHERRY_PLUGIN_DIR . 'includes/shortcodes/pricing-tables.php');