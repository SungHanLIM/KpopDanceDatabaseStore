<?php
/**
 * Grouped product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

$parent_product_post = $post;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<!-- 상품 정보 중앙정렬 CSS-->
<!--
<style>
	#outer{display:table;width:80%;height:100px}
	.inner{display:table-cell;vertical-align:middle;text-align:center}
</style>
-->


<!-- 그룹상품 리스트 display Section -->

<form class="cart" method="post" enctype='multipart/form-data'>
	<table cellspacing="0" class="group_table">
		<tbody>
			<?php
				
				// 상품이 있는 수량만큼 반복
				foreach ( $grouped_products as $product_id ) :
				
					$product = wc_get_product( $product_id );
					// echo $product_id;
					if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $product->is_in_stock() ) {
						continue;
					}

					$post    = $product->post;
					
					setup_postdata( $post );
					
					?>
					
					<tr>
					
						<!-- 상품수량 출력 부분 -->
						<td>
							<label>
							<?php if ( $product->is_sold_individually() || ! $product->is_purchasable() ) : ?>
								<?php woocommerce_template_loop_add_to_cart(); ?>
							<?php else : ?>
								<?php
									$quantites_required = true;
									woocommerce_quantity_input( array(
										'input_name'  => 'quantity[' . $product_id . ']',
										'input_value' => ( isset( $_POST['quantity'][$product_id] ) ? wc_stock_amount( $_POST['quantity'][$product_id] ) : 0 ),
										'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
										'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
									) );
								?>
							<?php endif; ?>
							</label>
						</td>
						<!-- 상품수량 출력 부분 끝 -->
						
						<!-- 상품명 출력 부분 -->
						<td class="label">
							<label for="product-<?php echo $product_id; ?>">
								
								<?php echo $product->is_visible() ? '<a href="' . get_permalink() . '">' . get_the_title() . '</a>' : get_the_title(); ?>
								
							</label>
						</td>
						<!-- 상품명 출력 부분 끝 -->
						
						<?php do_action ( 'woocommerce_grouped_product_list_before_price', $product ); ?>

						<!-- 상품가격 출력 부분 -->
						<!-- 상품가격 $product->get_price_html(); -->
						
						<td class="price">
							<?php
								echo $product->get_price_html();

								if ( $availability = $product->get_availability() ) {
									$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';
									echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
								}
							?>
						</td>
						<!-- 상품가격 출력 부분 끝 -->
						
					</tr>
					<?php
				endforeach;

				// Reset to parent grouped product
				$post    = $parent_product_post;
				$product = wc_get_product( $parent_product_post->ID );
				setup_postdata( $parent_product_post );
			?>
		</tbody>
	</table>

	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

	<?php if ( $quantites_required ) : ?>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<button type="submit" class="single_add_to_cart_button button alt"><?php echo $product->single_add_to_cart_text(); ?></button>
		
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>	

	<?php endif; ?>
	
</form>
<!-- 그룹상품 리스트 display Section 끝 -->


<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
