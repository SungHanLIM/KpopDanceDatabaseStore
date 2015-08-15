<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package i-craft
 * @since i-craft 1.0
 */
?>
<?php


$background = of_get_option('itrans_background_style');
$hide_cart = of_get_option('hide_cart');
$bg_cover = of_get_option('bg_cover', '1');
				
$bacground_style = '';

if ($background) {
	
   	if ($background['color']) {		
		$bacground_style .= 'background-color: '.$background['color'].'; ' ;
   	}
		
   	if ($background['image']) {		
		$bacground_style .= 'background-image: URL('.$background['image'].'); ' ;
		foreach ($background as $i=>$param)
		{
        	$bacground_style .= 'background-'.$i .': '.$param.'; ' ;
        }
   	}
	
	if( $bg_cover == '1' )
	{
		$bacground_style .= 'background-size: cover;' ;			
	}
	
	
} else
{
	$bacground_style .= 'background-image: URL('.get_template_directory_uri() . '/images/bg7.jpg'.'); ' ;
	$bacground_style .= 'background-repeat: no-repeat;' ;
	$bacground_style .= 'background-size: cover;' ;	
	$bacground_style .= 'background-attachment: fixed;' ;		
}

global $post; 

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">

	<?php    
    if ( ! function_exists( '_wp_render_title_tag' ) ) :
        function icraft_render_title() {
    ?>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php
        }
        add_action( 'wp_head', 'icraft_render_title' );
    endif;    
    ?>    
    
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
	
	<!-- Bootstrap :: Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

	<!-- Add to Cart 버튼 색상 변경 css -->
	<style>
	.woocommerce-page button.button.alt, .woocommerce-page input.button.alt {
    	background: #FFFFFF;
	}
	</style>
	<!-- Add to Cart 버튼 색상 변경 css 끝 -->

        <!-- jQuery 영역 - tab.js -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>	
</head>
<body <?php body_class(); ?> style="<?php echo $bacground_style; ?>">

	<div id="page" class="hfeed site">
    	
        <?php if ( of_get_option('top_bar_phone') || of_get_option('top_bar_email') || icraft_social_icons() ) : ?>
    	<div id="utilitybar" class="utilitybar">
        	<div class="ubarinnerwrap">
                <div class="socialicons">
                    <?php echo icraft_social_icons(); ?>
                </div>
                <?php if ( of_get_option('top_bar_phone') ) : ?>
                <div class="topphone">
                    <i class="topbarico genericon genericon-phone"></i>
                    <?php if ( of_get_option('top_bar_phone') ) : ?>
                        <?php _e('Call us : ','i-craft'); ?> <?php echo esc_attr(of_get_option('top_bar_phone')); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ( of_get_option('top_bar_email') ) : ?>
                <div class="topphone">
                    <i class="topbarico genericon genericon-mail"></i>
                    <?php if (of_get_option('top_bar_email') ) : ?>
                        <?php _e('Mail us : ','i-craft'); ?> <?php echo sanitize_email(of_get_option('top_bar_email')); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>                
            </div> 
        </div>
        <?php endif; ?>
        
        <div class="headerwrap">
            <header id="masthead" class="site-header" role="banner">
         		<div class="headerinnerwrap">
					<?php if (of_get_option('itrans_logo_image')) : ?>
                        <a class="home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                            <span><img src="<?php echo of_get_option('itrans_logo_image'); ?>" alt="<?php bloginfo( 'name' ); ?>" /></span>
                        </a>
                    <?php else : ?>
                        <span id="site-titlendesc">
                            <a class="home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                                <h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
                                <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>   
                            </a>
                        </span>
                    <?php endif; ?>	
        
                    <div id="navbar" class="navbar">
                        <nav id="site-navigation" class="navigation main-navigation" role="navigation">
                            <h3 class="menu-toggle"><?php _e( 'Menu', 'i-craft' ); ?></h3>
                            <a class="screen-reader-text skip-link" href="#content" title="<?php esc_attr_e( 'Skip to content', 'i-craft' ); ?>"><?php _e( 'Skip to content', 'i-craft' ); ?></a>
                            <?php 
								if ( has_nav_menu(  'primary' ) ) {
										wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container_class' => 'nav-container', 'container' => 'div' ) );
									}
									else
									{
										wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-container' ) ); 
									}
								?>
							
                        </nav><!-- #site-navigation -->

                        <div class="header-iconwrap">
                        <?php
                        global $woocommerce;
                        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && empty($hide_cart) ) {
                        ?>
                            <div class="header-icons woocart">
                                <a href="<?php echo $woocommerce->cart->get_cart_url() ?>" >
                                    <span class="show-sidr"><?php _e('Cart','i-craft'); ?></span>
                                    <span class="genericon genericon-cart"></span>
                                    <span class="cart-counts"><?php echo sprintf($woocommerce->cart->cart_contents_count); ?></span>
                                </a>
                                <?php echo icraft_top_cart(); ?>
                             </div>
                        <?php	
                        }
                        ?>
                        </div>
                                    
                        <div class="topsearch">
                            <?php get_search_form(); ?>
                        </div>
                    </div><!-- #navbar -->
                    <div class="clear"></div>
                </div>
            </header><!-- #masthead -->
        </div>
        
        <!-- #Banner -->
	<!-- 배너 CSS 설정 부분 -->
	<style>
	.ibanner .da-slider h2 {
		background-color: rgba(127, 28, 28, 0);
		font-size: 20%;
	}
	
	.ibanner .da-slider p {
		background-color: rgba(127, 28, 28, 0);
	}

	.nx-slider-container {
		text-align: center;
		font-size: 22px;
	}
	
	.slider-content-wrap {
		text-align: center;
	}

	
	</style>
	
	<!-- 배너 CSS 설정 부분 끝 -->
	
        <?php
		
		$hide_title = rwmb_meta('icraft_hidetitle');
		$show_slider = rwmb_meta('icraft_show_slider');
		$other_slider = rwmb_meta('icraft_other_slider');
		$custom_title = rwmb_meta('icraft_customtitle');
		
		$hide_front_slider = of_get_option('hide_front_slider');
		$other_front_slider = of_get_option('other_front_slider');
		$itrans_slogan = of_get_option('itrans_slogan');
		

		//echo '<div style="position: relative;">'. do_shortcode('[tx_slider items="4" category_id="" delay="8000" class=""]').'</div>';
		
		if($other_slider) :
		?>
		
        <div class="other-slider" style="">
	       	<?php echo do_shortcode( $other_slider ) ?>
        </div>
        <?php //elseif ( is_front_page() )  : ?>
		<?php	
		elseif ( is_home() && !is_paged() || $show_slider ) : 
		?>
            <?php if (!empty($other_front_slider)) : ?>
            <?php echo do_shortcode( $other_front_slider ) ?>
        	<?php elseif (!$hide_front_slider) : ?>
		
	     <?php icraft_ibanner_slider(); ?>

        	<?php endif; ?>
            
        <?php elseif(!$hide_title) : ?>
        
        <div class="iheader" style="">
        	<div class="titlebar">
            	
                <?php
					if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && is_shop() )
					{
						echo '<h1 class="entry-title">';
						woocommerce_page_title();
						echo '</h1>';
						
					} elseif ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && is_product_category() )
					{
						echo '<h1 class="entry-title">';
						woocommerce_page_title();
						echo '</h1>';
						
					} elseif ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && is_product() && empty($custom_title) )
					{
						echo '<h1 class="entry-title">';
						the_title();
						echo '</h1>';
						
					} elseif( is_archive() )
					{
						echo '<h1 class="entry-title">';
							if ( is_day() ) :
								printf( __( 'Daily Archives: %s', 'i-craft' ), get_the_date() );
							elseif ( is_month() ) :
								printf( __( 'Monthly Archives: %s', 'i-craft' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'i-craft' ) ) );
							elseif ( is_year() ) :
								printf( __( 'Yearly Archives: %s', 'i-craft' ), get_the_date( _x( 'Y', 'yearly archives date format', 'i-craft' ) ) );
							elseif ( is_category() ) :	
								printf( __( 'Category Archives: %s', 'i-craft' ), single_cat_title( '', false ) );		
							else :
								_e( 'Archives', 'i-craft' );
							endif;                						
						echo '</h1>';
					} elseif ( is_search() )
					{
						echo '<h1 class="entry-title">';
							printf( __( 'Search Results for: %s', 'i-craft' ), get_search_query() );					
						echo '</h1>';
					} else
					{
						if ( !empty($custom_title) )
						{
							echo '<h1 class="entry-title">'.esc_attr($custom_title).'</h1>';
						}
						else
						{
							echo '<h1 class="entry-title">';
							the_title();
							echo '</h1>';
						}						
					}
					
					//echo rwmb_meta('icraft_customtitle');

					/**/
            	?>
				<?php 
				
					$hide_breadcrumb = rwmb_meta('icraft_hide_breadcrumb');
					
                    if(function_exists('bcn_display') && !$hide_breadcrumb )
                    {
				?>
                	<div class="nx-breadcrumb">
                <?php
                        bcn_display();
				?>
                	</div>
                <?php		
                    } 
                ?>               
            	
            </div>
        </div>
        
		<?php endif; ?>
		<div id="main" class="site-main">