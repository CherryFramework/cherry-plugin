<?php
/**
 * Customize Controls
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

define( 'IFRAME_REQUEST', true );

require_once( dirname(__FILE__) . '/../../../../../wp-admin/admin.php' );

//TODO: Move this to plugin activation or an interface to create a specific username

if ( ! current_user_can( 'edit_theme_options' ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

wp_reset_vars( array( 'url', 'return' ) );
$url = urldecode( $url );
$url = wp_validate_redirect( $url, home_url( '/' ) );
if ( $return )
	$return = wp_validate_redirect( urldecode( $return ) );
if ( ! $return )
	$return = $url;

global $wp_scripts, $wp_customize;

$registered = $wp_scripts->registered;
$wp_scripts = new WP_Scripts;
$wp_scripts->registered = $registered;

add_action( 'customize_controls_print_scripts',        'print_head_scripts', 20 );
add_action( 'customize_controls_print_footer_scripts', '_wp_footer_scripts'     );
add_action( 'customize_controls_print_styles',         'print_admin_styles', 20 );

/**
 * Fires when Customizer controls are initialized, before scripts are enqueued.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_init' );

wp_enqueue_script( 'customize-controls' );
wp_enqueue_style( 'customize-controls' );

wp_enqueue_script( 'accordion' );

/**
 * Custom action for enqueue custom stylesheet
 */
do_action( 'cherry_customize_enqueue_styles' );

/**
 * Enqueue Customizer control scripts.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_enqueue_scripts' );

// Let's roll.
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

//wp_user_settings();
_wp_admin_html_begin();

$body_class = 'wp-core-ui wp-customizer js';

if ( wp_is_mobile() ) :
	$body_class .= ' mobile';

	?><meta name="viewport" id="viewport-meta" content="width=device-width, initial-scale=0.8, minimum-scale=0.5, maximum-scale=1.2"><?php
endif;

$is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );

if ( $is_ios )
	$body_class .= ' ios';

if ( is_rtl() )
	$body_class .= ' rtl';
$body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

$admin_title = sprintf( __( '%1$s &#8212; WordPress' ), strip_tags( sprintf( __( 'Customize %s' ), $wp_customize->theme()->display('Name') ) ) );
?><title><?php echo $admin_title; ?></title>

<script type="text/javascript">
var ajaxurl = '<?php echo admin_url( "admin-ajax.php", "relative" ); ?>';
</script>

<?php
/**
 * Print Customizer control styles.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_print_styles' );

/**
 * Print Customizer control scripts.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_print_scripts' );
?>
</head>
<body class="<?php echo esc_attr( $body_class ); ?>">
<div class="wp-full-overlay expanded">
	<form id="customize-controls" class="wrap wp-full-overlay-sidebar">

		<div id="customize-header-actions" class="wp-full-overlay-header">
			<?php _e('Style Switcher', CHERRY_PLUGIN_DOMAIN); ?>
			<a href="#" class="collapse-sidebar button-secondary" title="<?php esc_attr_e('Collapse Sidebar'); ?>">
				<span class="collapse-sidebar-arrow"></span>
			</a>
		</div>

		<div class="wp-full-overlay-sidebar-content accordion-container" tabindex="-1">
			<div id="customize-theme-controls"><ul>
				<?php
				foreach ( $wp_customize->sections() as $section ) {
					if ( strpos( $section->id, 'style_switcher' ) !== FALSE ) {
						$section->maybe_render();
					}
				} ?>
			</ul></div>
		</div>
	</form>
	<div id="customize-preview" class="wp-full-overlay-main"></div>
	<?php

	/**
	 * Print Customizer control scripts in the footer.
	 *
	 * @since 3.4.0
	 */
	do_action( 'customize_controls_print_footer_scripts' );

	// If the frontend and the admin are served from the same domain, load the
	// preview over ssl if the customizer is being loaded over ssl. This avoids
	// insecure content warnings. This is not attempted if the admin and frontend
	// are on different domains to avoid the case where the frontend doesn't have
	// ssl certs. Domain mapping plugins can allow other urls in these conditions
	// using the customize_allowed_urls filter.

	$allowed_urls = array( home_url('/') );
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin[ 'host' ] ) != strtolower( $home_origin[ 'host' ] ) );

	if ( is_ssl() && ! $cross_domain )
		$allowed_urls[] = home_url( '/', 'https' );

	/**
	 * Filter the list of URLs allowed to be clicked and followed in the Customizer preview.
	 *
	 * @since 3.4.0
	 *
	 * @param array $allowed_urls An array of allowed URLs.
	 */
	$allowed_urls = array_unique( apply_filters( 'customize_allowed_urls', $allowed_urls ) );

	$fallback_url = add_query_arg( array(
		'preview'        => 1,
		'template'       => $wp_customize->get_template(),
		'stylesheet'     => $wp_customize->get_stylesheet(),
		'preview_iframe' => true,
		'TB_iframe'      => 'true'
	), home_url( '/' ) );

	$login_url = add_query_arg( array(
		'interim-login' => 1,
		'customize-login' => 1
	), wp_login_url() );

	$settings = array(
		'theme'    => array(
			'stylesheet' => $wp_customize->get_stylesheet(),
			'active'     => $wp_customize->is_theme_active(),
		),
		'url'      => array(
			'preview'       => esc_url( $url ? $url : home_url( '/' ) ),
			'parent'        => esc_url( admin_url() ),
			'activated'     => admin_url( 'themes.php?activated=true&previewed' ),
			'ajax'          => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
			'allowed'       => array_map( 'esc_url', $allowed_urls ),
			'isCrossDomain' => $cross_domain,
			'fallback'      => $fallback_url,
			'home'          => esc_url( home_url( '/' ) ),
			'login'         => $login_url,
		),
		'browser'  => array(
			'mobile' => wp_is_mobile(),
			'ios'    => $is_ios,
		),
		'settings' => array(),
		'controls' => array(),
		'nonce'    => array(
			// Removing the orginal "Save" part of the customizer
			// 'save'    => wp_create_nonce( 'save-customize_' . $wp_customize->get_stylesheet() ),
			'preview' => wp_create_nonce( 'preview-customize_' . $wp_customize->get_stylesheet() )
		),
	);

	foreach ( $wp_customize->settings() as $id => $setting ) {
		$settings['settings'][ $id ] = array(
			'value'     => $setting->js_value(),
			'transport' => $setting->transport,
		);
	}

	foreach ( $wp_customize->controls() as $id => $control ) {
		$control->to_json();
		$settings['controls'][ $id ] = $control->json;
	}

	?>
	<script type="text/javascript">
		var _wpCustomizeSettings = <?php echo json_encode( $settings ); ?>;
	</script>
</div>
</body>
</html>