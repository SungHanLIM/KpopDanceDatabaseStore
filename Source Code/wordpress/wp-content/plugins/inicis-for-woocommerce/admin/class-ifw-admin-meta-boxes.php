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

class IFW_Admin_Meta_Boxes {

	private static $meta_box_errors = array();

		public function __construct() {
		include_once('post-types/meta-boxes/class-ifw-meta-box-refund.php');
        include_once('post-types/meta-boxes/class-ifw-meta-box-vbank-refund.php');
        include_once('post-types/meta-boxes/class-ifw-meta-box-escrow-register-delivery.php');
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
        
        add_action( 'woocommerce_process_shop_order_meta', 'IFW_Meta_Box_Escrow_Register_Delivery::save', 10, 2 );
	}

		public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

		public function output_errors() {
		$errors = maybe_unserialize( get_option( 'ifw_meta_box_refund_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="woocommerce_errors" class="error fade">';
			foreach ( $errors as $error ) {
				echo '<p>' . esc_html( $error ) . '</p>';
			}
			echo '</div>';

			// Clear
			delete_option( 'ifw_meta_box_refund_errors' );
		}
	}

		public function add_meta_boxes() {
	    global $post;
        
        if(isset($_GET['post'])) {
            if (get_post_meta($_GET['post'], '_payment_method', true) == 'inicis_escrow_bank') {

                $payment_method = get_post_meta($_GET['post'], '_payment_method', true);
                $tmp_settings = get_option('woocommerce_' . $payment_method . '_settings', true);
                $refund_mypage_status = $tmp_settings['possible_register_delivery_info_status_for_admin'];  //관리자 배송정보 등록/환불 가능 주문상태 지정

                $order = new WC_Order($post->ID);
                $paid_order = get_post_meta($post->ID, "_paid_date", true);
                if (in_array($order->get_status(), $refund_mypage_status) && !empty($paid_order)) {
                    add_meta_box(
                        'ifw-order-escrow-register-delivery-request',
                        __('이니시스 에스크로', 'codem_inicis'),
                        'IFW_Meta_Box_Escrow_Register_Delivery::output',
                        'shop_order',
                        'side',
                        'default'
                    );
                }
            } else if( get_post_meta($_GET['post'], '_payment_method', true) == 'inicis_vbank' ) {
                    add_meta_box(
                        'ifw-order-vbank-refund-request',
                        __('가상계좌 무통장입금 환불 처리', 'codem_inicis'),
                        'IFW_Meta_Box_Vbank_Refund::output',
                        'shop_order',
                        'side',
                        'default'
                    );
            } else {
                add_meta_box(
                    'ifw-order-refund-request',
                    __( '결제내역', 'codem_inicis' ),
                    'IFW_Meta_Box_Refund::output',
                    'shop_order',
                    'side',
                    'default'
                );
            }
        } else {
            add_meta_box(
                'ifw-order-refund-request',
                __( '결제내역', 'codem_inicis' ),
                'IFW_Meta_Box_Refund::output',
                'shop_order',
                'side',
                'default'
            );
            
        }
	}
}

new IFW_Admin_Meta_Boxes();
