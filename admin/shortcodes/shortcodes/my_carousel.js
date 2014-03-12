frameworkShortcodeAtts={
	attributes:[
			{
				label:"Title",
				id:"title",
				help:"Title for your carousel."
			},
			{
				label:"How many posts to show?",
				id:"num",
				help:"This is how many recent posts will be displayed."
			},
			{
				label:"Type of posts",
				id:"type",
				help:"Input the type of posts (default is post)."
			},
			{
				label:"Do you want to show the featured image?",
				id:"thumb",
				controlType:"select-control",
				selectValues:['true', 'false'],
				defaultValue: 'true',
				defaultText: 'true',
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
				label:"Link Text for post",
				id:"more_text_single",
				help:"Link Text for post."
			},
			{
				label:"Which category to pull from? (for Blog posts)",
				id:"category",
				help:"Enter the slug of the category you'd like to pull posts from. Leave blank if you'd like to pull from all categories."
			},
			{
				label:"Which category to pull from? (for Custom posts)",
				id:"custom_category",
				help:"Enter the slug of the category you'd like to pull posts from. Leave blank if you'd like to pull from all categories."
			},
			{
				label:"The number of words in the content (or excerpt if it's set)",
				id:"excerpt_count",
				help:"How many words are displayed in the content (or excerpt if it's set) ?"
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
				label:"Min Items",
				id:"min_items",
				controlType:"select-control",
				selectValues:['1', '2', '3', '4'],
				defaultValue: '3',
				defaultText: '3',
				help:"This params for better handling responsive behaviour."
			},
			{
				label:"Spacer Items",
				id:"spacer",
				help:"Enter space(margin) value for items (default, 20px)."
			},
			{
				label:"Custom class",
				id:"custom_class",
				help:"Use this field if you want to use a custom class."
			}
	],
	defaultContent:"",
	shortcode:"carousel"
};