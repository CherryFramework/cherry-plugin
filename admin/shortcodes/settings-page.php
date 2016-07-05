<form method="post" action="options.php">
	<?php settings_fields( 'cp_ss_group' ); ?>
	<?php $settings = get_option( 'cherry_plugin_shortcode_settings', array() ); ?>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Google Maps API Key (required)', 'cherry-plugin' ); ?>
				</th>
				<td>
					<input
						type="text"
						name="cherry_plugin_shortcode_settings[google_apikey]"
						id="cp_ss_google_apikey"
						value="<?php echo ! empty( $settings['google_apikey'] ) ? $settings['google_apikey'] : ''; ?>"
						style="width:100%">

				</td>
				<td><label class="descrirtion" for="cherry_plugin_shortcode_settings[google_apikey]"><?php printf( __( 'This API key can be obtained from the <a href="%s">Google Developers Console</a>.', 'cherry-plugin' ), 'https://console.developers.google.com/' ) ?></td>
			</tr>
		</tbody>
	</table>
	<?php submit_button(); ?>
</form>