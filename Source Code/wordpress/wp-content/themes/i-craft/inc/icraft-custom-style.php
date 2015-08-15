<?php
	
	/*
	*
	*	nx Theme Functions
	*	------------------------------------------------
	*	nx Framework v 1.0
	*
	*	nx_custom_styles()
	*	nx_custom_script()
	*
	*/

 	/* CUSTOM CSS OUTPUT
 	================================================== */
 	if (!function_exists('nx_custom_styles')) { 
		function nx_custom_styles() {
			
			global  $icraft_data;
			$custom_css = "";
			$body_font_size = "13";
			$body_line_height = "24";
			$menu_font_size = "13";
			$primary_color = "#95C837";

			$primary_color = esc_attr(of_get_option('itrans_primary_color', '#dd3333'));
						

			echo '<style type="text/css">'. "\n";
			
			//echo 'body{font-size: '. $body_font_size .'px;line-height: '. $body_line_height .'px;}';
			
			//echo '.nav-container > ul li a {font-size: '. $menu_font_size .'px;}';
			
			echo 'a,a:visited,.blog-columns .comments-link a:hover {color: '.$primary_color.';}';

			echo 'input:focus,textarea:focus {border: 1px solid '.$primary_color.';}';
			
			echo 'button,input[type="submit"],input[type="button"],input[type="reset"],.nav-container .current_page_item > a > span,.nav-container .current_page_ancestor > a > span,.nav-container .current-menu-item > a span,.nav-container .current-menu-ancestor > a > span,.nav-container li a:hover span {background-color: '.$primary_color.';}';

			echo '.nav-container li:hover > a,.nav-container li a:hover {color: '.$primary_color.';}';

			echo '.nav-container .sub-menu,.nav-container .children,.header-icons.woocart .cartdrop.widget_shopping_cart.nx-animate {border-top: 2px solid '.$primary_color.';}';

			echo '.ibanner,.da-dots span.da-dots-current,.tx-cta a.cta-button,.header-iconwrap .header-icons.woocart > a .cart-counts {background-color: '.$primary_color.';}';

			echo '#ft-post .entry-thumbnail:hover > .comments-link,.tx-folio-img .folio-links .folio-linkico,.tx-folio-img .folio-links .folio-zoomico {background-color: '.$primary_color.';}';

			echo '.entry-header h1.entry-title a:hover,.entry-header > .entry-meta a:hover,.header-icons.woocart .cartdrop.widget_shopping_cart li a:hover {color: '.$primary_color.';}';

			echo '.featured-area div.entry-summary > p > a.moretag:hover {background-color: '.$primary_color.';}';

			echo '.site-content div.entry-thumbnail .stickyonimg,.site-content div.entry-thumbnail .dateonimg,.site-content div.entry-nothumb .stickyonimg,.site-content div.entry-nothumb .dateonimg {background-color: '.$primary_color.';}';

			echo '.entry-meta a,.entry-content a,.comment-content a,.entry-content a:visited {color: '.$primary_color.';}';

			echo '.format-status .entry-content .page-links a,.format-gallery .entry-content .page-links a,.format-chat .entry-content .page-links a,.format-quote .entry-content .page-links a,.page-links a {background: '.$primary_color.';border: 1px solid '.$primary_color.';color: #ffffff;}';

			echo '.format-gallery .entry-content .page-links a:hover,.format-audio .entry-content .page-links a:hover,.format-status .entry-content .page-links a:hover,.format-video .entry-content .page-links a:hover,.format-chat .entry-content .page-links a:hover,.format-quote .entry-content .page-links a:hover,.page-links a:hover {color: '.$primary_color.';}';

			echo '.iheader.front {background-color: '.$primary_color.';}';

			echo '.navigation a,.tx-post-row .tx-folio-title a:hover,.tx-blog .tx-blog-item h3.tx-post-title a:hover {color: '.$primary_color.';}';

			echo '.paging-navigation div.navigation > ul > li a:hover,.paging-navigation div.navigation > ul > li.active > a {color: '.$primary_color.';	border-color: '.$primary_color.';}';

			echo '.comment-author .fn,.comment-author .url,.comment-reply-link,.comment-reply-login,.comment-body .reply a,.widget a:hover {color: '.$primary_color.';}';

			echo '.widget_calendar a:hover {	background-color: '.$primary_color.';	color: #ffffff;	}';

			echo '.widget_calendar td#next a:hover,.widget_calendar td#prev a:hover {	background-color: '.$primary_color.';color: #ffffff;}';

			echo '.site-footer div.widget-area .widget a:hover {color: '.$primary_color.';}';

			echo '.site-main div.widget-area .widget_calendar a:hover,.site-footer div.widget-area .widget_calendar a:hover {	background-color: '.$primary_color.';color: #ffffff;}';
						
			echo '.widget a:visited { color: #373737;}';

			echo '.widget a:hover,.entry-header h1.entry-title a:hover,.error404 .page-title:before,.tx-service-icon span i {color: '.$primary_color.';}';

			echo '.da-dots > span > span {background-color: '.$primary_color.';}';

			echo '.iheader,.format-status,.tx-service:hover .tx-service-icon span {background-color: '.$primary_color.';}';
			
			echo '.tx-cta {border-left: 6px solid '.$primary_color.';}';
			
			echo '.paging-navigation #posts-nav > span:hover, .paging-navigation #posts-nav > a:hover, .paging-navigation #posts-nav > span.current, .paging-navigation #posts-nav > a.current, .paging-navigation div.navigation > ul > li a:hover, .paging-navigation div.navigation > ul > li > span.current, .paging-navigation div.navigation > ul > li.active > a {border: 1px solid '.$primary_color.';color: '.$primary_color.';}';
			
			echo '.entry-title a { color: #141412;}';
			
			echo '.tx-service-icon span { border: 2px solid '.$primary_color.';}';
			
			echo '.ibanner .da-slider .owl-item .da-link { background-color:'.$primary_color.'; color: #FFF; }';
			
			echo '.ibanner .da-slider .owl-item .da-link:hover { background-color: #373737; color: #FFF; }';

			
			
			
			if ($custom_css) {
			echo "\n".'/* =============== user styling =============== */'."\n";
			echo $custom_css;
			}
			
			// CLOSE STYLE TAG
			echo "</style>". "\n";
		}
	
		add_action('wp_head', 'nx_custom_styles');
	}
	
	/* CUSTOM JS OUTPUT
	================================================== 
	function nx_custom_script() {
		
		global  $icraft_data;
		
		$custom_js = $icraft_data['custom_js'];
		
		if ($custom_js) {			
			echo "\n<script>\n".$custom_js."\n</script>\n";			
		}
	}
	
	add_action('wp_footer', 'nx_custom_script');
		
*/
?>