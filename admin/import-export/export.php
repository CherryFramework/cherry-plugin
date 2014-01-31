<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#export-content').on('click', post_export_content);
		var wpAjax=jQuery.extend({unserialize:function(a){var b,c,d,e,f={};if(!a)return f;b=a.split("?"),b[1]&&(a=b[1]),c=a.split("&");for(d in c)(!jQuery.isFunction(c.hasOwnProperty)||c.hasOwnProperty(d))&&(e=c[d].split("="),f[e[0]]=e[1]);return f},parseAjaxResponse:function(a,b,c){var d={},e=jQuery("#"+b).html(""),f="";return a&&"object"==typeof a&&a.getElementsByTagName("wp_ajax")?(d.responses=[],d.errors=!1,jQuery("response",a).each(function(){var b,e=jQuery(this),g=jQuery(this.firstChild);b={action:e.attr("action"),what:g.get(0).nodeName,id:g.attr("id"),oldId:g.attr("old_id"),position:g.attr("position")},b.data=jQuery("response_data",g).text(),b.supplemental={},jQuery("supplemental",g).children().each(function(){b.supplemental[this.nodeName]=jQuery(this).text()}).size()||(b.supplemental=!1),b.errors=[],jQuery("wp_error",g).each(function(){var e,g,h,i=jQuery(this).attr("code");e={code:i,message:this.firstChild.nodeValue,data:!1},g=jQuery('wp_error_data[code="'+i+'"]',a),g&&(e.data=g.get()),h=jQuery("form-field",g).text(),h&&(i=h),c&&wpAjax.invalidateForm(jQuery("#"+c+' :input[name="'+i+'"]').parents(".form-field:first")),f+="<p>"+e.message+"</p>",b.errors.push(e),d.errors=!0}).size()||(b.errors=!1),d.responses.push(b)}),f.length&&e.html('<div class="error">'+f+"</div>"),d):isNaN(a)?!e.html('<div class="error"><p>'+a+"</p></div>"):(a=parseInt(a,10),-1==a?!e.html('<div class="error"><p>'+wpAjax.noPerm+"</p></div>"):0===a?!e.html('<div class="error"><p>'+wpAjax.broken+"</p></div>"):!0)},invalidateForm:function(a){return jQuery(a).addClass("form-invalid").find("input:visible").change(function(){jQuery(this).closest(".form-invalid").removeClass("form-invalid")})},validateForm:function(a){return a=jQuery(a),!wpAjax.invalidateForm(a.find(".form-required").filter(function(){return""===jQuery("input:visible",this).val()})).size()}},wpAjax||{noPerm:"You do not have permission to do that.",broken:"An unidentified error has occurred."});

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