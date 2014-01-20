<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#export-content').on('click', post_export_content);

		function post_export_content(){
			var button = jQuery(this);
			button.off('click').addClass('not_active').next('.spinner').css({'display':'block'});

			jQuery.post(ajaxurl, {action: 'export_content'}, function(response) {
				var res = wpAjax.parseAjaxResponse(response, "ajax-response");
				if(!res.errors){
					var file = res.responses[1].data,
						status = res.responses[0].data;
					if(file.indexOf('.zip')!=-1){
						window.location.href = '<?php echo (CHERRY_PLUGIN_URL."admin/import-export/download-content.php"); ?>?file=' + file;
					}
				}else{
					//console.log('error 1');
				}
				button.removeClass('not_active').on('click', post_export_content).next('.spinner').css({'display':'none'});;
			}).error(function(response) {
				button.removeClass('not_active').on('click', post_export_content).next('.spinner').css({'display':'none'});;
			});
			return !1;
		}
	});
</script>
<p><?php _e("Export allows you to create a backup of your website content in one click. You'll get a downloaded archive containing all website data: images, audio, video and other files from your media library. XML file with your posts and categories data, JSON file with widget settings. You can use downloaded archive to move your website to other hosting server or restore website data. ", CHERRY_PLUGIN_DOMAIN); ?></p>
<p><b><?php _e("Please note! ", CHERRY_PLUGIN_DOMAIN);  ?></b><?php _e("XML file doesn't contain any user data except name and email of the website administrator.", CHERRY_PLUGIN_DOMAIN); ?></p>
<a id="export-content" class="button button-primary float-left" href="#"><?php _e('Export Content', CHERRY_PLUGIN_DOMAIN); ?></a><span class="spinner"></span>