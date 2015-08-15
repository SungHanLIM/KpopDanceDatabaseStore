<?php
	/*
	*
	*	nx woocommerce Functions
	*	------------------------------------------------
	*	nx Framework v 1.0
	*
	*	nx_woo_bar()
	*
	*/
	
	
// Change number or products per row to 3
add_filter('loop_shop_columns', 'icraft_loop_columns');
if (!function_exists('icraft_loop_columns')) {
	function icraft_loop_columns() {
		
		global  $icraft_data;
		
		$woo_columns = 4;
		
		if ( !empty($icraft_data['woo-archive-columns']) )
		{
			$woo_columns = $icraft_data['woo-archive-columns'];
		}
		
		return $woo_columns; // 3 products per row
	}
}

// Display 12 products per page.
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );


/**
* Change number of related products on product page
* Set your own value for 'posts_per_page'
*
*/

add_filter( 'woocommerce_output_related_products_args', 'icraft_related_products_args' );
function icraft_related_products_args( $args ) {
	
	global  $icraft_data;
		
	$woo_columns = 4;
		
	if ( !empty($icraft_data['woo-archive-columns']) )
	{
		$woo_columns = $icraft_data['woo-archive-columns'];
	}
			
	$args['posts_per_page'] = $woo_columns;
	$args['posts_per_page'] = 10;	 
	$args['columns'] = $woo_columns; 
	return $args;
}


/* TOP BAR Shopping Cart
================================================== */
if (!function_exists('icraft_top_cart')) {
	function icraft_top_cart() {
				
		global $woocommerce;
		$nx_top_cart = '';
			
		$nx_top_cart .= '<div class="cartdrop widget_shopping_cart nx-animate">';
		$nx_top_cart .= '<div class="widget_shopping_cart_content">';
		$nx_top_cart .= '<ul class="cart_list product_list_widget">';
		$nx_top_cart .= '</ul>';
		$nx_top_cart .= '</div>';
		$nx_top_cart .= '</div>';
			
		return $nx_top_cart;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Adding login logout menu item */
/*-----------------------------------------------------------------------------------*/
 
add_filter( 'wp_nav_menu_items', 'icraft_add_loginout_link', 10, 2 );
function icraft_add_loginout_link( $items, $args ) {
		
	$hide_login = of_get_option('hide_login');
		
	if( empty($hide_login) ){	
		if (is_user_logged_in() && $args->theme_location == 'primary') {
			$items .= '<li class="menu-item nx-mega-menu"><a href="'. wp_logout_url() .'">Log Out</a></li>';
		}
		elseif (!is_user_logged_in() && $args->theme_location == 'primary') {
			$items .= '<li class="menu-item nx-mega-menu"><a href="'. site_url('wp-login.php') .'">Log In</a></li>';
		}
	}
	return $items;

}

// archive remove title
//add_filter( 'woocommerce_show_page_title', function() { return false; } );
//add_filter('woocommerce_show_page_title',false);

add_filter( 'woocommerce_show_page_title' , 'woo_hide_page_title' );

/**
 * woo_hide_page_title
 *
*/
function woo_hide_page_title() {
	return false;
}


// Disable default style
add_filter( 'woocommerce_enqueue_styles', 'icraft_dequeue_styles' );
function icraft_dequeue_styles( $enqueue_styles ) {
	
	wp_enqueue_style( 'woocommerce-general', get_template_directory_uri() . '/css/nx-woo.css', array(), '2.09' );
	
	unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
	//unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
	//unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation

	
	
	return $enqueue_styles;
}

// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('add_to_cart_fragments', 'icraft_header_add_to_cart_fragment');

function icraft_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;
	
	ob_start();
	
	?>
	<span class="cart-counts"><?php echo sprintf($woocommerce->cart->cart_contents_count); ?></span>
	<?php
	
	$fragments['.cart-counts'] = ob_get_clean();
	
	return $fragments;
	
}