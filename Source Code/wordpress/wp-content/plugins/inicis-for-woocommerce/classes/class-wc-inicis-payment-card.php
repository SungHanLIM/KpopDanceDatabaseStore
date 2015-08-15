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

if (class_exists('WC_Payment_Gateway')) {

	if ( ! class_exists( 'WC_Gateway_Inicis_Card' ) ) {
    
		class WC_Gateway_Inicis_Card extends WC_Gateway_Inicis {
			public function __construct() {
				parent::__construct();
				
				$this->id = 'inicis_card';
				$this->method_title = __('신용카드', 'inicis_payment');
				$this->has_fields = false;
				$this->countries = array('KR');
				$this->method_description = __('이니시스 결제 대행 서비스를 사용하시는 분들을 위한 설정 페이지입니다. 실제 서비스를 하시려면 키파일을 이니시스에서 발급받아 설치하셔야 정상 사용이 가능합니다.', 'inicis_payment');
				$this->view_transaction_url = 'https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s';

				$this->init_settings();
	            $this->settings['gopaymethod'] = 'card';		
	            $this->settings['paymethod'] = 'wcard';
				
				if( empty($this->settings['title']) ){
					$this->title =  __('신용카드', 'inicis_payment');
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
                echo __('신용카드로 결제되었습니다. 감사합니다.', 'inicis_payment');
            }

			function init_form_fields() {
				parent::init_form_fields();
				$this->form_fields = array_merge($this->form_fields, array(
					'quotabase' => array(
						'title' => __('할부 구매 개월수 설정', 'inicis_payment'), 
						'type' => 'text', 
						'description' => __('할부 구매를 허용할 개월수를 설정하세요.<span style="color:red;">(무이자 할부 개월수가 아닙니다)</span><br/>예) 선택:일시불:2개월:3개월:6개월<br/>단, 최소 결제금액이 5만원 이상인 경우에만 할부 결제가 허용됩니다. 지정한 할부 개월수와 상관없이 할부 결제 최소 금액이 아닌 경우 할부 거래가 허용되지 않습니다.', 'inicis_payment'), 
						'default' => __('선택:일시불:3개월:6개월:9개월:12개월', 'inicis_payment'), 
						'desc_tip' => true, 
						), 
					'nointerest' => array(
						'title' => __('무이자 할부 설정', 'inicis_payment'), 
						'type' => 'checkbox', 
						'label' => __('무이자 할부 허용(수수료 상점 부담) ', 'inicis_payment'), 
						'default' => 'no',
						'description' => __('무이자 할부는 이니시스 계약시 무이자 할부 계약이 체결되어 있어야 합니다. 무이자 할부 허용하시면 무이자 할부에 따른 수수료는 상점에서 부담하게 됩니다. 수수료는 이니시스에 문의하여 주십시오. (단, 이니시스에서 모든 가맹점을 대상으로 하는 무이자 이벤트인 경우는 제외입니다)', 'inicis_payment'),
						'desc_tip' => true, 
						), 
                    'cardpoint' => array(
                        'title' => __('카드 포인트 결제 허용', 'inicis_payment'), 
                        'type' => 'checkbox', 
                        'label' => __('카드 포인트 결제 허용 여부 ', 'inicis_payment'), 
                        'default' => 'no',
						'description' => __('카드 포인트 결제는 이니시스 계약시 카드 포인트 사용 계약이 체결되어 있어야 합니다. 카드 포인트를 결제시에 사용할 수 있도록 허용할 것인지 여부를 지정합니다.', 'inicis_payment'),
                        'desc_tip' => true, 
                        ), 
                    'skincolor' => array(
                        'title' => __('PG결제 스킨 색상', 'inicis_payment'), 
                        'class' => 'chosen_select',
                        'type' => 'select', 
                        'label' => __('PG결제 스킨 색상 지정 ', 'inicis_payment'),
                        'default' => 'SKIN(BLUE)', 
                        'options' => array( 'SKIN(BLUE)' => __('파랑색', 'inicis_payment'), 'SKIN(GREEN)' => __('초록색', 'inicis_payment'), 'SKIN(PURPLE)' => __('보라색', 'inicis_payment'), 'SKIN(RED)' => __('빨강색', 'inicis_payment'), 'SKIN(YELLOW)' => __('노랑색', 'inicis_payment') ),
                        'description' => __('PG결제 창의 스킨 색상을 지정합니다.', 'inicis_payment'), 
                        'desc_tip' => true, 
                        ), 
				));
			}
		}
		
		if ( defined('DOING_AJAX') ) {
			$ajax_requests = array('payment_form_inicis_card', 'refund_request_inicis_card');
			if( in_array( $_REQUEST['action'], $ajax_requests ) ){
				new WC_Gateway_Inicis_Card();
			}
		}	
	}

} // class_exists function end
