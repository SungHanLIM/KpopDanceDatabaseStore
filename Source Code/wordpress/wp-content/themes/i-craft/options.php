<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 */
 

/*
function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);

	// echo $themename;
}
*/

function optionsframework_option_name() {
       $themename = get_option( 'stylesheet' );
       $themename = preg_replace( "/\W/", "_", strtolower( $themename ) );
       return $themename;
}

if ( ! function_exists( 'of_get_option' ) ) :
function of_get_option( $name, $default = false ) {

    // Get theme options
    $options = get_option( 'optionsframework' );

    // Return specific option
    if ( isset( $options[$name] ) ) {
        return $options[$name];
    }
    return $default;
}

endif;

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 */

function optionsframework_options() {

	// Test data
	$test_array = array(
		'one' => __('One', 'i-craft'),
		'two' => __('Two', 'i-craft'),
		'three' => __('Three', 'i-craft'),
		'four' => __('Four', 'i-craft'),
		'five' => __('Five', 'i-craft')
	);

	// Multicheck Array
	$multicheck_array = array(
		'one' => __('French Toast', 'i-craft'),
		'two' => __('Pancake', 'i-craft'),
		'three' => __('Omelette', 'i-craft'),
		'four' => __('Crepe', 'i-craft'),
		'five' => __('Waffle', 'i-craft')
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one' => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '#cfd0d2',
		'image' => get_template_directory_uri() . '/images/bg7.jpg',
		'repeat' => '',
		'position' => 'top center',
		'attachment'=> 'fixed' );

	// Typography Defaults
	$typography_defaults = array(
		'size' => '15px',
		'face' => 'georgia',
		'style' => 'bold',
		'color' => '#bada55' );

	// Typography Options
	$typography_options = array(
		'sizes' => array( '6','12','14','16','20' ),
		'faces' => array( 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ),
		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
		'color' => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories[] = "All";
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}
	

	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/images/';

	$options = array();

	$options[] = array(
		'name' => __('Basic Settings', 'i-craft'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Phone Number', 'i-craft'),
		'desc' => __('Phone number that appears on top bar.', 'i-craft'),
		'id' => 'top_bar_phone',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
		

	$options[] = array(
		'name' => __('Email Address', 'i-craft'),
		'desc' => __('Email Id that appears on top bar.', 'i-craft'),
		'id' => 'top_bar_email',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');		
		
	$options[] = array( 
		"name" => __('Site header logo', 'i-craft'),
		"desc" => __('Width 280px, height 72px max. Upload logo for header', 'i-craft'),
		"id" => "itrans_logo_image",
		"type" => "upload");
		
	$options[] = array( 
		"name" => __('Site title/slogan (optional)', 'i-craft'),
		"desc" => __('if you are using a logo and want your site title or slogan to appear on the header banner', 'i-craft'),
		"id" => "itrans_slogan",
		'std' => '',
		"type" => "text");

	$options[] = array(
		'name' => __('Layout Options', 'i-craft'),
		'type' => 'heading');
		
				
	$options[] = array(
		'name' => __( 'Primary Color', 'i-craft' ),
		'desc' => __( 'Choose your theme color', 'i-craft' ),
		'id' => 'itrans_primary_color',
		'std' => '#dd3333',
		'type' => 'color'
	);

	$options[] = array(
		'name' => __('Blog Posts Layout', 'i-craft'),
		'desc' => __('Choose blog posts layout (one column/two column)', 'i-craft'),
		'id' => "itrans_blog_layout",
		'std' => "onecol",
		'type' => "images",
		'options' => array(
			'onecol' => $imagepath . 'onecol.png',		
			'twocol' => $imagepath . 'twocol.png')
	);
	
	$options[] = array(
		'name' => __('Show Full Content', 'i-craft'),
		'desc' => __('Show full content on blog pages', 'i-craft'),
		'id' => 'full_content',
		'std' => '',
		'type' => 'checkbox');		
		
	$options[] = array(
		'name' => __('Wide layout', 'i-craft'),
		'desc' => __('Check to have wide layout', 'i-craft'),
		'id' => 'boxed_type',
		'std' => '',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('Main Sidebar on left (default sidebar appears on right)', 'i-craft'),
		'desc' => __('move the main sidebar position to left', 'i-craft'),
		'id' => 'sidebar_side',
		'std' => '',
		'type' => 'checkbox');	
					
		
	$options[] = array(
		'name' => __( 'Body Background', 'i-craft' ),
		'desc' => __( 'Change the background image/color.', 'i-craft' ),
		'id' => 'itrans_background_style',
		'std' => $background_defaults,
		'type' => 'background'
	);
	
	
	$options[] = array(
		'name' => __('Background Image Size : Cover', 'i-craft'),
		'desc' => __('Cover background image', 'i-craft'),
		'id' => 'bg_cover',
		'std' => '',
		'type' => 'checkbox');	
			
	$options[] = array(
		'name' => __('Additional style', 'i-craft'),
		'desc' => __('add extra style(CSS) codes here', 'i-craft'),
		'id' => 'itrans_extra_style',
		'std' => '',
		'type' => 'textarea');	
		
		
				
	$options[] = array(
		'name' => __('Social Links ', 'i-craft'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Facebook', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_social_facebook',
		'std' => '',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Twitter', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_social_twitter',
		'std' => '',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Youtube', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_social_youtube',
		'std' => '',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Flickr', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_social_flickr',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('RSS', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_social_feed',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('Instagram', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_social_instagram',
		'std' => '',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Google plus', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_social_googleplus',
		'std' => '',
		'type' => 'text');
				
		
	/* Sliders */
	$options[] = array(
		'name' => __('Slider', 'i-craft'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Slide Duration', 'i-craft'),
		'desc' => __('slide visibility in milisecond ', 'i-craft'),
		'id' => 'itrans_sliderspeed',
		'std' => '6000',
		'class' => 'mini',
		'type' => 'text');		

	$options[] = array(
		'name' => __('Slide1 Title', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide1_title',
		'std' => 'i-craft, Exclusive WooCommerce Features',
		'type' => 'text');

	$options[] = array(
		'name' => __('Slide1 Description', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide1_desc',
		'std' => 'To start setting up i-craft go to appearance &gt; Theme Options. Make sure you have installed recommended plugin &#34;TemplatesNext Toolkit&#34; by going appearance > install plugin.',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Slide1 Link text', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide1_linktext',
		'std' => 'Know More',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Slide1 Link URL', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide1_linkurl',
		'std' => '#',
		'type' => 'text');		

	$options[] = array(
		'name' => __('Slide1 Image', 'i-craft'),
		'desc' => __('Ideal image size width: 1200px and height: 440px', 'i-craft'),
		'id' => 'itrans_slide1_image',
		'std' => get_template_directory_uri() . '/images/slide1.jpg',
		'type' => 'upload');


	$options[] = array(
		'name' => __('Slide2 Title', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide2_title',
		'std' => 'Live Cart And Product Search',
		'type' => 'text');

	$options[] = array(
		'name' => __('Slide2 Description', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide2_desc',
		'std' => 'Optional live cart update on main navigation. Optional login menu item on main navigation. Toggle site search or product search',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Slide2 Link text', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide2_linktext',
		'std' => 'Know More',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Slide2 Link URL', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide2_linkurl',
		'std' => '#',
		'type' => 'text');		

	$options[] = array(
		'name' => __('Slide2 Image', 'i-craft'),
		'desc' => __('Ideal image size width: 1200px and height: 440px', 'i-craft'),
		'id' => 'itrans_slide2_image',
		'std' => get_template_directory_uri() . '/images/slide2.jpg',
		'type' => 'upload');



	$options[] = array(
		'name' => __('Slide3 Title', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide3_title',
		'std' => 'Product Carousel Shortcodes',
		'type' => 'text');

	$options[] = array(
		'name' => __('Slide3 Description', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide3_desc',
		'std' => 'i-craft comes with plugin &#34;TemplatesNext Toolkit&#34; giving you ability to create product or category carousel shortcodes ',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Slide3 Link text', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide3_linktext',
		'std' => 'Know More',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Slide3 Link URL', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide3_linkurl',
		'std' => '#',
		'type' => 'text');		

	$options[] = array(
		'name' => __('Slide3 Image', 'i-craft'),
		'desc' => __('Ideal image size width: 1200px and height: 440px', 'i-craft'),
		'id' => 'itrans_slide3_image',
		'std' => get_template_directory_uri() . '/images/slide3.jpg',
		'type' => 'upload');



	$options[] = array(
		'name' => __('Slide4 Title', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide4_title',
		'std' => 'Individual Page Customization',
		'type' => 'text');

	$options[] = array(
		'name' => __('Slide4 Description', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide4_desc',
		'std' => 'Customize your pages with meta options.',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Slide4 Link text', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide4_linktext',
		'std' => 'Know More',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Slide4 Link URL', 'i-craft'),
		'desc' => __('', 'i-craft'),
		'id' => 'itrans_slide4_linkurl',
		'std' => '#',
		'type' => 'text');		

	$options[] = array(
		'name' => __('Slide4 Image', 'i-craft'),
		'desc' => __('Ideal image size width: 1200px and height: 440px', 'i-craft'),
		'id' => 'itrans_slide4_image',
		'std' => get_template_directory_uri() . '/images/slide4.jpg',
		'type' => 'upload');
		
		
	/* Front Page */
	$options[] = array(
		'name' => __('Blog Page', 'i-craft'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Hide i-craft Slider', 'i-craft'),
		'desc' => __('Hide default i-craft slider', 'i-craft'),
		'id' => 'hide_front_slider',
		'std' => '',
		'type' => 'checkbox');	
				
		
	$options[] = array(
		'name' => __('Other Slider Shortcode', 'i-craft'),
		'desc' => __('Enter a 3rd party slider shortcode, ex. meta slider, smart slider 2, wow slider, etc.', 'i-craft'),
		'id' => 'other_front_slider',
		'std' => '',
		'type' => 'text');
		
		
	/* WooCommerce Settings */
	$options[] = array(
		'name' => __('WooCommerce', 'i-craft'),
		'type' => 'heading');
		
		
	$options[] = array(
		'name' => __('Hide Topnav Login', 'i-craft'),
		'desc' => __('Hide login menu item from top nav', 'i-craft'),
		'id' => 'hide_login',
		'std' => '',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Hide Topnav Cart', 'i-craft'),
		'desc' => __('Hide cart from top nav', 'i-craft'),
		'id' => 'hide_cart',
		'std' => '',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('Turn On Normal Search', 'i-craft'),
		'desc' => __('Product only search will be turned off.', 'i-craft'),
		'id' => 'normal_search',
		'std' => '',
		'type' => 'checkbox');
		

	
	
	
	/*
	$options[] = array( 'name' => __( 'Typography', 'theme-textdomain' ),
		'desc' => __( 'Example typography.', 'theme-textdomain' ),
		'id' => "example_typography",
		'std' => $typography_defaults,
		'type' => 'typography'
	);
	
	$options[] = array(
		'name' => __( 'Custom Typography', 'theme-textdomain' ),
		'desc' => __( 'Custom typography options.', 'theme-textdomain' ),
		'id' => "custom_typography",
		'std' => $typography_defaults,
		'type' => 'typography',
		'options' => $typography_options
	);	
	*/
	/*
	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);
	$options[] = array(
		'name' => __( 'Default Text Editor', 'theme-textdomain' ),
		'desc' => sprintf( __( 'You can also pass settings to the editor.  Read more about wp_editor in <a href="%1$s" target="_blank">the WordPress codex</a>', 'theme-textdomain' ), 'http://codex.wordpress.org/Function_Reference/wp_editor' ),
		'id' => 'example_editor',
		'type' => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => __( 'Select a Page', 'theme-textdomain' ),
		'desc' => __( 'Passed an pages with ID and post_title', 'theme-textdomain' ),
		'id' => 'example_select_pages',
		'type' => 'select',
		'options' => $options_pages
	);
	
	if ( $options_categories ) {
		$options[] = array(
			'name' => __( 'Select a Category', 'theme-textdomain' ),
			'desc' => __( 'Passed an array of categories with cat_ID and cat_name', 'theme-textdomain' ),
			'id' => 'example_select_categories',
			'type' => 'select',
			'options' => $options_categories
		);
	}
	*/		
				


	return $options;
}