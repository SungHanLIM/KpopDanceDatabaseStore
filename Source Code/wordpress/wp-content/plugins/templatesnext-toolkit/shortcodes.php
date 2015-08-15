<?php


function tx_shortcodes_button() {

   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
      return;
   }

   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'tx_add_plugin' );
      add_filter( 'mce_buttons', 'tx_register_button' );
   }

}
//add_action('init', 'tx_shortcodes_button');
add_action('admin_head', 'tx_shortcodes_button');

function tx_add_plugin( $plugin_array ) {
   $plugin_array['txshortcodes'] = plugin_dir_url( __FILE__ ) . 'tx-shortcodes.js';
   return $plugin_array;
}

function tx_register_button( $buttons ) {
   array_push( $buttons, "|", "txshortcodes" );
   return $buttons;
}

// recent posts [tx_blog items="3" colums="6" showcat="show" category_id="8,9"]

function tx_blog_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'items' => 4,
      	'columns' => 4,
      	'showcat' => 'show',
      	'category_id' => '',
		'show_pagination' => 'no',		
      	'carousel' => 'no',								
   	), $atts);
	
	
	$width = 600;
	$height = 360;
	
	$post_in_cat = tx_shortcodes_comma_delim_to_array( $atts['category_id'] );
	$post_comments = '';

	$posts_per_page = intval( $atts['items'] );
	$total_column = intval( $atts['columns'] );
	$tx_category = $atts['showcat'];
	$tx_carousel = $atts['carousel'];
	
	$return_string = '';
	
	if( $tx_carousel == 'no' ) {
   		$return_string .= '<div class="tx-blog tx-post-row tx-masonry">';
	} else
	{
   		$return_string .= '<div class="tx-blog tx-post-row tx-carousel" data-columns="'.$total_column.'">';		
	}
	
	wp_reset_query();
	global $post;
	
	$args = array(
		'posts_per_page' => $posts_per_page,
		'orderby' => 'date', 
		'order' => 'DESC',
		'ignore_sticky_posts' => 1,
		'category__in' => $post_in_cat, //use post ids		
	);

	if ($atts['show_pagination'] == 'yes' && $atts['carousel'] == 'no' )
	{	
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args['paged'] = $paged;
		$args['prev_text'] = __('&laquo;','tx');
		$args['next_text'] = __('&raquo;','tx');
		$args['show_all'] = false;
	}

	
	query_posts( $args );
   
	if ( have_posts() ) : while ( have_posts() ) : the_post();
	
		$post_comments = get_comments_number();
			
		$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );

		$thumb_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$thumb_image_url = aq_resize( $thumb_image_url[0], $width, $height, true, true, true );
	
		$return_string .= '<div class="tx-blog-item tx-post-col-'.$total_column.'"><div class="tx-border-box">';

		if ( has_post_thumbnail() ) { 
			$return_string .= '<div class="tx-blog-img"><a href="'.esc_url($full_image_url[0]).'" class="tx-colorbox">';
			$return_string .= '<img src="'.esc_url($thumb_image_url).'" alt="" class="blog-image" /></a><span class="tx-post-comm"><span>'.$post_comments.'</span></span></div>';
		} else
		{
			$return_string .= '<div class="tx-blog-imgpad"></div>';
		}
		
		$return_string .= '<div class="tx-post-content"><h3 class="tx-post-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		if ( $tx_category == "show" )
		{
			$return_string .= '<div class="tx-category">'.get_the_category_list( ', ' ).'</div>';	
		} else
		{
			$return_string .= '<div style="height: 16px;"></div>';
		}
		
		$return_string .= '<div class="tx-blog-content">'.get_the_excerpt().'</div>';

		$return_string .= '<div class="tx-meta">';
		$return_string .= '<span class="tx-author">By : <a href="'.esc_url( get_author_posts_url( get_the_author_meta("ID") ) ).'">'.get_the_author().'</a></span>';
		$return_string .= '<span class="tx-date"> | '.get_the_date('M j, Y').'</span>';
		$return_string .= '</div>';
		
		
		$return_string .= '</div></div></div>';		
		
		
	endwhile; else :
		$return_string .= '<p>Sorry, no posts matched your criteria.</p>';
	endif;
  
   	$return_string .= '</div>';

	if ($atts['show_pagination'] == 'yes' && $atts['carousel'] == 'no' ) {
		$return_string .= '<div class="nx-paging"><div class="nx-paging-inner">'.paginate_links( $args ).'</div></div>';
	}

   	wp_reset_query();

   	return $return_string;
}

// heading

function tx_heading_function($atts, $content = null) {
	
	//[tx_heading style=”default” heading_text=”Heading Text” tag=”h1″ size=”24″ margin=”24″]
	
   	$atts = shortcode_atts(array(
      	'style' => 'default',
      	'heading_text' => 'Heading Text',
      	'tag' => 'h2',
      	'size' => '24',	
      	'margin' => '24',
      	'align' => 'left',
      	'class' => '',
   	), $atts);
	
	$return_string ='';

   	$return_string .= '<div class="tx-heading" style="margin-bottom:'.$atts['margin'].'px; text-align: '.$atts['align'].';">';
   	$return_string .= '<'.$atts['tag'].' class="tx-heading-tag" style="font-size:'.$atts['size'].'px;">';	
	$return_string .= do_shortcode($atts['heading_text']);
   	$return_string .= '</'.$atts['tag'].'>';
   	$return_string .= '</div>';	

   	return $return_string;
}

// row

function tx_row_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'class' => '',
   	), $atts);
	
	$return_string ='';

   	$return_string .= '<div class="tx-row">';
	$return_string .= do_shortcode($content);
   	$return_string .= '</div>';

   	return $return_string;
}

// columns
function tx_column_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'size' => '1/4',
		'class' => '',
   	), $atts);
	
	$return_string ='';
	$column_class = 'tx-column-size-';
	
	if ( $atts['size'] == '1/1' ) 
	{
		$column_class .= '1-1';
	} elseif ( $atts['size'] == '1/2' )
	{
		$column_class .= '1-2';
	} elseif ( $atts['size'] == '1/3' )
	{
		$column_class .= '1-3';
	} elseif ($atts['size'] == '1/4' )
	{
		$column_class .= '1-4';
	} elseif ($atts['size'] == '2/3' )
	{
		$column_class .= '2-3';
	} elseif ($atts['size'] == '3/4' )
	{
		$column_class .= '3-4';
	}

   	$return_string .= '<div class="tx-column ' .$column_class. '">';
	$return_string .= do_shortcode($content);
   	$return_string .= '</div>';

   	return $return_string;
}

// spacer

function tx_spacer_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'class' => '',
		'size' => '16',
   	), $atts);
	
	$return_string ='';

   	$return_string .= '<div class="tx-spacer clearfix" style="height: '.esc_attr($atts['size']).'px"></div>';

   	return $return_string;
}

// devider [tx_devider size="24"]
function tx_divider_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'class' => '',
		'size' => '16',
   	), $atts);
	
	$return_string ='';

   	$return_string .= '<div class="tx-divider clearfix" style="margin-top: '.esc_attr($atts['size']).'px;margin-bottom: '.esc_attr($atts['size']).'px"></div>';

   	return $return_string;
}


// recent posts

function tx_testimonial_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'posts' => 6,
   	), $atts);
	
   
   	$posts_per_page = $atts['posts'];
	$posts_per_page = intval( $posts_per_page );
	
	$return_string = '';

   	$return_string .= '<div class="tx-testimonials">';
 
  
	wp_reset_query();
	global $post;
	
	$args = array(
		'posts_per_page' => $posts_per_page,
		'post_type' => 'testimonials',
		'orderby' => 'date', 
		'order' => 'DESC'
	);

	query_posts( $args );   
   
	if ( have_posts() ) : while ( have_posts() ) : the_post();
	
		$testi_name = esc_attr(rwmb_meta('tx_testi_name'));
		$testi_desig = esc_attr(rwmb_meta('tx_testi_desig'));
		$testi_organ = esc_attr(rwmb_meta('tx_testi_company'));				
	
		$return_string .= '<div class="tx-testi-item">';
		$return_string .= '<span class="tx-testi-text">'.get_the_content().'</span>';
		$return_string .= '<span class="tx-testi-name">'.$testi_name.'</span>';
		$return_string .= '<span class="tx-testi-desig">'.$testi_desig.', </span>';
		$return_string .= '<span class="tx-testi-org">'.$testi_organ.'</span>';						
		$return_string .= '</div>';
	endwhile; else :
		$return_string .= '<p>Sorry, no posts matched your criteria.</p>';
	endif;
  
   	$return_string .= '</div>';

   	wp_reset_query();
   	return $return_string;
}


// button 

function tx_button_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'style' => '',
		'text' => '',
		'url' => '',
		'color' => '',
		'textcolor' => '',				
		'class' => '',
   	), $atts);
	
	$return_string ='';

   	$return_string .= '<a class="tx-button" href="'.esc_url($atts['url']).'" style="color: '.esc_attr($atts['textcolor']).'; background-color: '.esc_attr($atts['color']).'">'.esc_attr($atts['text']).'</a>';

   	return $return_string;
}

// Call to act

function tx_calltoact_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'button_text' => '',
		'url' => '',
		'class' => '',
   	), $atts);
	
	$cta_text = esc_attr($content);
	
	$return_string ='';
	
   	$return_string .= '<div class="tx-cta" style=""><div class="tx-cta-text">'.$content.'</div><a href="'.esc_url($atts['url']).'" class="cta-button">'.esc_attr($atts['button_text']).'</a></div>';

   	return $return_string;
}



// Call to act [tx_services title="Services Title" icon="fa-heart"]Services content[/tx_services]

function tx_services_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'style' => 'default',	
      	'title' => '',
		'icon' => '',
		'class' => '',
   	), $atts);
	
	$style_class = '';
	
	$service_text = do_shortcode($content);
	$service_icon = esc_attr($atts['icon']);
	$service_title = esc_attr($atts['title']);
	$style_class = $atts['style'];
	
	$return_string ='';
	
   	$return_string .= '<div class="tx-service '.$style_class.'" style="">';
	$return_string .= '<div class="tx-service-icon"><span><i class="fa '.$service_icon.'"></i></span></div>';
	$return_string .= '<div class="tx-service-title">'.$service_title.'</div>';
	$return_string .= '<div class="tx-service-text">'.$service_text.'</div>';		
	$return_string .= '</div>';

   	return $return_string;
}

// portfolio [tx_portfolio items="6" columns="3"]

function tx_portfolio_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'style' => 'default',
      	'items' => 4,
      	'columns' => 4,
		'hide_cat' => 'no',
		'hide_excerpt' => 'no',
		'show_pagination' => 'no',
		'carousel' => 'no',
   	), $atts);
	
   
   	$style_class = '';
   	$posts_per_page = intval( $atts['items'] );
   	$total_column = intval( $atts['columns'] );
	$tx_carousel = $atts['carousel'];
	
	$width = 600;
	$height = 480;	
	
	if ( $atts['style'] == 'gallery' )
	{
		$style_class = 'folio-style-gallery';
	}

	
	$return_string = '';

	if( $tx_carousel == 'no' ) {
   		$return_string .= '<div class="tx-portfolio tx-post-row tx-masonry '.$style_class.'">';
	} else
	{
   		$return_string .= '<div class="tx-portfolio tx-post-row tx-carousel" data-columns="'.$total_column.'">';		
	}
 
  
	wp_reset_query();
	global $post;
	
	$args = array(
		'posts_per_page' => $posts_per_page,
		'post_type' => 'portfolio',
		'orderby' => 'date',
		'order' => 'DESC'
	);

	if ($atts['show_pagination'] == 'yes' && $atts['carousel'] == 'no' )
	{
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args['paged'] = $paged;
		$args['prev_text'] = __('&laquo;','tx');
		$args['next_text'] = __('&raquo;','tx');
	}

	query_posts( $args );

	if ( have_posts() ) : while ( have_posts() ) : the_post();
	
		$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		
		$thumb_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$thumb_image_url = aq_resize( $thumb_image_url[0], $width, $height, true, true, true );		
	

		$return_string .= '<div class="tx-portfolio-item tx-post-col-'.$total_column.'"><div class="tx-border-box">';
		

		if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
			$return_string .= '<div class="tx-folio-img">';
			$return_string .= '<div class="tx-folio-img-wrap"><img src="'.esc_url($thumb_image_url).'" alt="" class="folio-img" /></div>';
			$return_string .= '<div class="folio-links"><span>';	
			$return_string .= '<a href="'.esc_url(get_permalink()).'" class="folio-linkico"><i class="fa fa-link"></i></a>';	
			$return_string .= '<a href="'.esc_url($full_image_url[0]).'" class="tx-colorbox folio-zoomico"><i class="fa fa-search-plus"></i></a>';										
			$return_string .= '</span></div>';			
			$return_string .= '</div>';			
		} 

		$return_string .= '<span class="folio-head">';
		$return_string .= '<h3 class="tx-folio-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		if ( $atts['hide_cat'] == 'no' ) { // check if the post has a Post Thumbnail assigned to it.
			$return_string .= '<div class="tx-folio-category">'.tx_folio_term( 'portfolio-category' ).'</div>';
		} else
		{
			$return_string .= '<div style="display: block; clear: both; height: 16px;"></div>';
		}
		$return_string .= '</span>';
		if ( $atts['hide_excerpt'] == 'no' && $atts['style'] != 'gallery' ) { // check if the post has a Post Thumbnail assigned to it.
			$return_string .= '<div class="tx-folio-content">'.get_the_excerpt().'</div>';
		}
			
		$return_string .= '</div></div>';
	endwhile; else :
		$return_string .= '<p>Sorry, no posts matched your criteria.</p>';
	endif;
  
   	$return_string .= '</div>';
	
	if ($atts['show_pagination'] == 'yes' && $atts['carousel'] == 'no' )
	{	
		$return_string .= '<div class="nx-paging"><div class="nx-paging-inner">'.paginate_links( $args ).'</div></div>';
	}
	

   	wp_reset_query();
	
   	return $return_string;
}

// button 

function tx_prodscroll_function($atts, $content = null) {
	
	//[tx_prodscroll type="products" ids="21,28,54,87" columns="4" items="8"]
	
   	$atts = shortcode_atts(array(
      	'type' => 'products',
		'ids' => '',
		'columns' => '4',
		'items' => '8',
		'class' => '',
   	), $atts);
	
	$return_string ='';
	$prod_shortcode = '';
	
	
	if ( !empty($atts['ids']) && ( $atts['type'] == 'product_categories' || $atts['type'] == 'products' ))
	{
		if ( $atts['type'] == 'product_categories' )
		{
			$prod_shortcode = '['.$atts['type'].' number="'.$atts['items'].'" columns="'.$atts['columns'].'" ids="'.$atts['ids'].'"]';
		} else
		{
			$prod_shortcode = '['.$atts['type'].' per_page="'.$atts['items'].'" columns="'.$atts['columns'].'" ids="'.$atts['ids'].'"]';
		}
	} else
	{
		if ( $atts['type'] == 'product_categories' )
		{
			$prod_shortcode = '['.$atts['type'].' number="'.$atts['items'].'" columns="'.$atts['columns'].'"]';
		} else
		{
			$prod_shortcode = '['.$atts['type'].' per_page="'.$atts['items'].'" columns="'.$atts['columns'].'"]';
		}		
	}
	
	$return_string = '<div class="tx-prod-carousel" data-columns="'.$atts['columns'].'">'.do_shortcode( $prod_shortcode ).'</div>';

   	return $return_string;
}


// recent posts [tx_blog items="3" colums="6" showcat="show" category_id="8,9"]

function tx_slider_function($atts, $content = null) {
	
   	$atts = shortcode_atts(array(
      	'items' => 10,
      	'category' => '',
		'delay' => 8000,
      	'class' => '',								
   	), $atts);
	
	$return_string = '';
	$cat_slug = '';
	
	if( !empty($atts['category']) )
	{
		$cat_slug = $atts['category'];
	}

	$posts_per_page = intval( $atts['items'] );
	$tx_class = $atts['class'];
	$tx_delay = $atts['delay'];
	
	
	$return_string .= '<div class="tx-slider" data-delay="'.$tx_delay.'">';		
	
	
	wp_reset_query();
	global $post;
	
	$args = array(
		'post_type' => 'itrans-slider',
		'posts_per_page' => $posts_per_page,
		'orderby' => 'date', 
		'order' => 'DESC',
		'ignore_sticky_posts' => 1,
		'itrans-slider-category' => $cat_slug, //use post ids				
	);

	$full_image_url = '';
	$large_image_url = '';
	$image_url = '';
	$width = 1200;
	$height = 420;

	query_posts( $args );
   
	if ( have_posts() ) : while ( have_posts() ) : the_post();
	
		$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );	
		$image_url = tx_image_resize( $full_image_url[0], $width, $height, true, true );

		$slide_link_text = rwmb_meta('tx_slide_link_text');
		$show_link_url = rwmb_meta('tx_slide_link_url');		
		
		$return_string .= '<div class="tx-slider-item">';
		$return_string .= '<div class="tx-slider-box">';
		
		if ( has_post_thumbnail() ) { 
			$return_string .= '<div class="tx-slider-img"><a href="'.esc_url($large_image_url[0]).'" class="tx-colorbox">';
			$return_string .= '<img src="'.esc_url($image_url["url"]).'" alt="" class="blog-image" /></a>';
			$return_string .= '</div>';
		} 
		/**/
		$return_string .= '<div class="tx-slide-content"><div class="tx-slide-content-inner">';
		$return_string .= '<h3 class="tx-slide-title">'.get_the_title().'</h3>';		
		$return_string .= '<div class="tx-slide-details"><p>'.tx_custom_excerpt(32).'</p></div>';
		$return_string .= '<div class="tx-slide-button"><a href="'.esc_url( $show_link_url ).'">'.esc_attr( $slide_link_text ).'</a></div>';		
		$return_string .= '</div></div></div></div>';		
		
		
	endwhile; else :
		$return_string .= '<p>Sorry, no slider matched your criteria.</p>';
	endif;
  
   	$return_string .= '</div>';

   	wp_reset_query();
   	return $return_string;
}


function tx_register_shortcodes(){
	add_shortcode('tx_recentposts', 'tx_recentposts_function');
	add_shortcode('tx_row', 'tx_row_function');
	add_shortcode('tx_column', 'tx_column_function');
	add_shortcode('tx_spacer', 'tx_spacer_function');	
	add_shortcode('tx_testimonial', 'tx_testimonial_function');	
	add_shortcode('tx_button', 'tx_button_function');
	add_shortcode('tx_calltoact', 'tx_calltoact_function');
	add_shortcode('tx_services', 'tx_services_function');
	add_shortcode('tx_portfolio', 'tx_portfolio_function');	
	add_shortcode('tx_blog', 'tx_blog_function');
	add_shortcode('tx_divider', 'tx_divider_function');	
	add_shortcode('tx_prodscroll', 'tx_prodscroll_function');
	add_shortcode('tx_heading', 'tx_heading_function');
	add_shortcode('tx_slider', 'tx_slider_function');								
}

add_action( 'init', 'tx_register_shortcodes');



