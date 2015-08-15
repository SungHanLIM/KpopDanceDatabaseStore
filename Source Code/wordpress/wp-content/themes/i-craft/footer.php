<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package i-craft
 * @since i-craft 1.0
 */
?>

		</div><!-- #main -->
		<footer id="colophon" class="site-footer" role="contentinfo">
        	<div class="footer-bg">
                <div calss="widget-wrap">
                    <?php get_sidebar( 'main' ); ?>
                </div>
			</div>
			<div class="site-info">
                <div class="copyright">
                	<?php esc_attr_e( 'Copyright &copy;', 'i-craft' ); ?>  <?php bloginfo( 'name' ); ?>
                </div>            
            	<div class="credit-info">
			<p><a href="http://61.252.147.56/index.php/terms-conditions/">이용약관</a> | <a href="http://61.252.147.56/index.php/privacy-policy/">개인정보보호정책</a> | <a href="http://61.252.147.56/index.php/site-map/">사이트맵</a></p>
                </div>

			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>