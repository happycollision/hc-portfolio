<?php
/**
 * Adds a new field group
 *
 * Usage:
 * simple_fields_register_field_group( string unique_field_group_slug, array field_groups );
 * 
 * Each field group looks like this:
 * 
 * 	array (
 * 		'name' => 'Name of field group',
 * 		'description' => "A short description of the field group",
 * 		'repeatable' => TRUE or FALSE if the field group should be repeatable,
 * 		'fields' => array of fields,
 * 		'deleted' => TRUE or FALSE
 *	),
 * 	array ( another field group array ),
 * 	array ( another field group array ),
 * 	array ( ...)
 *
 * Each field array looks like this:
 * 	array(
 * 		'slug' => "unique_field_slug"
 * 		'name' => 'Field name',
 *		'description' => 'Field description',
 *		'type' => 'field type',
 * 		'deleted' => TRUE or FALSE
 *	)
 * 
 * @param string $slug the slug of this field group. must be unique.
 * @param array $new_field_group settings/options for the new group
 * @return array the new field group as an array
 */
simple_fields_register_field_group('project_properties',
	array (
		'name' => 'Project Properties',
		'description' => "The name of the overarching project should be entered at the top of the page. The items you will add below are from that project. For example, if it is a video of scenes from Oklahoma, the project name would be Oklahoma.",
		'repeatable' => 0,
		'fields' => array(
			array(
				'slug' => "hc_project_location",
				'name' => 'Project Location',
				'description' => 'Examples: "Illinois Wesleyan" "Florida State University" "Post Playhouse"',
				'type' => 'text'
			)
			,array(
				'slug' => "hc_project_date",
				'name' => 'Project Date',
				'description' => 'Optional',
				'type' => 'date_v2',
				"options" => array(
					"date_v2" => array(
						"show" => "on_click",
						"show_as" => "date",
						"default_date" => "no_date"
					)
				)
			)			
			,array(
				'slug' => "hc_project_description",
				'name' => 'Project Description',
				'description' => 'Optional',
				'type' => 'textarea',
				'type_textarea_options' => array('use_html_editor' => 0)
			)
		)
	)
);

simple_fields_register_field_group('portfolio_item_video',
	array (
		'name' => 'Video Item(s)',
		'description' => 'Add one or more videos to your project portfolio. You can (and probably should) upload the same exact video in multiple formats. If you have different videos (one that is a slideshow of images and one that is actual footage of the show, for example), click the "+ Add" button again to upload the second clip.',
		'repeatable' => 1,
		'fields' => HCPortfolio::$all_simple_fields['video']
	)
);

simple_fields_register_field_group('portfolio_item_audio',
	array (
		'name' => 'Audio Item(s)',
		'description' => "Add one or more audio clips to your project portfolio. The only acceptable audio type is .mp3, so be sure it is converted to that.",
		'repeatable' => 1,
		'fields' => HCPortfolio::$all_simple_fields['audio']
	)
);

simple_fields_register_field_group('portfolio_item_image_gallery',
	array (
		'name' => 'Image Gallery',
		'description' => "Add a bunch of images to a single gallery.",
		'repeatable' => 1,
		'fields' => HCPortfolio::$all_simple_fields['image_gallery']
	)
);

simple_fields_register_field_group('portfolio_item_image',
	array (
		'name' => 'Single Image',
		'description' => "One single image. Perhaps your project was a painting? This field will be treated slightly different from the Featured Image.",
		'repeatable' => 0,
		'fields' => HCPortfolio::$all_simple_fields['single_image']
	)
);

/*
			,array(
				'slug' => "my_text_field_slug",
				'name' => 'Test text',
				'description' => 'Text description',
				'type' => 'text'
			)
			,array(
				'slug' => "my_textarea_field_slug",
				'name' => 'Test textarea',
				'description' => 'Textarea description',
				'type' => 'textarea',
				'type_textarea_options' => array('use_html_editor' => 1)
			)
			,array(
				'slug' => "my_checkbox_field_slug",
				'name' => 'Test checkbox',
				'description' => 'Checkbox description',
				'type' => 'checkbox',
				'type_checkbox_options' => array('checked_by_default' => 1)
			)
			,array(
				'slug' => "my_radiobutton_field_slug",
				'name' => 'Test radiobutton',
				'description' => 'Radiobutton description',
				'type' => 'radiobutton',
				'type_radiobutton_options' => array(
					array("value" => "Yes"),
					array("value" => "No")
				)
			)
			,array(
				'slug' => "my_dropdown_field_slug",
				'name' => 'Test dropdown',
				'description' => 'Dropdown description',
				'type' => 'dropdown',
				'type_dropdown_options' => array(
					"enable_multiple" => 1,
					"enable_extended_return_values" => 1,
					array("value" => "Yes"),
					array("value" => "No")
				)
			)
			,array(
				'slug' => "my_file_field_slug",
				'name' => 'Test file',
				'description' => 'File description',
				'type' => 'file'
			)
			,array(
				'slug' => "my_post_field_slug",
				'name' => 'Test post',
				'description' => 'Post description',
				'type' => 'post',
				'type_post_options' => array("enabled_post_types" => array("post"))
			)
			,array(
				'slug' => "my_taxonomy_field_slug",
				'name' => 'Test taxonomy',
				'description' => 'Taxonomy description',
				'type' => 'taxonomy',
				'type_taxonomy_options' => array("enabled_taxonomies" => array("category"))
			)
			,array(
				'slug' => "my_taxonomyterm_field_slug",
				'name' => 'Test taxonomy term',
				'description' => 'Taxonomy term description',
				'type' => 'taxonomyterm',
				'type_taxonomyterm_options' => array("enabled_taxonomy" => "category")
			)
			,array(
				'slug' => "my_color_field_slug",
				'name' => 'Test color selector',
				'description' => 'Color selector description',
				'type' => 'color'
			)
			,array(
				'slug' => "my_date_field_slug",
				'name' => 'Test date selector',
				'description' => 'Date selector description',
				'type' => 'date',
				'type_date_options' => array('use_time' => 1)
			)
			,array(
				'slug' => "my_date2_field_slug",
				'name' => 'Test date selector',
				'description' => 'Date v2 selector description',
				'type' => 'date_v2',
				"options" => array(
					"date_v2" => array(
						"show" => "on_click",
						"show_as" => "datetime",
						"default_date" => "today"
					)
				)
			)			
			,array(
				'slug' => "my_user_field_slug",
				'name' => 'Test user selector',
				'description' => 'User selector description',
				'type' => 'user'
			)
*/


// function simple_fields_register_post_connector($unique_name = "", $new_post_connector = array()) {
simple_fields_register_post_connector('portfolio_connector',
	array (
		'name' => "Portfolio Connector",
		'field_groups' => array(
			array(
				'slug' => 'project_properties',
				'context' => 'normal',
				'priority' => 'high'
			)
			,array(
				'slug' => 'portfolio_item_video',
				'context' => 'normal',
				'priority' => 'high'
			)
			,array(
				'slug' => 'portfolio_item_audio',
				'context' => 'normal',
				'priority' => 'high'
			)
/*			//The model for these items is not complete/necessary yet.
			,array(
				'slug' => 'portfolio_item_image_gallery',
				'context' => 'normal',
				'priority' => 'high'
			)
			,array(
				'slug' => 'portfolio_item_image',
				'context' => 'normal',
				'priority' => 'high'
			)
*/
		),
		'post_types' => array('hc_portfolio'),
		'hide_editor' => false
	)
);

/**
 * Sets the default post connector for a post type
 * 
 * @param $post_type_connector = connector id (int) or slug (string) or string __inherit__
 * 
 */
simple_fields_register_post_type_default('portfolio_connector', 'hc_portfolio');
