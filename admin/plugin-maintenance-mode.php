<?php
	$mtc_options = get_option('mtc_options');
	$to_day = getdate();
	$set_date = array(
		'year' => isset($mtc_options['mtc_mode_year']) ? $mtc_options['mtc_mode_year'] : $to_day['year'],
		'month' => isset($mtc_options['mtc_mode_month']) ? $mtc_options['mtc_mode_month'] : $to_day['mon'],
		'day' => isset($mtc_options['mtc_mode_day']) ? $mtc_options['mtc_mode_day'] : $to_day['mday'],
		'hours' => isset($mtc_options['mtc_mode_hour']) ? $mtc_options['mtc_mode_hour'] : $to_day['hours'],
		'minutes' => isset($mtc_options['mtc_mode_minute']) ? $mtc_options['mtc_mode_minute'] : $to_day['minutes'],
		);
?>
<script>
	jQuery(document).ready(function() {
		function isValidDate(y, m, d){
			var dt = new Date(parseInt(y), parseInt(m)-1, parseInt(d));
			return (y-1900 == dt.getYear() && m-1 == dt.getMonth() && d == dt.getDate());
		}

		jQuery('#mtc_save').on('click', function() {
			var button = jQuery(this),
				post_data = Object();

			jQuery.each(button.parents("form").serializeArray(), function( index, value ) {
				post_data[value.name] = value.value;
			})

			if(isValidDate(post_data.mtc_mode_year, post_data.mtc_mode_month, post_data.mtc_mode_day)){
				button.addClass('button-primary-disabled').next('.spinner').css({'display':'block'});

				var now_date= new Date(),
					date = new Date(parseInt(post_data.mtc_mode_year), parseInt(post_data.mtc_mode_month)-1, parseInt(post_data.mtc_mode_day), parseInt(post_data.mtc_mode_hour), parseInt(post_data.mtc_mode_minute)),
					client_time_zone = now_date.getTimezoneOffset()*60000,
					date_ms = (date.getTime()-client_time_zone)/1000;

				post_data['date_ms'] = date_ms;

				jQuery.post(ajaxurl, {action: 'mtc_save', data: post_data}, function(response) {
					button.removeClass('button-primary-disabled').next('.spinner').css({'display':'none'});
				})
			}else{
				var error_date = jQuery('.date_input .error');
				error_date.css({'display':'block'});

				setTimeout(function(){
					error_date.css({'display':'none'});
				}, 3000)
			}
			return !1;
		});
	})
</script>
<form method="post" action="options.php">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<?php _e('Maintenance Mode', 'cherry-plugin') ?>
				</th>
				<td>
					<label for="mtc_mode_on">
						<input name="mtc_mode_on" type="checkbox" id="mtc_mode_on" value="1" <?php echo isset($mtc_options['mtc_mode_on']) ? 'checked="checked"' : '' ; ?>>
						<?php _e('Enable this option to activate website maintenance mode.', 'cherry-plugin') ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Display Logo?', 'cherry-plugin') ?>
				</th>
				<td>
					<label for="mtc_mode_logo">
						<input name="mtc_mode_logo" type="checkbox" id="mtc_mode_logo" value="1" <?php echo isset($mtc_options['mtc_mode_logo']) ? 'checked="checked"' : '' ; ?>>
						<?php _e('Enable this option to display website logo at the under construction page', 'cherry-plugin') ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Description', 'cherry-plugin') ?>
				</th>
				<td>
					<p>
						<textarea name="mtc_mode_description" rows="10" cols="50" id="mtc_mode_description" class="large-text code"><?php echo isset($mtc_options['mtc_mode_description']) ? $mtc_options['mtc_mode_description'] : '' ; ?></textarea>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Display timer?', 'cherry-plugin') ?>
				</th>
				<td>
					<label for="mtc_mode_timer">
						<input name="mtc_mode_timer" type="checkbox" id="mtc_mode_timer" value="1" <?php echo isset($mtc_options['mtc_mode_timer']) ? 'checked="checked"' : '' ; ?>>
						<?php _e('Display timer?', 'cherry-plugin') ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="date_input">
					<?php _e('Website launch date.', 'cherry-plugin'); ?><br>
					<span class="error"><?php _e('Date is not correct.', 'cherry-plugin') ?></span>
				</th>
				<td>
					<p>
						<label for="mtc_mode_year" class="small_size" ><?php _e('Year:', 'cherry-plugin') ?></label>
						<input name="mtc_mode_year" type="text" id="mtc_mode_year" value="<?php echo $set_date['year'] ?>" class="small-text" maxlength="4">
						<label for="mtc_mode_month" class="small_size" ><?php _e('Month:', 'cherry-plugin') ?></label>
						<input name="mtc_mode_month" type="text" id="mtc_mode_month" value="<?php echo $set_date['month'] ?>" class="small-text" maxlength="2">
						<label for="mtc_mode_day" class="small_size" ><?php _e('Day:', 'cherry-plugin') ?></label>
						<input name="mtc_mode_day" type="text" id="mtc_mode_day" value="<?php echo $set_date['day'] ?>" class="small-text" maxlength="2">
					</p>
					<p>
						<label for="mtc_mode_hour" class="small_size" ><?php _e('Hour:', 'cherry-plugin') ?></label>
						<input name="mtc_mode_hour" type="text" id="mtc_mode_hour" value="<?php echo $set_date['hours'] ?>" class="small-text" maxlength="2">
						<label for="mtc_mode_minute" class="small_size" ><?php _e('Minute:', 'cherry-plugin') ?></label>
						<input name="mtc_mode_minute" type="text" id="mtc_mode_minute" value="<?php echo $set_date['minutes'] ?>" class="small-text" maxlength="2">
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<a id="mtc_save" class="button button-primary float-left" href="#"><?php _e('Save Changes', 'cherry-plugin'); ?></a><span class="spinner" style="float:left; margin:4px 0 0 8px; "></span>
	</p>
</form>