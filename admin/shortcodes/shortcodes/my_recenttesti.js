frameworkShortcodeAtts={
	attributes:[
			{
				label:"How many testimonials to show?",
				id:"num",
				help:"This is how many recent testimonials will be displayed."
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
				label:"Post content",
				id:"text",
				controlType:"select-control",
				selectValues:['part', 'full'],
				defaultValue: 'part',
				defaultText: 'part',
				help:"Choose to display an part or full content."
			},
			{
				label:"The number of words in the excerpt",
				id:"excerpt_count",
				help:"How many words are displayed in the excerpt?"
			},
			{
				label:"Linked text?",
				id:"linked",
				controlType:"select-control",
				selectValues:['true', 'false'],
				defaultValue: 'true',
				defaultText: 'true',
			},
			{
				label:"Custom class",
				id:"custom_class",
				help:"Use this field if you want to use a custom class."
			}
	],
	defaultContent:"",
	shortcode:"recenttesti"
};