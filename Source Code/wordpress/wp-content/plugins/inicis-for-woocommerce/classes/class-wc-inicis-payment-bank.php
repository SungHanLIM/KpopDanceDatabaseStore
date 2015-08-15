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
//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) exit;

if( class_exists('WC_Payment_Gateway') ) { 

	if ( ! class_exists( 'WC_Gateway_Inicis_Bank' ) ) {
         
	    class WC_Gateway_Inicis_Bank extends WC_Gateway_Inicis{
	        public function __construct(){
	            $this->id = 'inicis_bank';
	            $this->has_fields = false;
	            
				parent::__construct();
	
	            $this->has_fields = false;
	            $this->countries = array('KR');
	            $this->method_title = __('실시간 계좌이체', 'inicis_payment');
	            $this->method_description = __('이니시스 결제 대행 서비스를 사용하시는 분들을 위한 설정 페이지입니다. 실제 서비스를 하시려면 키파일을 이니시스에서 발급받아 설치하셔야 정상 사용이 가능합니다.', 'inicis_payment');
				$this->view_transaction_url = 'https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s';

	            $this->init_settings();
	            $this->settings['quotabase'] = '일시불';
	            $this->settings['nointerest'] = 'no';
	            $this->settings['gopaymethod'] = 'directbank';
	            $this->settings['paymethod'] = 'bank';
				
				if( empty($this->settings['title']) ){
					$this->title =  __('실시간 계좌이체', 'inicis_payment');
					$this->description = __('이니시스 결제대행사를 통해 결제합니다.', 'inicis_payment');
				}else{
					$this->title = $this->settings['title'];
					$this->description = $this->settings['description'];
				}
				
	            $this->merchant_id = $this->settings['merchant_id'];
	            $this->merchant_pw = $this->settings['merchant_pw'];

	            $this->init_form_fields();
	            $this->init_action();
	        }

			function init_action() {
				add_action('woocommerce_api_wc_gateway_' . $this->id, array($this, 'check_inicis_payment_response'));
               
				if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
					add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'), 20);
				} else {
					add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'), 20);
				}
                
				add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
				add_filter( 'woocommerce_payment_complete_order_status', array($this, 'woocommerce_payment_complete_order_status' ), 15, 2 );

				add_filter( 'ifw_is_admin_refundable_' . $this->id, array( $this, 'ifw_is_admin_refundable' ), 10, 2 );
				add_action( 'inicis_mypage_cancel_order_' . $this->id, array($this, 'inicis_mypage_cancel_order'), 20 );
				
				add_action( 'wp_ajax_payment_form_' . $this->id, array( &$this, 'wp_ajax_generate_payment_form' ) );
	        	add_action( 'wp_ajax_nopriv_payment_form_' . $this->id, array( &$this, 'wp_ajax_generate_payment_form' ) );
				add_action( 'wp_ajax_refund_request_' . $this->id, array( &$this, 'wp_ajax_refund_request' ) );							
			}

            function thankyou_page( $order_id ) {
                echo __('실시간 계좌이체로 결제되었습니다. 감사합니다.', 'inicis_payment');
            }

			function init_form_fields() {
				parent::init_form_fields();
				$this->form_fields = array_merge($this->form_fields, array(
					'receipt' => array(
						'title' => __('현금영수증', 'inicis_payment'),
						'class' => 'chosen_select',
						'type' => 'select',
						'label' => __('현금영수증 발행 여부 설정', 'inicis_payment'),
						'default' => 'no',
						'options' => array( 'yes' => __('발행', 'inicis_payment'),
											'no' => __('발행 차단', 'inicis_payment')),
						'description' => __('현금영수증 발행 여부를 설정할 수 있습니다. 현금영수증 발행은 이니시스와 계약이 되어 있는 경우에만 사용이 가능합니다.', 'inicis_payment'),
						'desc_tip' => true,
					),
				));
			}
		}

		if ( defined('DOING_AJAX') ) {
			$ajax_requests = array('payment_form_inicis_bank', 'refund_request_inicis_bank');
			if( in_array( $_REQUEST['action'], $ajax_requests ) ){
				new WC_Gateway_Inicis_Bank();
			}
		}	
	}
	   
} // class_exists function end