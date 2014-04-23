<?php
// Setup URL to WordPres
$absolute_path = __FILE__;
$path_to_wp    = explode( 'wp-content', $absolute_path );
$wp_url        = $path_to_wp[0];

// Access WordPress
require_once( $wp_url . '/wp-load.php' );

// URL to TinyMCE plugin folder
$plugin_url = CHERRY_PLUGIN_URL . '/includes/shortcodes/';

?><!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<div id="dialog">
			<div class="buttons-wrapper">
				<input type="button" id="cancel-button" class="button alignleft" name="cancel" value="<?php _e('Cancel', CHERRY_PLUGIN_DOMAIN) ?>" accesskey="C" />
				<input type="button" id="insert-button" class="button-primary alignright" name="insert" value="<?php _e('Insert', CHERRY_PLUGIN_DOMAIN) ?>" accesskey="I" />
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<h3 class="sc-options-title"><?php _e('Shortcode Options', CHERRY_PLUGIN_DOMAIN) ?></h3>
			<div id="shortcode-options" class="alignleft">
				<table id="options-table">
				</table>
				<input type="hidden" id="selected-shortcode" value="">
			</div>
			<div class="clear"></div>
			<?php include_once( CHERRY_PLUGIN_DIR . 'admin/shortcodes/dialog-js.php' ); ?>
		</div><!-- #dialog (end) -->
	</body>
</html>