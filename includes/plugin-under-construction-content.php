<?php
	$mtc_options = get_option('mtc_options');
	$get_theme= wp_get_theme();
	$date_ms = (float) $mtc_options['date_ms'];
	$left_time = $date_ms-(gmdate("U"));
?>
<script>
	jQuery(document).ready(function() {
		var left_time = parseInt("<?php echo $left_time; ?>");

		set_date();
		setInterval(set_date, 1000);

		function set_date(){
			left_time--;

			var get_date = timeToEvent(left_time),
				days = '<span>'+get_date['days'].substr(0,1)+'</span><span>'+get_date['days'].substr(1,1)+'</span>',
				hours = '<span>'+get_date['hours'].substr(0,1)+'</span><span>'+get_date['hours'].substr(1,1)+'</span>',
				minutes = '<span>'+get_date['minutes'].substr(0,1)+'</span><span>'+get_date['minutes'].substr(1,1)+'</span>',
				seconds = '<span>'+get_date['seconds'].substr(0,1)+'</span><span>'+get_date['seconds'].substr(1,1)+'</span>';

			days += get_date['days'].length>2 ? '<span>'+get_date['days'].substr(2,1)+'</span>' : '' ;

			jQuery('#days_left .numbers').html(days);
			jQuery('#hour_left .numbers').html(hours);
			jQuery('#minute_left .numbers').html(minutes);
			jQuery('#seconds_left .numbers').html(seconds);

			if(left_time<0){
				jQuery('#under_construction_timer').html("<span class='web_site_message'><?php _e('Website is currently down for maintenance.', CHERRY_PLUGIN_DOMAIN); ?></span>");
			}
		}

		function timeToEvent(eventDate){
			var output = Array(),
				days = (eventDate-(eventDate%86400))/86400,
				hours_left = (eventDate%86400),
				hours = (hours_left-(hours_left%3600))/3600,
				minutes_left = (hours_left%3600),
				minutes = (minutes_left-(minutes_left%60))/60,
				seconds = minutes_left%60;

			output['days'] = days < 10 ? "0"+days : days.toString();
			output['hours'] = hours < 10 ? "0"+hours : hours.toString();
			output['minutes'] = minutes<10 ? "0"+minutes : minutes.toString();
			output['seconds'] = seconds<10 ? "0"+seconds : seconds.toString();

			return output;
		}
	});
</script>
<div id="under_construction_page" class="container">
<?php
	if(isset($mtc_options['mtc_mode_logo'])){ ?>
		<div id="under_construction_logo">
	<?php if($get_theme['Template']=='CherryFramework'){
			if(file_exists(CURRENT_THEME_DIR.'static/static-logo.php')){
				include_once (CURRENT_THEME_DIR.'static/static-logo.php');
			}elseif (file_exists(get_theme_root().'/'.$get_theme['Template'].'/static/static-logo.php')) {
				include_once (get_theme_root().'/'.$get_theme['Template'].'/static/static-logo.php');
			}
		}else{ ?>
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
<?php 	} ?>
		</div>
<?php };

if(isset($mtc_options['mtc_mode_description'])){ ?>
	<p id="under_construction_description"><?php echo $mtc_options['mtc_mode_description']; ?></p>
<?php
};
if(isset($mtc_options['mtc_mode_timer'])){ ?>
	<div id="under_construction_timer">
		<div id="days_left" class="box">
			<div class="numbers"></div>
			<div class="lable"><?php _e('Days', CHERRY_PLUGIN_DOMAIN); ?></div>
		</div>
		<div id="hour_left" class="box">
			<div class="numbers"></div>
			<div class="lable"><?php _e('Hours', CHERRY_PLUGIN_DOMAIN); ?></div>
		</div>
		<div id="minute_left" class="box">
			<div class="numbers"></div>
			<div class="lable"><?php _e('Minutes', CHERRY_PLUGIN_DOMAIN); ?></div>
		</div>
		<div id="seconds_left" class="box">
			<div class="numbers"></div>
			<div class="lable"><?php _e('Seconds', CHERRY_PLUGIN_DOMAIN); ?></div>
		</div>
	</div>
<?php
};
?>
	<div id="under-construction-area">
		<?php dynamic_sidebar("under-construction-area"); ?>
	</div>
</div>