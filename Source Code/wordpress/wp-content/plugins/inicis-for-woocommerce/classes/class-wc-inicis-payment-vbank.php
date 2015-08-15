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

	if ( ! class_exists( 'WC_Gateway_Inicis_Vbank' ) ) {
         
	    class WC_Gateway_Inicis_Vbank extends WC_Gateway_Inicis{
	        public function __construct(){
	            $this->id = 'inicis_vbank';
	            $this->has_fields = false;
	            
				parent::__construct();
	
	            $this->has_fields = false;
	            $this->countries = array('KR');
	            $this->method_title = __('가상계좌 무통장입금', 'inicis_payment');
	            $this->method_description = __('이니시스 결제 대행 서비스를 사용하시는 분들을 위한 설정 페이지입니다. 실제 서비스를 하시려면 키파일을 이니시스에서 발급받아 설치하셔야 정상 사용이 가능합니다.', 'inicis_payment');
				$this->view_transaction_url = 'https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s';

	            $this->init_settings();
	            $this->settings['quotabase'] = '일시불';
	            $this->settings['nointerest'] = 'no';
	            $this->settings['gopaymethod'] = 'vbank';
	            $this->settings['paymethod'] = 'vbank';
				
				if( empty($this->settings['title']) ){
					$this->title =  __('가상계좌 무통장입금', 'inicis_payment');
					$this->description = __('가상계좌 안내를 통해 무통장입금을 할 수 있습니다.', 'inicis_payment');
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
				
				add_action( 'wp_ajax_payment_form_' . $this->id, array( $this, 'wp_ajax_generate_payment_form' ) );
	        	add_action( 'wp_ajax_nopriv_payment_form_' . $this->id, array( $this, 'wp_ajax_generate_payment_form' ) );
				add_action( 'wp_ajax_refund_request_' . $this->id, array( $this, 'wp_ajax_refund_request' ) );

				add_action( 'wp_ajax_' . $this->id . '_order_cancelled', array( &$this, 'ajax_inicis_vbank_order_cancelled' ) );
			}

			function inicis_vbank_view_order($order_id)
			{
				if (empty($order_id)) {
					return;
				}

				$order = new WC_Order($order_id);
				if ($order->payment_method != 'inicis_vbank' || $order->get_status() == 'refunded' || $order->get_status() == 'failed' ) {
					return;
				}


				$VACT_BankCodeName 		= get_post_meta($order_id, 'VACT_BankCodeName', true);	//입금은행명/코드
				$VACT_Num 				= get_post_meta($order_id, 'VACT_Num', true);				//계좌번호
				$VACT_Name 				= get_post_meta($order_id, 'VACT_Name', true);			//예금주
				$VACT_InputName 		= get_post_meta($order_id, 'VACT_InputName', true);		//송금자
				$VACT_Date 				= get_post_meta($order_id, 'VACT_Date', true);			//입금예정일
				$vact_date_format 		= date( __('Y년 m월 d일','inicis_payment'), strtotime($VACT_Date) );

				echo '<h2>' . __('가상계좌 무통장입금 안내', 'inicis_payment') . '</h2>';
				echo '<p>' . __('가상계좌 무통장입금 안내로 주문이 접수되었습니다. 아래 지정된 계좌번호로 입금기한내에 반드시 입금하셔야 하며, 송금자명으로 입금 해주셔야 주문이 정상 접수 됩니다.','inicis_payment') . '</p>';
				echo '
				<table name="inicis_vbank_account_table" id="inicis_vbank_account_table" class="inicis_vbank_account_table">
					<tbody>';

				echo '<tr>';
				echo '	<th>' . __('은행명:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('은행명:','inicis_payment') . '">' . $VACT_BankCodeName . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('계좌번호:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('계좌번호:','inicis_payment') . '">' . $VACT_Num . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('예금주:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('예금주:','inicis_payment') . '">' . $VACT_Name . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('송금자:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('송금자:','inicis_payment') . '">' . $VACT_InputName . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('입금기한:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('입금기한:','inicis_payment') . '">' . $vact_date_format . '</td>';
				echo '</tr>';
				echo '</tbody></table>';
			}

            function thankyou_page( $order_id ) {
				if (empty($order_id)) {
					return;
				}

				$order = new WC_Order($order_id);
				if ($order->payment_method != 'inicis_vbank' || $order->get_status() == 'failed' ) {
					return;
				}

				$VACT_BankCodeName 		= get_post_meta($order_id, 'VACT_BankCodeName', true);	//입금은행명/코드
				$VACT_Num 				= get_post_meta($order_id, 'VACT_Num', true);				//계좌번호
				$VACT_Name 				= get_post_meta($order_id, 'VACT_Name', true);			//예금주
				$VACT_InputName 		= get_post_meta($order_id, 'VACT_InputName', true);		//송금자
				$VACT_Date 				= get_post_meta($order_id, 'VACT_Date', true);			//입금예정일
				$vact_date_format 		= date( __('Y년 m월 d일','inicis_payment'), strtotime($VACT_Date) );

				echo '<h2>' . __('가상계좌 무통장입금 안내', 'inicis_payment') . '</h2>';
				echo '<p>' . __('가상계좌 무통장입금 안내로 주문이 접수되었습니다. 아래 지정된 계좌번호로 입금기한내에 반드시 입금하셔야 하며, 송금자명으로 입금 해주셔야 주문이 정상 접수 됩니다.','inicis_payment') . '</p>';
				echo '
				<table name="inicis_vbank_account_table" id="inicis_vbank_account_table" class="inicis_vbank_account_table">
					<tbody>';

				echo '<tr>';
				echo '	<th>' . __('은행명:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('은행명:','inicis_payment') . '">' . $VACT_BankCodeName . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('계좌번호:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('계좌번호:','inicis_payment') . '">' . $VACT_Num . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('예금주:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('예금주:','inicis_payment') . '">' . $VACT_Name . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('송금자:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('송금자:','inicis_payment') . '">' . $VACT_InputName . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '	<th>' . __('입금기한:','inicis_payment') . '</th>';
				echo '	<td data-title="' . __('입금기한:','inicis_payment') . '">' . $vact_date_format . '</td>';
				echo '</tr>';
				echo '</tbody></table>';
			}

			function init_form_fields() {
				global $inicis_payment;
				parent::init_form_fields();
				unset($this->form_fields['order_status_after_payemnt']);
				unset($this->form_fields['order_status_after_refund']);
				unset($this->form_fields['logo_upload']);
				unset($this->form_fields['keyfile_upload']);
				$this->form_fields = array_merge($this->form_fields, array(
					'possible_refund_status_for_mypage' => array(
						'title' => __('사용자 주문취소 가능상태', 'inicis_payment'),
						'type' => 'ifw_order_status',
						'description' => __('이니시스 결제건에 한해서, 사용자가 My-Account 메뉴에서 주문취소 요청을 할 수 있는 주문 상태를 지정합니다.', 'inicis_payment'),
						'default' => array(''),
						'desc_tip' => true,
					),
					'possible_refund_status_for_admin' => array(
						'title' => __('관리자 주문취소 가능상태', 'inicis_payment'),
						'type' => 'ifw_order_status',
						'description' => __('이니시스 결제건에 한해서, 관리자가 관리자 페이지 주문 상세 페이지에서 환불 처리를 할 수 있는 주문 상태를 지정합니다.', 'inicis_payment'),
						'default' => array('on-hold'),
						'desc_tip' => true,
					),
					'order_status_after_payemnt' => array(
						'title' => __('주문접수시 변경될 주문상태', 'inicis_payment'),
						'class' => 'chosen_select',
						'type' => 'select',
						'options' => $this->get_order_status_list( array( 'cancelled', 'failed', 'refunded' ) ),
						'default' => 'on-hold',
						'description' => __('이니시스 플러그인을 통한 결제건에 한해서, 주문접수가 완료된 경우 해당 주문의 상태를 지정하는 필수옵션입니다.', 'inicis_payment'),
						'desc_tip' => true,
					),
					'order_status_after_vbank_noti' => array(
						'title' => __('입금통보후 변경될 주문상태', 'inicis_payment'),
						'class' => 'chosen_select',
						'type' => 'select',
						'options' => $this->get_order_status_list( array( 'cancelled', 'failed', 'refunded' ) ),
						'default' => 'processing',
						'description' => __('가상계좌 무통장입금 결제후 입금통보가 접수된 경우 해당 주문의 상태를 지정하는 필수옵션입니다.', 'inicis_payment'),
						'desc_tip' => true,
					),
					'order_status_after_refund' => array(
						'title' => __('환불처리시 변경될 주문상태', 'inicis_payment'),
						'class' => 'chosen_select',
						'type' => 'select',
						'options' => $this->get_order_status_list( array('completed','on-hold','pending','processing') ),
						'default' => 'refunded',
						'description' => __('이니시스 플러그인을 통한 결제건에 한해서, 사용자의 환불처리가 승인된 경우 해당 주문의 상태를 지정하는 필수옵션입니다.','inicis_payment'),
						'desc_tip' => true,
					),
					'order_vbank_noti_url' => array(
						'title' => __('입금내역통보 URL', 'inicis_payment'),
						'type' => 'ifw_vbank_url',
						'description' => __('가상계좌 무통장입금 내역 통보에 사용되는 URL 주소입니다. 가상계좌 무통장입금 메뉴얼을 참고하여 입력하여 주시기 바랍니다.', 'inicis_payment'),
						'desc_tip' => true,
					),
					'logo_upload' => array(
						'title' => __('결제 PG 로고', 'inicis_payment'),
						'type' => 'ifw_logo_upload',
						'description' => __('로고를 업로드 및 선택해 주세요. 128 x 40 pixels 사이즈로 지정해주셔야 하며, gif/jpg/png 확장자가 지원됩니다. 투명배경은 허용되지 않습니다. ', 'inicis_payment'),
						'default' => $inicis_payment->plugin_url() . '/assets/images/codemshop_logo_pg.jpg',
						'desc_tip' => true,
					),
					'keyfile_upload' => array(
						'title' => __('키파일 업로드', 'inicis_payment'),
						'type' => 'ifw_keyfile_upload',
						'description' => __('상점 키파일을 업로드 해주세요.', 'inicis_payment'),
						'desc_tip' => true,
					),
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

			function inicis_vbank_refund_add($posted) {
				global $woocommerce;

				$nonce = $_REQUEST['refund_wpnonce'];
				if( !wp_verify_nonce( $nonce, 'inicis_vbank_refund_add' ) ) {
					echo 'ERR : 요청 실패. 다시 시도해 주세요.';
					die();
				} else {
					add_post_meta($posted['orderid'], 'vbank_refund_bankcode', $posted['refund_bankcode']);
					add_post_meta($posted['orderid'], 'vbank_refund_vaccnum', $posted['refund_vaccnum']);
					add_post_meta($posted['orderid'], 'vbank_refund_vaccname', $posted['refund_vaccname']);
					add_post_meta($posted['orderid'], 'vbank_refund_reason', $posted['refund_reason']);
					add_post_meta($posted['orderid'], 'inicis_paymethod_vbank_add', 'yes');
					echo 'success';
					die();
				}
			}

			function inicis_vbank_refund_modify($posted) {
				global $woocommerce;

				$nonce = $_REQUEST['refund_wpnonce'];
				if( !wp_verify_nonce( $nonce, 'inicis_vbank_refund_modify' ) ) {
					echo 'ERR : 요청 실패. 다시 시도해 주세요.';
					die();
				} else {
					update_post_meta($posted['orderid'], 'vbank_refund_bankcode', $posted['refund_bankcode']);
					update_post_meta($posted['orderid'], 'vbank_refund_vaccnum', $posted['refund_vaccnum']);
					update_post_meta($posted['orderid'], 'vbank_refund_vaccname', $posted['refund_vaccname']);
					update_post_meta($posted['orderid'], 'vbank_refund_reason', $posted['refund_reason']);
					update_post_meta($posted['orderid'], 'inicis_paymethod_vbank_add', 'yes');
					echo 'success';
					die();
				}
			}

			function ajax_inicis_vbank_order_cancelled(){
				$post_id = $_POST['post_id'];
				$after_refund_order_status = $this->settings['order_status_after_refund'];
				$received_tid = get_post_meta($post_id, 'inicis_vbank_noti_received_tid', true);
				$vbank_refund_bankcode = get_post_meta($post_id, 'vbank_refund_bankcode', true);
				$vbank_refund_vaccnum = get_post_meta($post_id, 'vbank_refund_vaccnum', true);
				$vbank_refund_vaccname = get_post_meta($post_id, 'vbank_refund_vaccname', true);
				$vbank_refund_reason = get_post_meta($post_id, 'vbank_refund_reason', true);

				if ( isset($_POST['inicis_vbank_refund_request']) || wp_verify_nonce($_POST['inicis_vbank_refund_request'],'inicis_vbank_refund_request') )
				{
					if( !file_exists($this->settings['libfolder'] . "/libs/INILib.php" ) ) {
						die('<span style="color:red;font-weight:bold;">' . __( '에러 : 상점 키파일 설정에 문제가 있습니다. 사이트 관리자에게 문의하여 주십시오.', 'inicis_payment' ) . '</span>');
						wc_add_notice( __( '상점 키파일 설정에 문제가 있습니다. 사이트 관리자에게 문의하여 주십시오.', 'inicis_payment' ), 'error' );
					}
					require_once ($this->settings['libfolder'] . "/libs/INILib.php");

					$inipay = new INIpay50();
					$inipay->SetField("inipayhome", $this->settings['libfolder']);       // 이니페이 홈디렉터리(상점수정 필요)
					$inipay->SetField("type", "refund");      // 고정 (절대 수정 불가)
					$inipay->SetField("debug", "true");        // 로그모드("true"로 설정하면 상세로그가 생성됨.)
					$inipay->SetField("mid", $this->settings['merchant_id']);            // 상점아이디
					$inipay->SetField("admin", $this->settings['merchant_pw']);         //비대칭 사용키 키패스워드
					//$inipay->SetField("pgn", $pgn);	//pgn 파라미터 값의 의미를 알수가 없음
					$inipay->SetField("tid", $received_tid);            // 환불할 거래의 거래아이디
					$inipay->SetField("cancelmsg", mb_convert_encoding($vbank_refund_reason, "EUC-KR", "UTF-8"));            // 환불사유
					$inipay->SetField("racctnum", $vbank_refund_vaccnum);
					$inipay->SetField("rbankcode", $vbank_refund_bankcode);
					$inipay->SetField("racctname", mb_convert_encoding($vbank_refund_vaccname, "EUC-KR", "UTF-8"));
					$inipay->startAction();

					$order = new WC_Order($post_id);
					if($inipay->getResult('ResultCode') == '00') {
						//성공
						$order->update_status( $after_refund_order_status );
						$order->add_order_note( sprintf( __('관리자의 요청으로 주문건의 가상계좌 환불처리가 완료되었습니다. 결과코드 : %s, 처리메시지 : %s, 거래번호 : %s, 취소날짜 : %s, 취소시간 : %s, 현금영수증 환불승인번호 : %s', 'inicis_payment'), $inipay->getResult('ResultCode'), mb_convert_encoding($inipay->GetResult('ResultMsg'), "UTF-8", "EUC-KR"), $received_tid, $inipay->getResult('CancelDate'), $inipay->getResult('CancelTime'), $inipay->getResult('CSHR_CancelNum') ) );
						update_post_meta($post_id, 'inicis_paymethod_vbank_refunded', 'yes');
						wp_send_json_success( __( '관리자의 요청으로 주문건의 가상계좌 환불처리가 완료되었습니다.', 'inicis_payment' ) );
					} else {
						//실패
						$order = new WC_Order($post_id);
						$order->add_order_note( sprintf( __('관리자의 요청으로 주문건의 가상계좌 환불처리가 실패하였습니다. 결과코드 : %s, 처리메시지 : %s, 거래번호 : %s, 취소날짜 : %s, 취소시간 : %s, 현금영수증 환불승인번호 : %s', 'inicis_payment'), $inipay->getResult('ResultCode'), mb_convert_encoding($inipay->GetResult('ResultMsg'), "UTF-8", "EUC-KR"), $received_tid, $inipay->getResult('CancelDate'), $inipay->getResult('CancelTime'), $inipay->getResult('CSHR_CancelNum') ) );
						wp_send_json_error( __( '관리자의 요청으로 주문건의 가상계좌 환불처리가 실패하였습니다. 환불계좌 정보를 다시 한번 확인 하신 후 환불하기를 진행해주세요.', 'inicis_payment' ) );
					}
				} else {
					//nonce 인증 실패시
					$order = new WC_Order($post_id);
					$order->add_order_note( sprintf( __('가상계좌 환불처리 요청이 실패하였습니다. 허용되지 않은 취소 신청입니다. 아이피 : %s', 'inicis_payment'), getenv("REMOTE_ADDR") ) );
					wp_send_json_error( __( '가상계좌 환불처리 요청이 실패하였습니다. 허용되지 않은 취소 신청입니다.', 'inicis_payment' ) );
				}
			}

		}

		if ( defined('DOING_AJAX') ) {
			$ajax_requests = array('payment_form_inicis_vbank', 'refund_request_inicis_vbank', 'inicis_vbank_order_cancelled');
			if( in_array( $_REQUEST['action'], $ajax_requests ) ){
				new WC_Gateway_Inicis_Vbank();
			}
		}	
	}
	   
} // class_exists function end