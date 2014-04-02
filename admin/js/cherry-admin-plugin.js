jQuery(document).ready(function(){
	if(typeof remove_widgets_text != 'undefined'){
		var inactive_holder = jQuery('#wp_inactive_widgets');
		jQuery('.description', inactive_holder).after('<a href="#" style="float: right; margin: 2px 4px 5px 5px;" class="button remove_inactive">'+remove_widgets_text+'</a>');
	}
	jQuery('.remove_inactive').click(
		function () {
			jQuery.post(
				ajaxurl,
				{action: 'remove_widgets'},
				function (data) {
					if(data == 'done'){
						jQuery('.widget', inactive_holder).remove();
					}
				}
			);
			return false;
		}
	);
});