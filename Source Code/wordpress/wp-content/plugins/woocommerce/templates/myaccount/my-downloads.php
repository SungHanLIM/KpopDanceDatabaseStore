<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $downloads = WC()->customer->get_downloadable_products() ) : ?>

	<?php do_action( 'woocommerce_before_available_downloads' ); ?>

	
	
	<!-- 테이블 구현 -->
	<div class="panel panel-warning">
      
      <div class="panel-heading">
	  <b><?php echo apply_filters( 'woocommerce_my_account_my_downloads_title', __( 'Available downloads', 'woocommerce' ) ); ?></b>
	  </div>

      
      <table class="table">
	  
        <tbody>
        
		 <?php foreach ( $downloads as $download ) : ?>
		
          <tr>
			<?php
					do_action( 'woocommerce_available_download_start', $download );
			?>
			<td>
			<ul class="digital-downloads"><li>
			<?php
					echo apply_filters( 'woocommerce_available_download_link', '<a href="' . esc_url( $download['download_url'] ) . '">' . $download['download_name'] . '</a>', $download );

					do_action( 'woocommerce_available_download_end', $download );
			?>
			</li></ul>
			</td>	
			
			<td>
			<?php
					if ( is_numeric( $download['downloads_remaining'] ) )
						echo apply_filters( 'woocommerce_available_download_count', '<span class="count">' . sprintf( _n( '%s download remaining', '%s downloads remaining', $download['downloads_remaining'], 'woocommerce' ), $download['downloads_remaining'] ) . '</span> ', $download );
			?>
			</td>		
			
		  
          </tr>
		  
		 <?php endforeach; ?>
		
        </tbody>
	  
      </table>
    </div>
	<!-- 테이블 구현 끝 -->
	

	<?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php endif; ?>
