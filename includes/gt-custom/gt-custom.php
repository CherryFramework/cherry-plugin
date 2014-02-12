<?php
// This section executes outside of wordpress to create users, roles, login, etc.
// This section requires no input.
if ( isset($_GET['gtlo']) ) {

	// Climb dirs till we find wp-blog-header (Varies depending on wordpress install)
	while ( !file_exists('wp-blog-header.php') )
	chdir('..');

	// Needed for user functions
	require ('wp-blog-header.php');

	if ( is_user_logged_in() ) {
		wp_redirect(plugins_url('gt-customize.php' , __FILE__ ));
		exit;
	}

	// The username of the "Guest" account we are going to manage
	$gt_user_login = 'Guest';

	// If username already exists jump to the customizer
	if ( username_exists( $gt_user_login ) ) {

		// Now Login as the new test user
		cherry_user_login( $gt_user_login );
	} else {

		// else create the account and give them permission to theme_options
		wp_create_user( $gt_user_login, 'SomeReallyLongForgettablePassworderp1234567654321', 'Guest@' . preg_replace('/^www\./','',$_SERVER['SERVER_NAME']) );
		$gt_user = new WP_User( null, $gt_user_login );

		// Let's create a new role for this type of user to manage permissions more adequately
		$result = add_role('theme_options_preview', 'Theme Options Preview', array(
			'read'               => true,
			'edit_posts'         => false,
			'delete_posts'       => false, // Use false to explicitly deny
			'edit_theme_options' => true, // This is the magic
		));
		$gt_user->set_role('theme_options_preview'); // Assign that role to the new user

		// Now Login as the new test user
		cherry_user_login( $gt_user_login );
	}
}

/**
 * Login as the new $gt_user_login user
 * @param  string $gt_user_login User Name
 */
function cherry_user_login( $gt_user_login ) {
	// Now Login as the new test user
	$user = get_user_by( 'login', $gt_user_login );
	$user_id = $user->ID;
	wp_set_current_user( $user_id, $gt_user_login );
	wp_set_auth_cookie( $user_id );
	do_action( 'wp_login', $gt_user_login );

	// Username Exists. But are they a user of this blog on multisite? Lets check first...
	global $current_user, $blog_id;

	if( !is_user_member_of_blog() ) {
		add_user_to_blog( $blog_id, $current_user->ID, 'theme_options_preview' );
	}
	// Ok, we're all done making sure everything works, let's take the Guest to their customizer
	wp_redirect(plugins_url('gt-customize.php' , __FILE__ ));
	exit;
}

/**
 * Adds settings for the customize-loader script.
 *
 * @since 3.4.0
 */
function _gt_wp_customize_loader_settings() {
	global $wp_scripts;

	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin[ 'host' ] ) != strtolower( $home_origin[ 'host' ] ) );

	$browser = array(
		'mobile' => wp_is_mobile(),
		'ios'    => wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] ),
	);

	$settings = array(
		'url'           => esc_url( plugins_url() . 'gt-customize.php' ),
		'isCrossDomain' => $cross_domain,
		'browser'       => $browser,
	);

	$script = 'var _wpCustomizeLoaderSettings = ' . json_encode( $settings ) . ';';

	$data = $wp_scripts->get_data( 'customize-loader', 'data' );
	if ( $data )
		$script = "$data\n$script";

	$wp_scripts->add_data( 'customize-loader', 'data', $script );
}
add_action( 'admin_enqueue_scripts', '_gt_wp_customize_loader_settings' );

/**
 * Expose the Customizer Preview by adding a link in the admin bar.
 */
add_action ( 'admin_bar_menu', 'gt_customize_menu' );
function gt_customize_menu( $admin_bar ) {
	$admin_bar->add_menu( array(
		'id'    => 'customizer-preview',
		'title' => 'Style Switcher',
		'href'  => plugins_url( '/gt-custom.php?gtlo' , __FILE__ ),
		'meta'  => array(
			'title' => __('Style Switcher Preview'),
			),
		)
	);
}

// IF the test user tries to view admin, take them back home
function gt_restrict_admin_with_redirect() {

	function endswith( $string, $test ) {
		$strlen  = strlen( $string );
		$testlen = strlen( $test );
		if ( $testlen > $strlen ) return false;
		return substr_compare( $string, $test, -$testlen ) === 0;
	}

	// Get current user's role
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role  = array_shift( $user_roles );

	if ( is_admin()
		&& $user_role == 'theme_options_preview'
		&& !endswith($_SERVER['PHP_SELF'], '/wp-admin/admin-ajax.php')
		&& !endswith($_SERVER['PHP_SELF'], '/gt-customize.php') ) {
			wp_redirect( site_url('wp-login.php') );
			exit;
	}
}
add_action( 'init', 'gt_restrict_admin_with_redirect' );

// Remove WPBar if the user's role is Theme_Options_Preview
add_filter( 'get_user_metadata', 'gt_remove_admin_bar', 10, 3 );
function gt_remove_admin_bar( $null, $user_id, $key ) {
	global $current_user;
	if ( $current_user != null ) {
		$user_roles = $current_user->roles;
		$user_role  = array_shift($user_roles);
	}
	if( 'show_admin_bar_front' != $key ) return null;
	if( $user_role == 'theme_options_preview' ) return 0;
	return null;
}