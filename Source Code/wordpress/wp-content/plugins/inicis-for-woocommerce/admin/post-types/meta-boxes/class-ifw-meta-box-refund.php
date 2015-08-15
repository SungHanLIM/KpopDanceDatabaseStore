<?php
/*
=====================================================================================
                INICIS for WooCommerce / Copyright 2014 - 2015 by CodeM
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

    워드프레스 버전 : WordPress 4.2.3

    우커머스 버전 : WooCommerce 2.3.13


  [ 코드엠 플러그인 라이센스 규정 ]

    1. 코드엠에서 개발한 워드프레스 우커머스용 결제 플러그인의 저작권은 ㈜코드엠에게 있습니다.

    2. 당사의 플러그인의 설치, 인증에 따른 절차는 플러그인 라이센스 규정에 동의하는 것으로 간주합니다.

    3. 결제 플러그인의 사용권은 쇼핑몰 사이트의 결제 서비스 사용에 국한되며, 그 외의 상업적 사용을 금지합니다.

    4. 결제 플러그인의 소스 코드를 복제 또는 수정 및 재배포를 금지합니다. 이를 위반 시 민형사상의 책임을 질 수 있습니다.

    5. 플러그인 사용에 있어 워드프레스, 테마, 플러그인과의 호환 및 버전 관리의 책임은 사이트 당사자에게 있습니다.

    6. 위 라이센스는 개발사의 사정에 의해 임의로 변경될 수 있으며, 변경된 내용은 해당 플러그인 홈페이지를 통해 공개합니다.

=====================================================================================
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class IFW_Meta_Box_Refund {
        public static function output( $post ) {
  		global $woocommerce, $inicis_payment;
		$woocommerce->payment_gateways();

	    $order = new WC_Order($post->ID);
		$payment_method = get_post_meta($order->id, '_payment_method', true);
		$tid = get_post_meta($post->ID, 'inicis_paymethod_tid', true);
		
        wp_register_script( 'ifw-admin-js', $inicis_payment->plugin_url() . '/assets/js/ifw_admin.js' );
        wp_enqueue_script( 'ifw-admin-js' );
		wp_localize_script( 'ifw-admin-js', '_ifw_admin', array(
            'action' =>  'refund_request_' . $payment_method ,
            'order_id' => $order->id,
            'nonce' => wp_create_nonce('refund_request'),
            'tid' => $tid
            ) );

        echo '<p class="order-info">';
		if( apply_filters( 'ifw_is_admin_refundable_' . $payment_method, false, $order ) ) {
	        echo '<input style="margin-right:10px" type="button" class="button button-primary tips" id="ifw-refund-request" name="refund-request" value="' . __('환불하기','codem_inicis') . '">';
		}
		if ( !empty($tid) ) {
			echo '<input type="button" class="button button-primary tips" id="ifw-check-receipt" name="refund-request-check-receipt" value="' . __('영수증 확인','codem_inicis') . '">';
		}
        echo '</p>';
    }

        public static function save( $post_id, $post ) {
    }
}