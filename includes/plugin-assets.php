<?php
	//Widget
	include_once (CHERRY_PLUGIN_DIR . 'includes/widgets/register-widgets.php');

	// Exclude widget manager for customizer
	global $wp_customize;
	if ( !isset( $wp_customize ) ) {
		include_once (CHERRY_PLUGIN_DIR . 'includes/widgets/widgets-manager.php');
	}

//-----------------------------------------------------------------------------
// Maintenance Mode
//-----------------------------------------------------------------------------
	add_action( 'init', 'cherry_maintenance_mode_redirecte' );

	function cherry_maintenance_mode_redirecte() {
		global $pagenow;

		$mtc_options = get_option('mtc_options');
		if(isset($mtc_options['mtc_mode_on'])){
			// if (current_user_can( 'administrator')) {
			if ( is_user_logged_in() ) {
				if(is_admin()){
					if($pagenow != "admin.php"){
						add_action( 'admin_notices', 'cherry_maintenance_mode_notice' );
					}
				}else{
					add_action( 'wp_before_admin_bar_render', 'cherry_maintenance_mode_notice' );
				}
			}else{
				$page_url_now = cherry_get_page_URL();
				if(!strripos($page_url_now, '/wp-admin') && $pagenow != "wp-login.php"){
					add_action("wp_head", "cherry_under_construction_page_content");
				}
			}

			register_sidebar(array(
				'name'          => __('Under Construction Page Widgets Area.', CHERRY_PLUGIN_DOMAIN),
				'id'            => 'under-construction-area',
				'description'   => __('Under construction page widgets area.', CHERRY_PLUGIN_DOMAIN),
				'before_widget' => '<div id="%1$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4>',
				'after_title'   => '</h4>',
			));
		}
	}
	function cherry_under_construction_page_content(){
		$files = '/plugin-under-construction-content.php';
		$template_dir = file_exists(CURRENT_THEME_DIR . $files) ? CURRENT_THEME_DIR . $files : CHERRY_PLUGIN_DIR .'includes' . $files ;
		include_once ($template_dir);
		exit;
	}
	function cherry_maintenance_mode_notice(){
		echo cherry_add_notice(array(
				'wrapper_id' => 'maintenance_mode_notice',
				'wrapper_class' => 'error',
				'notice_content' => '<strong>'.__('Maintenance mode activated. Website is blocked from public', CHERRY_PLUGIN_DOMAIN).' <a href="'.admin_url().'admin.php?page=maintenance-mode-page" title="'.__('Settings.', CHERRY_PLUGIN_DOMAIN).'">'.__('Settings.', CHERRY_PLUGIN_DOMAIN).'</a></strong>'
			)
		);

		if(!is_admin()){
			echo '<script>jQuery(window).on("load", function() { setTimeout(function(){ jQuery("#maintenance_mode_notice").fadeOut(); }, 3000) })</script>';
		}
	}
	add_action('wp_ajax_mtc_save', 'cherry_mtc_save');
	function cherry_mtc_save() {
		$post_date = isset($data) ? $data : $_POST['data'] ;
		update_option('mtc_options', $post_date);
		exit();
	}
//-----------------------------------------------------------------------------
// End Maintenance Mode
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
// Add notace in admin panel or web site
//-----------------------------------------------------------------------------
	function cherry_add_notice($attr = array()){
		$default = array(
			'wrapper_id' => '',
			'wrapper_class' => 'error',
			'notice_content' => ''
		);
		extract(array_merge($default, $attr));

		$wrapper_id = $wrapper_id ? ' id ="'.$wrapper_id.'"' : '' ;
		$wrapper_class = $wrapper_class ? ' class ="'.$wrapper_class.'"' : '' ;
		$output = '<div'.$wrapper_id.$wrapper_class.'><p>'.$notice_content.'</p></div>';

		return $output;
	}
//-----------------------------------------------------------------------------
// get now page url
//-----------------------------------------------------------------------------
	function cherry_get_page_URL(){
		if(!isset($_SERVER['REQUEST_URI'])){
			$site_uri = $_SERVER['PHP_SELF'];
		} else {
			$site_uri = $_SERVER['REQUEST_URI'];
			$https = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
			$site_protocol = strtolower($_SERVER["SERVER_PROTOCOL"]);
			$site_protocol = substr($site_protocol,0,strpos($site_protocol,"/")).$https;
			$site_port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		}
		return $site_protocol."://".$_SERVER['SERVER_NAME'].$site_port.$site_uri;
	}