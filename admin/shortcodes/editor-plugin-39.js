(function() {
	// TinyMCE plugin start.
	tinymce.PluginManager.add( 'CherryTinyMCEShortcodes', function( editor, url ) {
		// Register a command to open the dialog.
		editor.addCommand( 'cherry_open_dialog', function( ui, v ) {
			cherrySelectedShortcodeType = v;
			selectedText = editor.selection.getContent({format: 'text'});
			tb_dialog_helper.loadShortcodeDetails();
			tb_dialog_helper.setupShortcodeType( v );

			jQuery( '#shortcode-options' ).addClass( 'shortcode-' + v );
			jQuery( '#selected-shortcode' ).val( v );

			var f=jQuery(window).width();
			b=jQuery(window).height();
			f=720<f?720:f;
			f+=32;
			b-=120;

			tb_show( "Insert ["+ v +"] shortcode", "#TB_inline?width="+f+"&height="+b+"&inlineId=dialog" );
		});

		// Register a command to insert the self-closing shortcode immediately.
		editor.addCommand( 'cherry_insert_self_immediate', function( ui, v ) {
			editor.insertContent( '[' + v + ']' );
		});

		// Register a command to insert the enclosing shortcode immediately.
		editor.addCommand( 'cherry_insert_immediate', function( ui, v ) {
			var selected = editor.selection.getContent({format: 'text'});

			editor.insertContent( '[' + v + ']' + selected + '[/' + v + ']' );
		});

		// Register a command to insert the N-enclosing shortcode immediately.
		editor.addCommand( 'cherry_insert_immediate_n', function( ui, v ) {
			var arr = v.split('|'),
				selected = editor.selection.getContent({format: 'text'}),
				sortcode;

			for (var i = 0, len = arr.length; i < len; i++) {
				if (0 === i) {
					sortcode = '[' + arr[i] + ']' + selected + '[/' + arr[i] + ']';
				} else {
					sortcode += '[' + arr[i] + '][/' + arr[i] + ']';
				};
			};
			editor.insertContent( sortcode );
		});

		// Register a command to insert `Tabs` shortcode.
		editor.addCommand( 'cherry_insert_tabs', function( ui, v ) {
			editor.insertContent( '[tabs direction="top" tab1="Title #1" tab2="Title #2" tab3="Title #3"] [tab1] Tab 1 content... [/tab1] [tab2] Tab 2 content... [/tab2] [tab3] Tab 3 content... [/tab3] [/tabs]' ); // direction - top, right, below, left
		});

		// Register a command to insert `Accordion` shortcode.
		editor.addCommand( 'cherry_insert_accordions', function( ui, v ) {
			editor.insertContent( '[accordions] [accordion title="title1" visible="yes"] tab content [/accordion] [accordion title="title2"] another content tab [/accordion] [/accordions]' );
		});

		// Register a command to insert `Table` shortcode.
		editor.addCommand( 'cherry_insert_table', function( ui, v ) {
			editor.insertContent( '[table td1="#" td2="Title" td3="Value"] [td1] 1 [/td1] [td2] some title 1 [/td2] [td3] some value 1 [/td3] [/table]' );
		});

		// Add a button that opens a window
		editor.addButton( 'cherry_shortcodes_button', {
			type: 'menubutton',
			icon: 'icon icon-puzzle-piece',
			tooltip: 'Insert a Cherry Shortcode',
			menu: [
				// Posts menu.
				{text: 'Posts', menu: [
					{text: 'Posts Grid', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'posts_grid', { title: 'Posts Grid' } ); } },
					{text: 'Posts List', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'posts_list', { title: 'Posts List' } ); } },
					{text: 'Mini Posts Grid', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'mini_posts_grid', { title: 'Mini Posts Grid' } ); } },
					{text: 'Mini Posts List', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'mini_posts_list', { title: 'Mini Posts List' } ); } },
					{text: 'Recent Posts', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'recentposts', { title: 'Recent Posts' } ); } },
					{text: 'Recent Testimonials', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'recenttesti', { title: 'Recent Testimonials' } ); } }
				]},
				// Basic menu.
				{text: 'Basic', menu: [
					{text: 'Banner', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'banner', { title: 'Banner' } ); } },
					{text: 'Comments', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'recentcomments', { title: 'Comments' } ); } },
					{text: 'Post Cycle', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'post_cycle', { title: 'Post Cycle' } ); } },
					{text: 'Carousel (Elasti)', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'carousel', { title: 'Carousel (Elasti)' } ); } },
					{text: 'Carousel (OWL)', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'carousel_owl', { title: 'Carousel (OWL)' } ); } },
					{text: 'Roundabout', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'roundabout', { title: 'Roundabout' } ); } },
					{text: 'Service Box', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'service_box', { title: 'Service Box' } ); } },
					{text: 'Hero Unit', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'hero_unit', { title: 'Hero Unit' } ); } },
					{text: 'Categories', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'categories', { title: 'Categories' } ); } },
					{text: 'Tags', onclick: function() { editor.execCommand( 'cherry_insert_self_immediate', false, 'tags', { title: 'Tags' } ); } },
				]},
				// Columns menu.
				{text: 'Columns', menu: [
					{text: 'row', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'row', { title: 'row' } ); } },
					{text: 'row inner', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'row_in', { title: 'row inner' } ); } },
					{text: 'span1', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span1', { title: 'span1' } ); } },
					{text: 'span2', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span2', { title: 'span2' } ); } },
					{text: 'span3', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span3', { title: 'span3' } ); } },
					{text: 'span4', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span4', { title: 'span4' } ); } },
					{text: 'span5', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span5', { title: 'span5' } ); } },
					{text: 'span6', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span6', { title: 'span6' } ); } },
					{text: 'span7', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span7', { title: 'span7' } ); } },
					{text: 'span8', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span8', { title: 'span8' } ); } },
					{text: 'span9', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span9', { title: 'span9' } ); } },
					{text: 'span10', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span10', { title: 'span10' } ); } },
					{text: 'span11', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span11', { title: 'span11' } ); } },
					{text: 'span12', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'span12', { title: 'span12' } ); } }
				]},
				// Fluid Columns menu.
				{text: 'Fluid Columns', menu: [
					{text: 'row fluid', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'row_fluid', { title: 'row fluid' } ); } },
					{text: '1/2', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'one_half', { title: '1/2' } ); } },
					{text: '1/3', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'one_third', { title: '1/3' } ); } },
					{text: '2/3', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'two_third', { title: '2/3' } ); } },
					{text: '1/4', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'one_fourth', { title: '1/4' } ); } },
					{text: '3/4', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'three_fourth', { title: '3/4' } ); } },
					{text: '1/6', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'one_sixth', { title: '1/6' } ); } },
					{text: '5/6', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'five_sixth', { title: '5/6' } ); } }
				]},
				// 2 Columns menu.
				{text: '2 Columns', menu: [
					{text: '1/2 | 1/2', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span6|span6', { title: '1/2 | 1/2' } ); } },
					{text: '2/3 | 1/3', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span8|span4', { title: '2/3 | 1/3' } ); } },
					{text: '1/3 | 2/3', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span4|span8', { title: '1/3 | 2/3' } ); } }
				]},
				// 3 Columns menu.
				{text: '3 Columns', menu: [
					{text: '1/3 | 1/3 | 1/3', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span4|span4|span4', { title: '1/3 | 1/3 | 1/3' } ); } },
					{text: '1/2 | 1/4 | 1/4', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span6|span3|span3', { title: '1/2 | 1/4 | 1/4' } ); } },
					{text: '1/4 | 1/2 | 1/4', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span3|span6|span3', { title: '1/4 | 1/2 | 1/4' } ); } },
					{text: '1/4 | 1/4 | 1/2', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span3|span3|span6', { title: '1/4 | 1/4 | 1/2' } ); } }
				]},
				// 4 Columns menu.
				{text: '4 Columns', menu: [
					{text: '1/4 | 1/4 | 1/4 | 1/4', onclick: function() { editor.execCommand( 'cherry_insert_immediate_n', false, 'span3|span3|span3|span3', { title: '1/4 | 1/4 | 1/4 | 1/4' } ); } }
				]},
				// Elements menu.
				{text: 'Elements', menu: [
					{text: 'Label', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'label', { title: 'Label' } ); } },
					{text: 'Text Highlight', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'highlight', { title: 'Text Highlight' } ); } },
					{text: 'Button', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'button', { title: 'Button' } ); } },
					{text: 'Drop Cap', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'dropcap', { title: 'Drop Cap' } ); } },
					// {text: 'Blockquote', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'blockquote', { title: 'Blockquote' } ); } },
					{text: 'Icon', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'icon', { title: 'Icon' } ); } },
					// {text: 'Frame', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'frame', { title: 'Frame' } ); } },
					{text: 'Horizontal Rule', onclick: function() { editor.execCommand( 'cherry_insert_self_immediate', false, 'hr', { title: 'Horizontal Rule' } ); } },
					{text: 'Small Horizontal Rule', onclick: function() { editor.execCommand( 'cherry_insert_self_immediate', false, 'sm_hr', { title: 'Small Horizontal Rule' } ); } },
					{text: 'Vertical Rule', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'vr', { title: 'Vertical Rule' } ); } },
					{text: 'Spacer', onclick: function() { editor.execCommand( 'cherry_insert_self_immediate', false, 'spacer', { title: 'Spacer' } ); } },
					{text: 'Progressbar', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'progressbar', { title: 'Progressbar' } ); } },
					{text: 'Address', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'address', { title: 'Address' } ); } },
					{text: 'Clear', onclick: function() { editor.execCommand( 'cherry_insert_self_immediate', false, 'clear', { title: 'Clear' } ); } },
					{text: 'Extra Wrap', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'extra_wrap', { title: 'Extra Wrap' } ); } },
					{text: 'Content Box', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'content_box', { title: 'Content Box' } ); } }
				]},
				// Lists menu.
				{text: 'Lists', menu: [
					{text: 'Unstyled', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'list_un', { title: 'Unstyled' } ); } },
					{text: 'Check List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'check_list', { title: 'Check List' } ); } },
					{text: 'Check 2 List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'check2_list', { title: 'Check 2 List' } ); } },
					{text: 'OK Circle List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'ok_circle_list', { title: 'OK Circle List' } ); } },
					{text: 'OK Sign List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'ok_sign_list', { title: 'OK Sign List' } ); } },
					{text: 'Arrow List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'arrow_list', { title: 'Arrow List' } ); } },
					{text: 'Arrow 2 List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'arrow2_list', { title: 'Arrow 2 List' } ); } },
					{text: 'Circle Arrow List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'circle_arrow_list', { title: 'Circle Arrow List' } ); } },
					{text: 'Caret List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'caret_list', { title: 'Caret List' } ); } },
					{text: 'Angle List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'angle_list', { title: 'Angle List' } ); } },
					{text: 'Double Angle List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'double_angle_list', { title: 'Double Angle List' } ); } },
					{text: 'Star List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'star_list', { title: 'Star List' } ); } },
					{text: 'Plus List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'plus_list', { title: 'Plus List' } ); } },
					{text: 'Minus List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'minus_list', { title: 'Minus List' } ); } },
					{text: 'Circle List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'circle_list', { title: 'Circle List' } ); } },
					{text: 'Circle Blank List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'circle_blank_list', { title: 'Circle Blank List' } ); } },
					{text: 'Custom List', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'custom_list', { title: 'Custom List' } ); } }
				]},
				// Misc menu.
				{text: 'Misc', menu: [
					{text: 'Alert Box', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'alert_box', { title: 'Alert Box' } ); } },
					{text: 'Well', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'well', { title: 'Well' } ); } },
					{text: 'Small', onclick: function() { editor.execCommand( 'cherry_insert_immediate', false, 'small', { title: 'Small' } ); } },
					{text: 'Title Box', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'title', { title: 'Title Box' } ); } },
					{text: 'Template URL', onclick: function() { editor.execCommand( 'cherry_insert_self_immediate', false, 'template_url', { title: 'Template URL' } ); } },
					{text: 'Sitemap', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'site_map', { title: 'Sitemap' } ); } }
				]},
				{text: 'Video Preview', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'video_preview', { title: 'Video Preview' } ); } },
				{text: 'Tabs', onclick: function() { editor.execCommand( 'cherry_insert_tabs', false, 'tabs', { title: 'Tabs' } ); } },
				{text: 'Accordion', onclick: function() { editor.execCommand( 'cherry_insert_accordions', false, 'accordions', { title: 'Accordion' } ); } },
				{text: 'Table', onclick: function() { editor.execCommand( 'cherry_insert_table', false, 'table', { title: 'Table' } ); } },
				{text: 'Pricing Table', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'chp_pricing_table', { title: 'Pricing Table' } ); } },
				{text: 'Google Map', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'map', { title: 'Google Map' } ); } },
				{text: 'Google Map Api', onclick: function() { editor.execCommand( 'cherry_open_dialog', false, 'google_map_api', { title: 'Google Map Api' } ); } }
			]
		});
	}); // TinyMCE plugin end.
})();