frameworkShortcodeAtts={
	attributes:[
			{
				label:"Title",
				id:"title",
				help:"Title for your carousel."
			},
			{
				label:"Type of posts",
				id:"post_type",
				help:"Input the type of posts (default is post)."
			},
			{
				label:"Which category to pull from?",
				id:"categories",
				help:"Enter the slug of the category you'd like to pull posts from. Leave blank if you'd like to pull from all categories."
			},
			{
				label:"How many posts to show?",
				id:"posts_count",
				help:"This is how many recent posts will be displayed."
			},
			{
				label:"Number of visibility items?",
				id:"visibility_items",
				help:"This is how many visibility items in carousel."
			},
			{
				label:"Do you want to show the featured image?",
				id:"thumb",
				controlType:"select-control",
				selectValues:['yes', 'no'],
				defaultValue: 'yes',
				defaultText: 'yes',
				help:"Enable or disable featured image."
			},
			{
				label:"Image width",
				id:"thumb_width",
				help:"Set width for your featured images."
			},
			{
				label:"Image height",
				id:"thumb_height",
				help:"Set height for your featured images."
			},
			{
				label:"Display post date?",
				id:"date",
				controlType:"select-control",
				selectValues:['yes', 'no'],
				defaultValue: 'yes',
				defaultText: 'yes',
				help:"Enable or disable post date."
			},
			{
				label:"Display post author?",
				id:"author",
				controlType:"select-control",
				selectValues:['yes', 'no'],
				defaultValue: 'yes',
				defaultText: 'yes',
				help:"Enable or disable post author."
			},
			{
				label:"Display post comments?",
				id:"comments",
				controlType:"select-control",
				selectValues:['yes', 'no'],
				defaultValue: 'no',
				defaultText: 'no',
				help:"Enable or disable post comments."
			},
			{
				label:"Link Text for post",
				id:"more_text_single",
				help:"Link Text for post."
			},
			{
				label:"The number of words in the content (or excerpt if it's set)",
				id:"excerpt_count",
				help:"How many words are displayed in the content (or excerpt if it's set) ?"
			},
			{
				label:"Pause time",
				id:"auto_play",
				help:"Pause time (ms)."
			},
			{
				label:"Next & Prev navigation",
				id:"display_navs",
				controlType:"select-control",
				selectValues:['yes', 'no'],
				defaultValue: 'yes',
				defaultText: 'yes',
				help:"Display next & prev navigation?"
			},
			{
				label:"Pagination",
				id:"display_pagination",
				controlType:"select-control",
				selectValues:['yes', 'no'],
				defaultValue: 'yes',
				defaultText: 'yes',
				help:"Display pagination?"
			},
			{
				label:"Custom class",
				id:"custom_class",
				help:"Use this field if you want to use a custom class."
			}
	],
	defaultContent:"",
	shortcode:"carousel_owl"
};