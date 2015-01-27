jQuery(document).ready(function(){
	// OWL Carousel init.
	cherryPluginCarouselInit();

	// Full-width block with left-right paddings.
	jQuery('.content_box.full-width').wrapInner('<div class="full-block-wrap"></div>');
	jQuery(window).resize( function(){
		contenBoxResizeFunction();
	});

	contenBoxResizeFunction();
	function contenBoxResizeFunction(){
		var width_of_viewport = jQuery(window).width(),
			half_width_of_viewport = width_of_viewport / 2,
			width_of_container = jQuery('.content-holder > .container').width();

		jQuery('.content_box.full-width').width(width_of_container);
		jQuery('.content_box.full-width').css({'padding-left': half_width_of_viewport+'px', 'padding-right': half_width_of_viewport+'px', 'margin-left': '-'+half_width_of_viewport+'px'});
		jQuery('.full-block-wrap').width(width_of_container);
	}
});

function cherryPluginCarouselInit() {
	jQuery('div[id^="owl-carousel-"]').each(function(){
		var carousel = jQuery(this),
			auto_play = parseInt(carousel.attr('data-auto-play'))<1 ? false : parseInt(carousel.attr('data-auto-play')),
			items_count = parseInt(carousel.attr('data-items')),
			display_navs = carousel.attr('data-nav')=='true' ? true : false,
			display_pagination = carousel.attr('data-pagination')=='true' ? true : false,
			auto_height = items_count<=1 ? true : false,
			keys = Object.keys(items_custom),
			last_key = keys[keys.length-1];

		items_custom[last_key] = [items_custom[last_key][0], items_count];

		jQuery(carousel).owlCarousel({
			autoPlay: auto_play,
			navigation: display_navs,
			pagination: display_pagination,
			navigationText: false,
			autoHeight: auto_height,
			itemsCustom: items_custom
		});
	})
	jQuery('.owl-prev').addClass('icon-chevron-left');
	jQuery('.owl-next').addClass('icon-chevron-right');
}