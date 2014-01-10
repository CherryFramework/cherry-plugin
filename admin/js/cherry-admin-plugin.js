jQuery(document).ready(function(){
	// ---------------------------------------------------------
	// Ajax Filter
	// ---------------------------------------------------------
	jQuery('#toolbar-filter select').live('change', function(e){
		load_filters(this);
	});
})