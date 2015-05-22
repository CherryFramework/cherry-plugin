<?php
/**
 *
 * Create widget showing manager
 *
 **/

// get theme name
function get_cherry_name() {
	if (function_exists('wp_get_theme')) {
		$theme = wp_get_theme();
		if ( $theme->exists() ) {
			$theme_name = $theme->Name;
		}
	} else {
		$theme_data = get_theme_data( get_template_directory() . '/style.css' );
		$theme_name = $theme_data['Name'];
	}
	$theme_name = preg_replace("/\W/", "_", strtolower($theme_name) );
	return $theme_name;
}

// load script and stylesheet only widgets page
add_action('admin_enqueue_scripts', 'widgets_scripts');
function widgets_scripts($hook) {
	$widget_page = 'widgets.php';
	if ( $widget_page != $hook) return;

	// widget rules JS
	wp_register_script('widget-rules-js', CHERRY_PLUGIN_URL.'admin/js/widget-rules.js', array('jquery'));
	wp_enqueue_script('widget-rules-js');
	// widget rules CSS
	wp_register_style('widget-rules-css', CHERRY_PLUGIN_URL.'admin/css/widget-rules.css');
	wp_enqueue_style('widget-rules-css');
}

// Clean up the variables
add_action('widgets_init', 'clean_rules');
function clean_rules() {
	$themename = 'cherry';

	// get option and style value
	$options_type = get_option($themename . '_widget_rules_type');
	$options      = get_option($themename . '_widget_rules');
	$custom_class = get_option($themename . '_widget_custom_class');
	$responsive   = get_option($themename . '_widget_responsive');
	$users        = get_option($themename  . '_widget_users');

	// if this options_type is set at first time
	if ( !is_array($options_type) ) {
		$options_type = array();
	}
	// if this option is set at first time
	if ( !is_array($options) ) {
		$options = array();
	}
	// if this custom_class is set at first time
	if ( !is_array($custom_class) ) {
		$custom_class = array();
	}
	// if this responsive is set at first time
	if ( !is_array($responsive) ) {
		$responsive = array();
	}
	// if this users is set at first time
	if ( !is_array($users) ) {
		$users = array();
	}

	// get all widgets names
	$all_widgets = array();
	$all_widgets_assoc = get_option('sidebars_widgets');
	// iterate throug the sidebar widgets settings to get all active widgets names
	foreach($all_widgets_assoc as $sidebar_name => $sidebar) {
		// remember about wp_inactive_widgets and array_version fields!
		if($sidebar_name != 'wp_inactive_widgets' && is_array($sidebar) && count($sidebar) > 0) {
			foreach($sidebar as $widget_name) {
				array_push($all_widgets, $widget_name);
			}
		}
	}
	// get the widget names from the exisitng settings
	$widget_names = array_keys($options_type);
	// check for the unexisting widgets
	foreach($widget_names as $widget_name) {
		// if widget doesn't exist - remove it from the options
		if(in_array($widget_name, $all_widgets) !== TRUE) {
			if(isset($options_type) && is_array($options_type) && isset($options_type[$widget_name])) {
				unset($options_type[$widget_name]);
			}

			if(isset($options) && is_array($options) && isset($options[$widget_name])) {
				unset($options[$widget_name]);
			}

			if(isset($custom_class) && is_array($custom_class) && isset($custom_class[$widget_name])) {
				unset($custom_class[$widget_name]);
			}

			if(isset($responsive) && is_array($responsive) && isset($responsive[$widget_name])) {
				unset($responsive[$widget_name]);
			}

			if(isset($users) && is_array($users) && isset($users[$widget_name])) {
				unset($users[$widget_name]);
			}
		}
	}
	// update the settings
	update_option($themename . '_widget_rules_type', $options_type);
	update_option($themename . '_widget_rules', $options);
	update_option($themename . '_widget_custom_class', $custom_class);
	update_option($themename . '_widget_responsive', $responsive);
	update_option($themename . '_widget_users', $users);
}

// define an additional operation when save the widget
add_filter( 'widget_update_callback', 'cherry_widget_update', 10, 4);

// definition of the additional operation
function cherry_widget_update($instance, $new_instance, $old_instance, $widget) {
	$themename = 'cherry';
	clean_rules();

	// check if param was set
	if ( isset( $_POST[$themename . '_widget_rules_' . $widget->id] ) ) {

		// get option and style value
		$options_type = get_option($themename . '_widget_rules_type');
		$options      = get_option($themename . '_widget_rules');
		$custom_class = get_option($themename . '_widget_custom_class');
		$responsive   = get_option($themename . '_widget_responsive');
		$users        = get_option($themename  . '_widget_users');

		// if this options_type is set at first time
		if ( !is_array($options_type) ) {
			$options_type = array();
		}
		// if this option is set at first time
		if ( !is_array($options) ) {
			$options = array();
		}
		// if this custom_class is set at first time
		if ( !is_array($custom_class) ) {
			$custom_class = array();
		}
		// if this responsive is set at first time
		if ( !is_array($responsive) ) {
			$responsive = array();
		}
		// if this users is set at first time
		if ( !is_array($users) ) {
			$users = array();
		}

		// set the new key in the array
		$options_type[$widget->id] = $_POST[$themename . '_widget_rules_type_' . $widget->id];
		$options[$widget->id]      = $_POST[$themename . '_widget_rules_' . $widget->id];
		$custom_class[$widget->id] = $_POST[$themename . '_widget_custom_class_' . $widget->id];
		$responsive[$widget->id]   = $_POST[$themename . '_widget_responsive_' . $widget->id];
		$users[$widget->id]        = $_POST[$themename . '_widget_users_' . $widget->id];

		// update the settings
		update_option($themename . '_widget_rules_type', $options_type);
		update_option($themename . '_widget_rules', $options);
		update_option($themename . '_widget_custom_class', $custom_class);
		update_option($themename . '_widget_responsive', $responsive);
		update_option($themename . '_widget_users', $users);

		// $instance = $old_instance;
		$instance[$themename . '_widget_rules_type_' . $widget->id]   = $options_type[$widget->id];
		$instance[$themename . '_widget_rules_' . $widget->id]        = $options[$widget->id];
		$instance[$themename . '_widget_custom_class_' . $widget->id] = $custom_class[$widget->id];
		$instance[$themename . '_widget_responsive_' . $widget->id]   = $responsive[$widget->id];
		$instance[$themename . '_widget_users_' . $widget->id]        = $users[$widget->id];
	}
	// return the widget instance
	return $instance;
}

add_filter('widget_form_callback', 'kc_widget_form_extend', 10, 2);
function kc_widget_form_extend( $instance, $widget ) {
	$themename = 'cherry';

	// get option and style value
	$options_type = get_option($themename . '_widget_rules_type');
	$options      = get_option($themename . '_widget_rules');
	$custom_class = get_option($themename . '_widget_custom_class');
	$responsive   = get_option($themename . '_widget_responsive');
	$users        = get_option($themename . '_widget_users');

	// if this option is set at first time
	if ( !is_array($options_type) ) {
		$options_type = array();
	}
	// if this option is set at first time
	if ( !is_array($options) ) {
		$options = array();
	}

	// if this responsive is set at first time
	if ( !is_array($responsive) ) {
		$responsive = array();
	}
	// if this users is set at first time
	if ( !is_array($users) ) {
		$users = array();
	}

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		$w_id = $widget->id_base;
		$w_num = abs($_POST['widget_number']);

		// save widget rules type
		if (isset($_POST[$themename . '_widget_rules_type_' . $w_id . '-' . $w_num])) {
			$options_type[$widget->id] = $_POST[$themename . '_widget_rules_type_' . $w_id . '-' . $w_num];
			$instance[$themename . '_widget_rules_type_' . $widget->id] = $options_type[$widget->id];
		}
		// save widget rules
		if (isset($_POST[$themename . '_widget_rules_' . $w_id . '-' . $w_num])) {
			$options[$widget->id] = $_POST[$themename . '_widget_rules_' . $w_id . '-' . $w_num];
			$instance[$themename . '_widget_rules_' . $widget->id] = $options[$widget->id];
		}
		// save widget style CSS
		if (isset($_POST[$themename . '_widget_custom_class_' . $w_id . '-' . $w_num])) {
			$custom_class[$widget->id] = $_POST[$themename . '_widget_custom_class_' . $w_id . '-' . $w_num];
			$instance[$themename . '_widget_custom_class_' . $widget->id] = $custom_class[$widget->id];
		}
		// save widget responsive
		if (isset($_POST[$themename . '_widget_responsive_' . $w_id . '-' . $w_num])) {
			$responsive[$widget->id] = $_POST[$themename . '_widget_responsive_' . $w_id . '-' . $w_num];
			$instance[$themename . '_widget_responsive_' . $widget->id] = $responsive[$widget->id];
		}
		// save widget users
		if (isset($_POST[$themename . '_widget_users_' . $w_id . '-' . $w_num])) {
			$users[$widget->id] = $_POST[$themename . '_widget_users_' . $w_id . '-' . $w_num];
			$instance[$themename . '_widget_users_' . $widget->id] = $users[$widget->id];
		}

		// update the settings
		update_option($themename . '_widget_rules_type', $options_type);
		update_option($themename . '_widget_rules', $options);
		update_option($themename . '_widget_custom_class', $custom_class);
		update_option($themename . '_widget_responsive', $responsive);
		update_option($themename . '_widget_users', $users);
	}
	return $instance;
}

// Hide the widget if necessary
add_filter( 'widget_display_callback', 'maybe_hide_widget', 10, 3 );
function maybe_hide_widget( $instance, $widget_object, $args ) {

	if ( ! check_widget_visibility( $args['widget_id'] ) )
		return false;
	return $instance;
}

// Add custom widget class
add_filter( 'dynamic_sidebar_params', 'cherry_dynamic_sidebar_params' );
function cherry_dynamic_sidebar_params( $params ) {
	global $wp_registered_widgets;

	$themename = 'cherry';
	$widget_id  = $params[0]['widget_id'];

	// get option and style value
	$responsive   = get_option($themename . '_widget_responsive');
	$custom_class = get_option($themename . '_widget_custom_class');

	if ( !isset($responsive[$widget_id]) && !isset($custom_class[$widget_id]) )
		return $params;

	$haystack_str = htmlspecialchars(stripslashes($params[0]['before_widget']), ENT_QUOTES);
	$params[0]['before_widget'] = add_widget_class_attr($haystack_str);

	if ( isset($custom_class[$widget_id]) && !empty($custom_class[$widget_id]) )
		$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$custom_class[$widget_id]} ", $params[0]['before_widget'], 1 );

	if ( isset($responsive[$widget_id]) && !empty($responsive[$widget_id]) )
	$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$responsive[$widget_id]} ", $params[0]['before_widget'], 1 );

	return $params;
}

// function to add (if necessary) class attribute to the widget holder
function add_widget_class_attr($haystack_str) {
	$needle_str   = 'class=';
	$pos          = strpos($haystack_str, $needle_str);

	if ( $pos === false ) {
		$search_str   = htmlspecialchars('>');
		$replace_str  = htmlspecialchars(' class="">');
		$haystack_str = str_replace($search_str, $replace_str, $haystack_str);
	}
	return htmlspecialchars_decode($haystack_str);
}


add_action( 'sidebar_admin_setup', 'cherry_add_widget_control');
function cherry_add_widget_control() {
	global $wp_registered_widgets;
	global $wp_registered_widget_controls;

	$themename = 'cherry';

	// get option value
	$options_type = get_option($themename . '_widget_rules_type');
	$options      = get_option($themename . '_widget_rules');
	$custom_class = get_option($themename . '_widget_custom_class');
	$responsive   = get_option($themename . '_widget_responsive');
	$users        = get_option($themename . '_widget_users');

	// if this option is set at first time
	if( !is_array($options) ) {
		$options = array();
	}
	// if this style CSS is set at first time
	if( !is_array($custom_class) ) {
		$custom_class = array();
	}
	// if this responsive is set at first time
	if( !is_array($responsive) ) {
		$responsive = array();
	}
	// if this users is set at first time
	if( !is_array($users) ) {
		$users = array();
	}
	// AJAX updates
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id ) {
			// save widget rules type
			if (isset($_POST[$themename . '_widget_rules_type_' . $widget_id])) {
				$options_type[$widget_id] = $_POST[$themename . '_widget_rules_type_' . $widget_id];
			}
			// save widget rules
			if (isset($_POST[$themename . '_widget_rules_' . $widget_id])) {
				$options[$widget_id] = $_POST[$themename . '_widget_rules_' . $widget_id];
			}
			// save widget style CSS
			if (isset($_POST[$themename . '_widget_custom_class_' . $widget_id])) {
				$custom_class[$widget_id] = $_POST[$themename . '_widget_custom_class_' . $widget_id];
			}
			// save widget responsive
			if (isset($_POST[$themename . '_widget_responsive_' . $widget_id])) {
				$responsive[$widget_id] = $_POST[$themename . '_widget_responsive_' . $widget_id];
			}
			// save widget users
			if (isset($_POST[$themename . '_widget_users_' . $widget_id])) {
				$users[$widget_id] = $_POST[$themename . '_widget_users_' . $widget_id];
			}
		}
	}
	// save the widget id
	foreach ( $wp_registered_widgets as $id => $widget ) {
		if ( isset($wp_registered_widget_controls[$id]) ) {
			// save the widget id
			$wp_registered_widget_controls[$id]['params'][0]['widget_id'] = $id;
			// do the redirection
			$wp_registered_widget_controls[$id]['callback_redir'] = $wp_registered_widget_controls[$id]['callback'];
			$wp_registered_widget_controls[$id]['callback'] = 'cherry_widget_control';
		}
	}
}

// function to add the widget control
function cherry_widget_control() {
	// get the access to the registered widget controls
	global $wp_registered_widget_controls;

	$themename = 'cherry';

	// get the widget parameters
	$params = func_get_args();

	// find the widget ID
	$id = $params[0]['widget_id'];
	$unique_id = $id . '-' . rand(10000000, 99999999);

	// get option value
	$options_type = get_option($themename . '_widget_rules_type');
	$options      = get_option($themename . '_widget_rules');
	$custom_class = get_option($themename . '_widget_custom_class');
	$responsive   = get_option($themename . '_widget_responsive');
	$users        = get_option($themename . '_widget_users');

	// if this option is set at first time
	if ( !is_array($options_type) ) {
		$options_type = array();
	}
	// if this option is set at first time
	if ( !is_array($options) ) {
		$options = array();
	}
	// if this custom_class is set at first time
	if ( !is_array($custom_class) ) {
		$custom_class = array();
	}
	// if this responsive is set at first time
	if ( !is_array($responsive) ) {
		$responsive = array();
	}
	// if this users is set at first time
	if ( !is_array($users) ) {
		$users = array();
	}
	// get the widget form callback
	$callback = $wp_registered_widget_controls[$id]['callback_redir'];
	// if the callbac exist - run it with the widget parameters
	if (isset($callback) && is_callable($callback)) {
		call_user_func_array($callback, $params);
	}

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

		$w_id = $_POST['id_base'];
		$w_num = abs($_POST['widget_number']);

		// save widget rules type
		if (isset($_POST[$themename . '_widget_rules_type_' . $w_id . '-' . $w_num])) {
			$options_type[$id] = $_POST[$themename . '_widget_rules_type_' . $w_id . '-' . $w_num];
		}
		// save widget rules
		if (isset($_POST[$themename . '_widget_rules_' . $w_id . '-' . $w_num])) {
			$options[$id] = $_POST[$themename . '_widget_rules_' . $w_id . '-' . $w_num];
		}
		// save widget style CSS
		if (isset($_POST[$themename . '_widget_custom_class_' . $w_id . '-' . $w_num])) {
			$custom_class[$id] = $_POST[$themename . '_widget_custom_class_' . $w_id . '-' . $w_num];
		}
		// save widget responsive
		if (isset($_POST[$themename . '_widget_responsive_' . $w_id . '-' . $w_num])) {
			$responsive[$id] = $_POST[$themename . '_widget_responsive_' . $w_id . '-' . $w_num];
		}
		// save widget users
		if (isset($_POST[$themename . '_widget_users_' . $w_id . '-' . $w_num])) {
			$users[$id] = $_POST[$themename . '_widget_users_' . $w_id . '-' . $w_num];
		}

		// update the settings
		update_option($themename . '_widget_rules_type', $options_type);
		update_option($themename . '_widget_rules', $options);
		update_option($themename . '_widget_custom_class', $custom_class);
		update_option($themename . '_widget_responsive', $responsive);
		update_option($themename . '_widget_users', $users);
	}

	// value of the option
	$value_type     = !empty($options_type[$id]) ? htmlspecialchars(stripslashes($options_type[$id]),ENT_QUOTES) : '';
	$value          = !empty($options[$id]) ? htmlspecialchars(stripslashes($options[$id]),ENT_QUOTES) : '';
	$c_class        = !empty($custom_class[$id]) ? htmlspecialchars(stripslashes($custom_class[$id]),ENT_QUOTES) : '';
	$responsiveMode = !empty($responsive[$id]) ? htmlspecialchars(stripslashes($responsive[$id]),ENT_QUOTES) : '';
	$usersMode      = !empty($users[$id]) ? htmlspecialchars(stripslashes($users[$id]),ENT_QUOTES) : '';

	//
	// output the custom CSS class field
	echo '<p class="custom_class"><label for="' . $themename . '_widget_custom_class_'.$id.'">'.__('Custom CSS class', CHERRY_PLUGIN_DOMAIN).': <input type="text" class="widefat" name="' . $themename . '_widget_custom_class_'.$id.'"  id="' . $themename . '_widget_custom_class_'.$id.'" value="'.$c_class.'" /></label></p>';
	echo '
	<a class="gk_widget_rules_btn button">'.__('Visibility', CHERRY_PLUGIN_DOMAIN).'</a>
	<div class="gk_widget_rules_wrapper'.((isset($_COOKIE['gk_last_opened_widget_rules_wrap']) && $_COOKIE['gk_last_opened_widget_rules_wrap'] == 'gk_widget_rules_form_'.$id) ? ' active' : '').'" data-id="gk_widget_rules_form_'.$id.'">
		<p>
			<label for="' . $themename . '_widget_rules_'.$id.'">'.__('Visible at', CHERRY_PLUGIN_DOMAIN).': </label>
			<select name="' . $themename . '_widget_rules_type_'.$id.'" id="' . $themename . '_widget_rules_type_'.$id.'" class="widefat gk_widget_rules_select">
				<option value="all"'.(($value_type != "include" && $value_type != 'exclude') ? " selected=\"selected\"":"").'>'.__('All pages', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="exclude"'.(($value_type == "exclude") ? " selected=\"selected\"":"").'>'.__('All pages expecting', CHERRY_PLUGIN_DOMAIN).':</option>
				<option value="include"'.(($value_type == "include") ? " selected=\"selected\"":"").'>'.__('No pages expecting', CHERRY_PLUGIN_DOMAIN).':</option>
			</select>
		</p>
		<fieldset class="gk_widget_rules_form" id="gk_widget_rules_form_'.$unique_id.'" data-id="gk_widget_rules_form_'.$id.'">
			<legend>'.__('Select page to add', CHERRY_PLUGIN_DOMAIN).'</legend>
			<select class="widefat gk_widget_rules_form_select">
				<option value="homepage">'.__('Homepage', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="blog">'.__('Blog', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="page:">'.__('Page', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="post:">'.__('Post', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="category:">'.__('Archive', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="tag:">'.__('Tag', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="archive">'.__('Archive', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="author:">'.__('Author', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="search">'.__('Search page', CHERRY_PLUGIN_DOMAIN).'</option>
				<option value="page404">'.__('404 page', CHERRY_PLUGIN_DOMAIN).'</option>
			</select>
			<p><label>'.__('Page ID/Title/slug', CHERRY_PLUGIN_DOMAIN).':<input type="text" class="gk_widget_rules_form_input_page" /></label></p>
			<p><label>'.__('Post ID/Title/slug', CHERRY_PLUGIN_DOMAIN).':<input type="text" class="gk_widget_rules_form_input_post" /></label></p>
			<p><label>'.__('Category ID/Name/slug', CHERRY_PLUGIN_DOMAIN).':<input type="text" class="gk_widget_rules_form_input_category" /></label></p>
			<p><label>'.__('Tag ID/Name', CHERRY_PLUGIN_DOMAIN).':<input type="text" class="gk_widget_rules_form_input_tag" /></label></p>
			<p><label>'.__('Author', CHERRY_PLUGIN_DOMAIN).':<input type="text" class="gk_widget_rules_form_input_author" /></label></p>
			<p><button class="gk_widget_rules_btn button-secondary">'.__('Add page', CHERRY_PLUGIN_DOMAIN).'</button></p>
			<input type="text" name="' . $themename . '_widget_rules_'.$id.'"  id="' . $themename . '_widget_rules_'.$id.'" value="'.$value.'" class="gk_widget_rules_output" />
			<fieldset class="gk_widget_rules_pages">
				<legend>'.__('Selected pages', CHERRY_PLUGIN_DOMAIN).'</legend>
				<span class="gk_widget_rules_nopages">'.__('No pages', CHERRY_PLUGIN_DOMAIN).'</span>
				<div></div>
			</fieldset>
		</fieldset>
		<script type="text/javascript">gk_widget_control_init(\'#gk_widget_rules_form_'.$unique_id.'\');</script>';
		// create the list of suffixes
	cherry_widget_control_styles_list($params[0]['widget_id'], $id, $responsiveMode, $usersMode);
}

function cherry_widget_control_styles_list($widget_name, $id, $value2, $value3) {
	$themename = 'cherry';

	echo '<div>';
	// prepare an array of options
	$items = array('<option value="" selected="selected">'.__('None', CHERRY_PLUGIN_DOMAIN).'</option>');

	// prepare the responsive select
	$items = array(
		'<option value="visible-all-devices"'.((!$value2 || $value2 == 'visible-all-devices') ? ' selected="selected"' : '').'>'.__('All devices', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="visible-desktop"'.(($value2 == 'visible-desktop') ? ' selected="selected"' : '').'>'.__('Desktops', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="visible-tablet"'.(($value2 == 'visible-tablet') ? ' selected="selected"' : '').'>'.__('Tablets', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="visible-phone"'.(($value2 == 'visible-phone') ? ' selected="selected"' : '').'>'.__('Phones', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="hidden-phone"'.(($value2 == 'hidden-phone') ? ' selected="selected"' : '').'>'.__('Desktops/Tablets', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="hidden-desktop"'.(($value2 == 'hidden-desktop') ? ' selected="selected"' : '').'>'.__('Tablets/Phones', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="hidden-tablet"'.(($value2 == 'hidden-tablet') ? ' selected="selected"' : '').'>'.__('Desktops/Phones', CHERRY_PLUGIN_DOMAIN).'</option>'
	);
	// output the responsive select
	echo '<p class="device_visible"><label for="' . $themename . '_widget_responsive_'.$id.'">'.__('Visible on', CHERRY_PLUGIN_DOMAIN).': <select name="' . $themename . '_widget_responsive_'.$id.'"  id="' . $themename . '_widget_responsive_'.$id.'" class="widefat">';
	//
	foreach($items as $item) {
		echo $item;
	}
	//
	echo '</select></label></p>';
	// output the user groups select
	$items = array(
		'<option value="all"'.(($value3 == null || !$value3 || $value3 == 'all') ? ' selected="selected"' : '').'>'.__('All users', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="guests"'.(($value3 == 'guests') ? ' selected="selected"' : '').'>'.__('Only guests', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="registered"'.(($value3 == 'registered') ? ' selected="selected"' : '').'>'.__('Only registered users', CHERRY_PLUGIN_DOMAIN).'</option>',
		'<option value="administrator"'.(($value3 == 'administrator') ? ' selected="selected"' : '').'>'.__('Only administrator', CHERRY_PLUGIN_DOMAIN).'</option>'
	);
	//
	echo '<p class="users_visible"><label for="' . $themename . '_widget_users_'.$id.'">'.__('Visible for', CHERRY_PLUGIN_DOMAIN).': <select name="' . $themename . '_widget_users_'.$id.'"  id="' . $themename . '_widget_users_'.$id.'" class="widefat">';
	//
	foreach($items as $item)
		echo $item;
	//
	echo '</select></label></p>';
	// // output the custom CSS class field
	// echo '<p class="custom_class"><label for="' . $themename . '_widget_custom_class_'.$id.'">'.theme_locals("custom_css_class").': <input type="text" class="widefat" name="' . $themename . '_widget_custom_class_'.$id.'"  id="' . $themename . '_widget_custom_class_'.$id.'" value="'.$value4.'" /></label></p>';

	echo '</div><!--.gk_widget_rules_wrapper-->';
	//
	echo '</div>';
	echo '<hr />';
}

function check_widget_visibility($id) {
	// get access to registered widgets
	global $wp_registered_widgets;
	// sidebar flag
	$sidebar_flag = false;

	$themename = 'cherry';

	// get the widget showing rules
	$options_type = get_option($themename . '_widget_rules_type');
	$options      = get_option($themename . '_widget_rules');
	$users        = get_option($themename . '_widget_users');

	// if widget doesn't exists - skip this iteration
	if ( !isset($wp_registered_widgets[$id]) ) continue;

	// check the widget rules
	$conditional_result = false;

	// create conditional function based on rules
	if ( isset($options[$id]) && $options[$id] != '' ) {
		// create function
		$conditional_function = create_function('', 'return '.cherry_condition($options_type[$id], $options[$id], $users[$id]).';');
		// generate the result of function
		$conditional_result = $conditional_function();
	} else if ( isset($users[$id]) && $users[$id] != '' ) {
		// create function
		$conditional_function = create_function('', 'return '.cherry_condition($options_type[$id], $options[$id], $users[$id]).';');
		// generate the result of function
		$conditional_result = $conditional_function();
	}
	// if condition for widget isn't set or is TRUE
	if ( (!isset($options[$id]) || $options[$id] == '') && (!isset($users[$id]) || $users[$id] == '') || $conditional_result === TRUE ) {
		// return TRUE, because at lease one widget exists in the specific sidebar
		$sidebar_flag = true;
	}
	// set the state of the widget
	$wp_registered_widgets[$id]['cherrystate'] = $conditional_result;

	return $sidebar_flag;
}

/**
 *
 * Function used to create conditional string
 *
 * @param mode - mode of the condition - exclude, all, include
 * @param input - input data separated by commas, look into example inside the function
 * @param users - the value of the user access
 *
 * @return HTML output
 *
 **/
function cherry_condition($mode, $input, $users) {
	// Example input:
	// homepage,page:12,post:10,category:test,tag:test
	$mode_output = '';

	if ( !empty($input) ) :
		$mode_output = ' (';
		if ( $mode == 'all' ) {
			$mode_output = '';
		// } else if (( $mode == 'exclude' ) && ( $users == 'all')) {
		} else if (( $mode == 'exclude' )) {
			$mode_output = ' !(';
		}

		if($mode != 'all') {
			$input = substr($input, 1);
			$input = explode(',', $input);

			for($i = 0; $i < count($input); $i++) {
				if($i > 0) {
					$mode_output .= '||';
				}

				if(stripos($input[$i], 'homepage') !== FALSE) {
					$mode_output .= ' is_front_page() ';
				} else if(stripos($input[$i], 'blog') !== FALSE) {
					$mode_output .= ' is_home() ';
				} else if(stripos($input[$i], 'page:') !== FALSE) {
					$mode_output .= ' is_page(\'' . substr($input[$i], 5) . '\') ';
				} else if(stripos($input[$i], 'post:') !== FALSE) {
					$mode_output .= ' is_single(\'' . substr($input[$i], 5) . '\') ';
				} else if(stripos($input[$i], 'category:') !== FALSE) {
					$mode_output .= ' (is_category(\'' . substr($input[$i], 9) . '\') || (in_category(\'' . substr($input[$i], 9) . '\') && is_single())) ';
				} else if(stripos($input[$i], 'tag:') !== FALSE) {
					$mode_output .= ' (is_tag(\'' . substr($input[$i], 4) . '\') || (has_tag(\'' . substr($input[$i], 4) . '\') && is_single())) ';
				} else if(stripos($input[$i], 'archive') !== FALSE) {
					$mode_output .= ' is_archive() ';
				} else if(stripos($input[$i], 'author:') !== FALSE) {
					$mode_output .= ' (is_author(\'' . substr($input[$i], 7) . '\') && is_single()) ';
				} else if(stripos($input[$i], 'search') !== FALSE) {
					$mode_output .= ' is_search() ';
				} else if(stripos($input[$i], 'page404') !== FALSE) {
					$mode_output .= ' is_404() ';
				}
			}
			$mode_output .= ')';
		}
	endif;

	$users_output = '';
	if ( $users != 'all' ) {
		if($users == 'guests') {
			$users_output .= ' !is_user_logged_in() ';
		} else if($users == 'registered') {
			$users_output .= ' is_user_logged_in() ';
		} else if($users == 'administrator') {
			$users_output .= ' current_user_can(\'manage_options\') ';
		}
	}

	if ( !empty($mode_output) && !empty($users_output) ) {
		$operator = ' && ';
	} else {
		$operator = '';
	}

	$output = $mode_output . (($users_output == '') ? '' : $operator . $users_output );

	if ( $output == '' )
		$output = ' TRUE';

	return $output;
} ?>