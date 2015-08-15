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

	if ( ! class_exists( 'WC_Gateway_Inicis_Escrow_bank' ) ) {
         
	    class WC_Gateway_Inicis_Escrow_bank extends WC_Gateway_Inicis{
	        public function __construct(){
	            $this->id = 'inicis_escrow_bank';
	            $this->has_fields = false;
	            
				parent::__construct();
	
	            $this->has_fields = false;
	            $this->countries = array('KR');
	            $this->method_title = __('에스크로 계좌이체', 'inicis_payment');
	            $this->method_description = __('이니시스 결제 대행 서비스를 사용하시는 분들을 위한 설정 페이지입니다. 실제 서비스를 하시려면 키파일을 이니시스에서 발급받아 설치하셔야 정상 사용이 가능합니다.', 'inicis_payment');
	    	
	            $this->init_settings();                                         
	            $this->settings['quotabase'] = '일시불';
	            $this->settings['nointerest'] = 'no';
	            $this->settings['gopaymethod'] = 'directbank';
	            $this->settings['paymethod'] = 'bank';
				
				if( empty($this->settings['title']) ){
					$this->title =  __('에스크로 계좌이체', 'inicis_payment');
					$this->description = __('이니시스 결제대행사를 통해 결제합니다. 에스크로 결제의 경우 인터넷익스프로러(IE) 환경이 아닌 경우 사용이 불가능합니다. 결제 완료시 내 계정(My-Account)에서 주문을 확인하여 주시기 바랍니다.', 'inicis_payment');
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
                
                add_action( 'wp_ajax_' . $this->id . '_order_cancelled', array( &$this, 'ajax_inicis_escrow_bank_order_cancelled' ) );
			}

            function ajax_inicis_escrow_bank_order_cancelled() {
                if ( isset($_POST['inicis_escrow_bank_refund_request']) || wp_verify_nonce($_POST['inicis_escrow_bank_refund_request'],'inicis_escrow_bank_refund_request') )
                {        
                    $get_shipping_num_send = get_post_meta($_POST['post_id'], "inicis_paymethod_escrow_delivery_add", true);
                    if( $get_shipping_num_send == 'yes' ) {
                        //배송 등록 완료 이후
                        $this->inicis_escrow_cancel_request_by_admin($_POST['post_id']);
                    } else {
                        //배송 등록 이전
                        $this->inicis_escrow_cancel_request_by_user($_POST['post_id']);
                    }
                    echo TRUE;
                    exit;
                } else {
                    echo FALSE;
                    exit;
                }
            }

            function inicis_escrow_order_shipping_save( $post_id, $post ) {
                if ( isset( $_POST['_order_tracking_number'] ) ) {
                    update_post_meta($post->ID, 'shipping_number', $_POST['_order_tracking_number']);
                }
            }

            function thankyou_page( $order_id ) {
                echo __('이니시스 에스크로 계좌이체로 결제되었습니다. 감사합니다.', 'inicis_payment');
            }
    
            function inicis_escrow_request_cancel_before_confirm($posted){
                require($this->settings['libfolder']."/libs/INILib.php");
                $inipay = new INIpay50();
                
                $inipay->SetField("inipayhome", $this->settings['libfolder']);      // 이니페이 홈디렉터리(상점수정 필요)
                $inipay->SetField("type", "cancel");                                            // 고정 (절대 수정 불가)
                $inipay->SetField("debug", "true");                                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
                $inipay->SetField("mid", $_POST['mid']);                                        // 상점아이디
                $inipay->SetField("admin", "1111");                                             // 비대칭 사용키 키패스워드
                $inipay->SetField("tid", $_POST['tid']);                                        // 취소할 거래의 거래아이디
                $inipay->SetField("cancelmsg", $_POST['msg']);                                  // 취소사유
            
                $inipay->startAction();
                
                $resultCode   = $inipay->GetResult("ResultCode");       // 결과코드 ("00"이면 지불 성공)
                $resultMsg    = $inipay->GetResult("ResultMsg");        // 결과내용 (지불결과에 대한 설명)
                $cancelDate   = $inipay->GetResult("CancelDate");       // 처리 날짜
                $cancelTime   = $inipay->GetResult("CancelTime");       // 처리 시각
                 
                $postid = $_POST['postid'];
                $orderinfo = new WC_Order($_POST['postid']);
                 
                if($resultCode == '00'){
                        
                    //환불처리시 변경될 상태          
                    $tmp_settings = get_option('woocommerce_'.$this->id.'_settings', TRUE);
                    $refunded_status = $tmp_settings['order_status_after_refund']; 
                    $orderinfo->update_status( $refunded_status );  //취소처리완료 상태로 변경 
                    
                    update_post_meta($postid, '_inicis_escrow_order_cancelled', TRUE);
                    
                    $orderinfo->add_order_note( sprintf(__('고객님께서 배송정보 등록이전에 거래를 <font color=red><strong>취소</strong></font>처리 하였습니다. 거래번호 : %s, 결과코드 : %s, 처리날짜 : %s, 처리시각 : %s','codem_inipay'), $_POST['tid'], $resultCode, $cancelDate, $cancelTime) );
                }else{
                    $orderinfo->add_order_note( sprintf( __('고객님께서 배송정보 등록이전에 거래의 <font color=red><strong>취소처리</strong></font>에 실패하셨습니다. 에러메시지를 확인하세요! 거래번호 : %s, 결과코드 : %s, 에러메시지 : %s, 처리날짜 : %s, 처리시각 : %s','codem_inipay'), $_POST['tid'], $resultCode, mb_convert_encoding($resultMsg, "UTF-8", "EUC-KR"), $cancelDate, $cancelTime ) );
                    die();
                }
            }
            
            function inicis_escrow_cancel_request_by_user($orderid){
                global $woocommerce;
                
                $payment_method = get_post_meta($orderid, '_payment_method', true);
                $tmp_settings = get_option('woocommerce_'.$payment_method.'_settings', true);
                $mid = $tmp_settings['merchant_id'];

                $tid = get_post_meta($orderid, 'inicis_paymethod_tid', true);
                $isescrow = get_post_meta($orderid, '_payment_method', true);
                
                if($isescrow == 'inicis_escrow_bank'){
                    $data = array('mid' => $mid, 
                                  'tid' => $tid,
                                  'msg' => '',
                                  'postid' => $orderid);
                                  
                    $response = wp_remote_post( home_url().'/wc-api/'.get_class($this).'?type=cancel', array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array(),
                        'body' => $data,
                        'cookies' => array()
                        )
                    );
                    
                    if ( is_wp_error( $response ) ) {
                       $error_message = $response->get_error_message();
                       echo "Error Msg : $error_message";
                    } else {
                       echo '1';
                    }     
                }
            }
            
            function inicis_escrow_cancel_request_by_admin($post_id){
                global $woocommerce;
                
                $payment_method = get_post_meta($post_id, '_payment_method', true);
                $tmp_settings = get_option('woocommerce_'.$payment_method.'_settings', true);
                $mid = $tmp_settings['merchant_id'];

                $tid = get_post_meta($post_id, 'inicis_paymethod_tid', true);
                $isescrow = get_post_meta($post_id, '_payment_method', true);
                
                $delivery_register_name = $tmp_settings['delivery_register_name']; //배송정보 등록자명
                
                $checker_name = $delivery_register_name; //구매거절 확인자 성명
                
                if($isescrow == 'inicis_escrow_bank'){
                    $data = array('mid' => $mid, 
                                  'tid' => $tid,
                                  'dcnf_name' => $checker_name,
                                  'postid' => $post_id);
                                  
                    $response = wp_remote_post( home_url().'/wc-api/'.get_class($this).'?type=denyconfirm', array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array(),
                        'body' => $data,
                        'cookies' => array()
                        )
                    );
                    
                    if ( is_wp_error( $response ) ) {
                       $error_message = $response->get_error_message();
                       echo "Error Msg : $error_message";
                    } else {
                       echo '1';
                    }                    
                }
            }     

            function inicis_escrow_delivery_add($posted) {
                global $woocommerce;
                
                //송장번호 업데이트
                if ( isset( $_POST['invoice'] ) ) {
                    update_post_meta($_POST['postid'], 'shipping_number', $_POST['invoice']);
                } else {
                    return;
                }
    
                require($this->settings['libfolder']."/libs/INILib.php");
                
                $iniescrow = new INIpay50();
                $iniescrow->SetField("inipayhome", $this->settings['libfolder']);       // 이니페이 홈디렉터리(상점수정 필요)
                $iniescrow->SetField("tid",$_POST['tid']); // 거래아이디
                $iniescrow->SetField("mid",$_POST['mid']); // 상점아이디
                $iniescrow->SetField("admin","1111"); // 키패스워드(상점아이디에 따라 변경)
                $iniescrow->SetField("type", "escrow");                                     // 고정 (절대 수정 불가)
                $iniescrow->SetField("escrowtype", "dlv");                                  // 고정 (절대 수정 불가)
                $iniescrow->SetField("dlv_ip", getenv("REMOTE_ADDR")); // 고정
                $iniescrow->SetField("debug","true"); // 로그모드("true"로 설정하면 상세한 로그가 생성됨)
                $iniescrow->SetField("oid",$_POST['oid']);  //쇼핑몰 내부 거래번호
                $iniescrow->SetField("soid","1");
                $iniescrow->SetField("dlv_date",$dlv_date);
                $iniescrow->SetField("dlv_time",$dlv_time);
                $iniescrow->SetField("dlv_report",$_POST['EscrowType']);
                $iniescrow->SetField("dlv_invoice",$_POST['invoice']);
                $iniescrow->SetField("dlv_name",$_POST['dlv_name']);
                
                $iniescrow->SetField("dlv_excode",$_POST['dlv_exCode']);  //택배사 코드 (9999:기타택배)
                $iniescrow->SetField("dlv_exname",$_POST['dlv_exName']);  //택배사 이름 (코드가 9999일때, 임의 택배사 이름 입력)
                $iniescrow->SetField("dlv_charge",$_POST['dlv_charge']);  //배송비지급방법(SH:판매자부담, BH:구매자부담)
                
                $iniescrow->SetField("dlv_invoiceday",$_POST['dlv_invoiceday']);
                $iniescrow->SetField("dlv_sendname",$_POST['sendName']);
                $iniescrow->SetField("dlv_sendpost",$_POST['sendPost']);
                $iniescrow->SetField("dlv_sendaddr1",$_POST['sendAddr1']);
                $iniescrow->SetField("dlv_sendaddr2",$_POST['sendAddr2']);
                $iniescrow->SetField("dlv_sendtel",$_POST['sendTel']);
            
                $iniescrow->SetField("dlv_recvname",$_POST['recvName']);
                $iniescrow->SetField("dlv_recvpost",$_POST['recvPost']);
                $iniescrow->SetField("dlv_recvaddr",$_POST['recvAddr']);
                $iniescrow->SetField("dlv_recvtel",$_POST['recvTel']);
                
                $iniescrow->SetField("dlv_goodscode",$_POST['goodsCode']);
                $iniescrow->SetField("dlv_goods",$_POST['goods']);
                $iniescrow->SetField("dlv_goodscnt",$_POST['goodCnt']);
                $iniescrow->SetField("price",$_POST['price']);
                $iniescrow->SetField("dlv_reserved1",$_POST['reserved1']);
                $iniescrow->SetField("dlv_reserved2",$_POST['reserved2']);
                $iniescrow->SetField("dlv_reserved3",$_POST['reserved3']);
                
                $iniescrow->SetField("pgn",$pgn);
            
                $iniescrow->startAction();
                
                 $tid        = $iniescrow->GetResult("tid");                    // 거래번호
                 $resultCode = $iniescrow->GetResult("ResultCode");     // 결과코드 ("00"이면 지불 성공)
                 $resultMsg  = $iniescrow->GetResult("ResultMsg");          // 결과내용 (지불결과에 대한 설명)
                 $dlv_date   = $iniescrow->GetResult("DLV_Date");
                 $dlv_time   = $iniescrow->GetResult("DLV_Time");
                 
                 if($resultCode == "00"){
                    echo "success";
                    die();
                 } else {
                    echo $resultCode.'_'.mb_convert_encoding($resultMsg, "UTF-8", "EUC-KR");
                    die();
                 }

            }

            function inicis_escrow_delivery_okay($posted){
                global $woocommerce;
                $orderinfo = new WC_Order($_POST['postid']);
                $orderinfo->add_order_note( __('판매자님께서 고객님의 에스크로 결제 주문을 배송 등록 또는 수정 처리하였습니다.','codem_inipay') );
                $delivery_tmp = get_post_meta( $_POST['postid'], 'inicis_paymethod_escrow_delivery_add', true );
                if( empty( $delivery_tmp ) ) {
                    $payment_method = get_post_meta($_POST['postid'], '_payment_method', true);
                    $tmp_settings = get_option('woocommerce_'.$payment_method.'_settings', true);
                    $after_enter_shipping_number_status = $tmp_settings['order_status_after_enter_shipping_number'];  //관리자 배송정보 등록/환불 가능 주문상태 지정
                    $orderinfo->update_status( $after_enter_shipping_number_status );
                }

                //1회라도 에스크로 배송 등록을 한 경우 표시(배송 수정으로 변경되도록)
                update_post_meta($_POST['postid'], "inicis_paymethod_escrow_delivery_add", "yes");  //에스크로 배송 정보 최초 추가 확인값 추가
                echo "success";
                die();
            }

            function init_form_fields() {
                parent::init_form_fields();
                
                $logo_upload = $this->form_fields["logo_upload"];
                $keyfile_upload = $this->form_fields["keyfile_upload"];
                
                unset( $this->form_fields["possible_refund_status_for_admin"] );
                unset( $this->form_fields["logo_upload"] );
                unset( $this->form_fields["keyfile_upload"] );
                
                $this->form_fields = array_merge($this->form_fields, array(
                    'order_status_after_enter_shipping_number' => array(
                        'title' => __('배송정보 등록후 변경될 주문상태', 'inicis_payment'), 
                        'class' => 'chosen_select',
                        'type' => 'select',
                        'options' => $this->get_order_status_list( array( 'pending', 'cancelled', 'completed', 'failed', 'on-hold', 'refunded' ) ),
                        'default' => 'shipped',
                        'desc' => __('에스크로 결제건에 한해서, 결제후 배송정보 등록이 완료된 경우 해당 주문의 상태를 지정하는 필수옵션입니다.', 'inicis_payment'),
                        ),
                    'possible_check_and_reject_status_for_customer' => array(
                        'title' => __('사용자 주문확인 및 거절 가능 상태', 'inicis_payment'), 
                        'type' => 'ifw_order_status',
                        'description' => __('에스크로 결제건에 한해서, 사용자가 내 계정 페이지 주문 상세 페이지에서 주문 확인 및 거절 처리를 할 수 있는 주문 상태를 지정합니다.', 'inicis_payment'),
                        'default' => array('processing'), 
                        'desc_tip' => true, 
                        ), 
                    'possible_register_delivery_info_status_for_admin' => array(
                        'title' => __('관리자 배송등록 및 환불 가능 상태', 'inicis_payment'), 
                        'type' => 'ifw_order_status',
                        'description' => __('에스크로 결제건에 한해서, 관리자가 관리자 페이지 주문 상세에서 배송 등록 및 환불 처리를 할 수 있는 주문 상태를 지정합니다.', 'inicis_payment'),
                        'default' => array('processing'), 
                        'desc_tip' => true, 
                        ), 
                    'delivery_company_name' => array(
                        'title' => __('택배사명', 'inicis_payment'),
                        'type' => 'text',
                        'description' => __('에스크로 배송시 사용하는 택배사명을 입력해주세요. 배송정보 등록시에 사용됩니다.', 'inicis_payment'),
                        ),                                        
                    'delivery_register_name' => array(
                        'title' => __('배송정보 등록자 성명', 'inicis_payment'),
                        'type' => 'text',
                        'description' => __('배송정보를 등록하시는 분의 성명을 입력해주세요. 일반적으로 사이트 관리자 성명을 입력하시면 됩니다.', 'inicis_payment'),
                        ),                                        
                    'delivery_sender_name' => array(
                        'title' => __('배송정보 발신자 성명', 'inicis_payment'),
                        'type' => 'text',
                        'description' => __('배송정보 등록시 사용되는 발신자의 성명으로 사이트 관리자 성명 또는 업체명을 입력하시면 됩니다.', 'inicis_payment'),
                        ),                                        
                    'delivery_sender_postnum' => array(
                        'title' => __('배송정보 발신자 우편번호', 'inicis_payment'),
                        'type' => 'text',
                        'description' => __('배송정보 등록시 사용되는 발신자의 우편번호로 \'000-000\'과 같이 입력해주시면 됩니다.', 'inicis_payment'),
                        'default' => '000-000',
                        ),                                        
                    'delivery_sender_addr1' => array(
                        'title' => __('배송정보 발신자 기본주소', 'inicis_payment'),
                        'type' => 'text',
                        'description' => __('배송정보 등록시 사용되는 발신자의 기본주소로 \'<strong>서울시 금천구 가산동</strong>\'과 같이 입력해주시면 됩니다.', 'inicis_payment'),
                        ),                                        
                    'delivery_sender_addr2' => array(
                        'title' => __('배송정보 발신자 상세주소', 'inicis_payment'),
                        'type' => 'text',
                        'description' => __('배송정보 등록시 사용되는 발신자의 상세주소로 \'<strong>123-1번지</strong>\' 혹은 \'<strong>A오피스텔 1동 101호</strong>\'과 같이 입력해주시면 됩니다.', 'inicis_payment'),
                        ),                                        
                    'delivery_sender_phone' => array(
                        'title' => __('배송정보 발신자 전화번호', 'inicis_payment'),
                        'type' => 'text',
                        'description' => __('배송정보 등록시 사용되는 발신자의 전화번호로 \'<strong>000-000</strong>\'과 같이 입력해주시면 됩니다.', 'inicis_payment'),
                        'default' => '000-0000-0000',
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
                    )
                ));
                
                array_push($this->form_fields, $logo_upload, $keyfile_upload);
            }

            function inicis_escrow_mypage_cancell_request($order) {
                //사용자 내 계정 페이지 주문 상세 보기에서 주문 취소 처리    
                $orderinfo = new WC_Order($order);
                $pay_escrow = get_post_meta($orderinfo->id, '_payment_method', true);
                if(empty($pay_escrow) && $pay_escrow != "inicis_escrow_bank") { return; }
                $escrow_bank_settings = get_option('woocommerce_' . $pay_escrow . '_settings');
                $valid_order_status = $escrow_bank_settings['possible_refund_status_for_mypage'];
            
                if( in_array($orderinfo->status, $valid_order_status) ){
                    
                echo '
                <script type="text/javascript">
                function OnEscrowCancelConfirmBtn(id){
                    var tid = "";
                    var mid = "";
                    if(confirm("주문 취소 처리를 하시겠습니까?\n\n")) {
                        '.$iestr.'
                        jQuery("input[name=escrow_bank_tid]").val("");
                        jQuery("input[name=escrow_bank_mid]").val("");
                        jQuery.ajax({
                            type:"POST",
                            dataType:"json",
                            url: "'.home_url().'/wc-api/'.get_class($this).'?type=get_order'.'",
                            data: {
                                postid : id
                            },
                            success:function(data, textStatus, jqXHR){
                                jQuery.each(data, function(){
                                    tid = data["tid"];
                                    mid = data["mid"];
                                });
                            }
                        });

                        jQuery.ajax({
                            type:"POST",
                            dataType:"json",
                            url: "'.home_url().'/wc-api/'.get_class($this).'?type=cancelled'.'",
                            data: {
                                post_id : id,
                                tid : tid,
                                mid : mid,
                                msg : "",
                            },
                            success:function(response, textStatus, jqXHR){
                                if(response == "success") {
                                    alert("주문 취소 처리가 완료되었습니다.");
                                    window.history.goback();
                                } else {
                                    alert("주문 취소 에러!\n\n 에러 코드 : " + data);
                                    window.history.goback();
                                }
                            }
                        });

                    } else {
                        return;
                    }
                }
                </script>
                ';
                         
                echo '<h2>주문 취소</h2>
                    <p>배송정보가 등록되기 전까지 주문을 취소할 수 있습니다. </p>
                    <p class="order-info">
                    <form name=escrow_bank_ini method=post action="'.home_url().'/wc-api/'.get_class($this).'?type=cancelled">
                        <input type="hidden" name="oid" id="oid" value="'.$orderinfo->id.'"/>
                        <input type="hidden" name="tid" id="tid" value=""/>
                        <input type="hidden" name="mid" id="mid" value=""/>
                        <input type="hidden" name="msg" id="msg" value=""/>
                        <input type="button" class="button" name="button_cancel_order_request" value="'.__('주문 취소','inicis_payment').'" onClick="javascript:OnEscrowCancelConfirmBtn('.$orderinfo->id.');"/>';
                    wp_nonce_field('cancel_request','cancel_request');
                echo '</form>'; 
                }
            }

            function inicis_escrow_mypage_accept_request($order) {
                global  $is_IE; 
                $orderinfo = new WC_Order($order);
            
                //에스크로 결제건이 아닌 경우 표시 안함
                $pay_escrow = get_post_meta($orderinfo->id, '_payment_method', true);
                if(empty($pay_escrow) || $pay_escrow != "inicis_escrow_bank") { return; }
                
                $iestr ='';
                if(!$is_IE) {
                    $iestr = "alert('주문확인 및 거절는 인터넷 익스플로러에서만 가능합니다. 익스플로러에서 진행하여 주십시오!'); return;";
                }else {
                    $iestr='';
                }
                
                echo '
                <script type="text/javascript" src="http://plugin.inicis.com/pay60_escrow.js" charset="euc-kr"></script>
                <script type="text/Javascript">
                if(typeof StartSmartUpdate == "function") { StartSmartUpdate(); }
                
                jQuery("#INIpay").css("display","none");
                function f_check(){
                    if(document.all.tid.value == ""){
                        alert("거래번호가 빠졌습니다.")
                        return;
                    }
                    if(document.all.mid.value == ""){
                        alert("상점아이디(mid)가 빠졌습니다.")
                        return;
                    }
                }
                var openwin;
                function pay(frm)
                {
                    f_check();
                    if(document.ini.clickcontrol.value == "enable")
                    {
                        if(document.INIpay==null||document.INIpay.object==null)
                        {
                            alert("플러그인을 설치 후 다시 시도 하십시오. 주의 : 에스크로의 경우에는 익스플로러에서만 서비스가 제공됩니다!");
                            return false;
                        }
                        else
                        {                       
                            if (MakePayMessage(frm))
                            {               
                                return true;
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                function enable_click()
                {
                    document.ini.clickcontrol.value = "enable"
                }
                function disable_click()
                {
                    document.ini.clickcontrol.value = "disable"
                }
                function focus_control()
                {
                    if(document.ini.clickcontrol.value == "disable")
                        openwin.focus();
                }
                function OnConfirmBtn(id){
                    if(confirm("구매 확인/거절 절차를 진행하시겠습니까?\n\n한번 구매 확인을 수행하신 경우 되돌릴 수 없습니다.\n\n신중하게 진행하여 주세요.\n\n(이미 구매 거절 처리된 상품을 다시 거절하는 경우 기거절 처리 관련 오류가 발생됩니다)")) {
                        '.$iestr.'
                        jQuery("input[name=tid]").val("");
                        jQuery("input[name=mid]").val("");
                        jQuery.ajax({
                            type:"POST",
                            dataType:"json",
                            url: "'.home_url().'/wc-api/'.get_class($this).'?type=get_order'.'",
                            data: {
                                postid : id
                            },
                            success:function(data, textStatus, jqXHR){
                                jQuery.each(data, function(){
                                    jQuery("input[name=tid]").val(data["tid"]);
                                    jQuery("input[name=mid]").val(data["mid"]);
                                    jQuery("input[name=orderid]").val( id );
                                });
                                jQuery("form[name=ini]").submit();
                            }
                        });
                    } else {
                        return;
                    }
                }
                </script>
                <body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=15 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0 onload="javascript:enable_click();" onFocus="javascript:focus_control()">    
                <form name=ini method=post action="'.home_url().'/wc-api/'.get_class($this).'?type=confirm'.'" onSubmit="return pay(this)">
                <input type=hidden name=tid size=45 value="">
                <input type=hidden name=mid value=""/>
                <input type=hidden name=paymethod value="">
                <input type=hidden name=encrypted value="">
                <input type=hidden name=sessionkey value="">
                <input type=hidden name=version value=5000>
                <input type=hidden name=clickcontrol value="">
                <input type=hidden name=acceptmethod value=" ">
                <input type=hidden name=orderid value="">
                </form></body>'; 
                
                if ( !empty($_POST['accept_request']) || isset($_POST['accept_request']) || wp_verify_nonce($_POST['accept_request'],'accept_request') )
                {
                    cancel_request_process($order);
                    update_post_meta($order, '_inicis_escrow_order_cancelled', TRUE);
                    $view_order_slug = get_option('woocommerce_myaccount_view_order_endpoint');
                    $my_account_page_id = get_option('woocommerce_myaccount_page_id');
                    $my_account_slug = get_permalink($my_account_page_id);
                    $view_order_slug = $my_account_slug.'/'.$view_order_slug.'/'.$order;
                    echo "<script type='text/javascript'>
                    location.href = '".$view_order_slug."';
                    </script>";
                }
            
                $default_data = get_option('woocommerce_inicis_escrow_bank_settings');
                if($default_data == false) { return; }
                $status_method_arr = $default_data['possible_check_and_reject_status_for_customer'];
                if(count($status_method_arr) == 1 && empty($status_method_arr[0])) {
                    $status_method_arr = array(__('shipped','inicis_payment'));    }
            
                if( get_post_meta($order, 'shipping_number', true) != "" ) {
                    //이니페이 설정 페이지 옵션 값에서 가져오기
                    $delivery_shipping_num = get_post_meta($orderinfo->id, 'shipping_number', true);
                    $inipay_setting = get_option('woocommerce_inicis_escrow_bank_settings');
                    $delivery_company_name = $inipay_setting['delivery_company_name']; //택배사명
                    $delivery_register_name = $inipay_setting['delivery_register_name']; //배송정보 등록자명
                    $delivery_sender_name = $inipay_setting['delivery_sender_name']; //송신자명
                    $delivery_sender_postnum = $inipay_setting['delivery_sender_postnum']; //송신자우편번호
                    $delivery_sender_addr1 = $inipay_setting['delivery_sender_addr1']; //송신자 기본주소
                    $delivery_sender_addr2 = $inipay_setting['delivery_sender_addr2']; //송신자 상세주소
                    $delivery_sender_phone = $inipay_setting['delivery_sender_phone']; //송신자 전화번호
                    echo '<h2>배송정보</h2><p>배송업체 : '.$delivery_company_name.'</br>송장번호 : '.$delivery_shipping_num.'</p>';
                }
                
                if( in_array($orderinfo->status, $status_method_arr) && (get_post_meta($orderinfo->id, '_payment_method', true) == 'inicis_escrow_bank') ){
                    if ( get_post_meta($orderinfo->id, '_inicis_escrow_order_confirm') == TRUE ){
                        echo '<h2>'.__('구매 확인/거절','inicis_payment').'</h2>';
                        echo '<p class="order-info">'.__('구매 확인이 완료되었습니다.','inicis_payment').'</p>';
                    } else if (get_post_meta($orderinfo->id, '_inicis_escrow_order_confirm_reject') == TRUE) {
                        echo '<h2>'.__('구매 확인/거절','inicis_payment').'</h2>';
                        echo '<p class="order-info">'.__('구매 거절 처리가 되었습니다. 관리자의 확인 이후에 처리가 완료됩니다. 만약 구매 거절을 철회하고 다시 구매 확인을 하시려는 경우 구매 확인/거절 버튼을 눌러 확인처리를 해주시기 바랍니다.','inicis_payment').'</p>
                        <p class="order-info">
                        <form name="accept_request" method="POST" action="">
                        <input type="hidden" name="accept_order" id="accept_order" value="'.$orderinfo->id.'"/>';
                        echo '<input type="button" class="button" name="button_refund_request" value="'.__('구매 확인/거절','inicis_payment').'" onClick="javascript:OnConfirmBtn('.$orderinfo->id.');"/>';
                        wp_nonce_field('accept_request','accept_request'); 
                    } else {
                        echo '<h2>'.__('구매 확인/거절','inicis_payment').'</h2>';
                        echo '<p class="order-info">물품을 받으신 후에 구매 확인 및 거절 처리를 해주세요.</p>
                        <p class="order-info">
                        <form name="accept_request" method="POST" action="">
                        <input type="hidden" name="accept_order" id="accept_order" value="'.$orderinfo->id.'"/>';
                        echo '<input type="button" class="button" name="button_refund_request" value="'.__('구매 확인/거절','inicis_payment').'" onClick="javascript:OnConfirmBtn('.$orderinfo->id.');"/>';
                        wp_nonce_field('accept_request','accept_request'); 
                    }    
                    echo '</form></p>';        
                }   
            }
                        function inicis_escrow_mypage_refund_request($order) {
            
                $orderinfo = new WC_Order($order);
            
                if ( !empty($_POST['inipay_refund_request']) || isset($_POST['inipay_refund_request']) || wp_verify_nonce($_POST['inipay_refund_request'],'inipay_refund_request') )
                {
                    $pay_method = get_post_meta($order, 'inicis_paymethod', true);
                    $pay_escrow = get_post_meta($order, '_payment_method', true);
                    if($pay_escrow == 'inicis_escrow_bank') {
                        switch($pay_method) {
                            case "DirectBank" :
                                do_action( 'woocommerce_cancelled_order_inipay', $order );  //이니페이 액션 처리
                                break;
                            default:
                                break;
                        }
                    }
                    $view_order_slug = get_option('woocommerce_myaccount_view_order_endpoint');
                    $my_account_page_id = get_option('woocommerce_myaccount_page_id');
                    $my_account_slug = get_permalink($my_account_page_id);
                    $view_order_slug = $my_account_slug.'/'.$view_order_slug.'/'.$order;
                    echo "<script type='text/javascript'>
                    location.href = '".$view_order_slug."';
                    </script>";     
                }
                
                $default_data = get_option('woocommerce_inipay_mypage_refund');
                $status_method_arr = explode( ':', $default_data );
                if(count($status_method_arr) == 1 && empty($status_method_arr[0])) {
                    $status_method_arr = array(__('order-received','inicis_payment'));
                }
            
                if( in_array($orderinfo->status, $status_method_arr) && (get_post_meta($order, '_payment_method', true) == 'inipay') ){
                    if ( get_post_meta($order, '_codem_inicis_order_cancelled') == TRUE )
                    {
                        echo '<h2>'.__('주문취소','inicis_payment').'</h2>';
                        echo '<p class="order-info">'.__('주문취소 처리가 완료되었습니다.','inicis_payment').'</p>
                        <form name="inipay_refund_request" method="POST" action="">
                        <input type="hidden" name="inipay_refund_request" id="inipay_refund_request" value="'.$order.'"/>';
                    } else {
                        echo '<h2>'.__('주문취소','inicis_payment').'</h2>';
                        echo '<p class="order-info">'.__('주문한 상품을 취소합니다. 결제 방법 및 취소 기간 등에 따라 환불에 시간이 소요될 수 있습니다.','inicis_payment').'</p>
                        <form name="inipay_refund_request" method="POST" action="">
                        <input type="hidden" name="inipay_refund_request" id="inipay_refund_request" value="'.$order.'"/>';
                        echo '<input type="submit" class="button" name="button_refund_request" value="'.__('주문 취소','inicis_payment').'"/>';
                        wp_nonce_field('inipay_refund_request','inipay_refund_request'); 
                    }    
                    echo '</form>';
                } 
            }

            function inicis_escrow_request_confirm($posted){
                global $woocommerce, $order;
                
                require($this->settings['libfolder']."/libs/INILib.php");
                
                $iniescrow = new INIpay50();
                
                $iniescrow->SetField("inipayhome", $this->settings['libfolder']);       // 이니페이 홈디렉터리(상점수정 필요)
                $iniescrow->SetField("tid",$_POST['tid']);                                          // 거래아이디
                $iniescrow->SetField("mid",$_POST['mid']);                                          // 상점아이디
                $iniescrow->SetField("admin","1111");                                               // 키패스워드(상점아이디에 따라 변경)
                $iniescrow->SetField("type", "escrow");                                             // 고정 (절대 수정 불가)
                $iniescrow->SetField("escrowtype", "confirm");                                      // 고정 (절대 수정 불가)
                $iniescrow->SetField("debug","true");                                               // 로그모드("true"로 설정하면 상세한 로그가 생성됨)
                $iniescrow->SetField("encrypted",$_POST['encrypted']);
                $iniescrow->SetField("sessionkey",$_POST['sessionkey']);
            
                $iniescrow->startAction();
                
                 $tid          = $iniescrow->GetResult("tid");                  // 거래번호
                 $resultCode   = $iniescrow->GetResult("ResultCode");           // 결과코드 ("00"이면 지불 성공)
                 $resultMsg    = $iniescrow->GetResult("ResultMsg");            // 결과내용 (지불결과에 대한 설명)
                 $resultDate   = $iniescrow->GetResult("CNF_Date");             // 처리 날짜 (구매확인일경우)
                 $resultTime   = $iniescrow->GetResult("CNF_Time");             // 처리 시각 (구매확인일경우)
            
                if($resultDate=="")
                {
                     $resultDate   = $iniescrow->GetResult("DNY_Date");         // 처리 날짜 (구매거절일경우)
                     $resultTime   = $iniescrow->GetResult("DNY_Time");         // 처리 시각 (구매거절일경우)
                }
    
                $orderinfo = new WC_Order($_POST['orderid']);
                 
                //구매확인/거절 처리 성공시(PG사에 요청이 처리된경우)
                if($resultCode == "00"){
                    $rst_confirm = ""; //회원 처리 결과 확인 값(yes:구매확인, no:구매거절)
                    $postid = $_POST['orderid']; //포스트 아이디값
                    
                    if($iniescrow->GetResult("CNF_Date") != ""){
                        $rst_confirm = "yes";                       //확인 상태
                        $orderinfo->update_status( 'completed' ); //주문처리완료 상태 
                        update_post_meta($postid, '_inicis_escrow_order_confirm', true);
                        $orderinfo->add_order_note( sprintf( __('고객님께서 에스크로 구매확인을 <font color=blue><strong>확정</strong></font>하였습니다. 거래번호 : %s, 결과코드 : %s, 처리날짜 : %s, 처리시각 : %s','inicis_payment'), $_POST['tid'], $resultCode, $resultDate, $resultTime) );
                    } else {
                        $rst_confirm = "no";                            //거절 상태
                        $orderinfo->update_status( 'cancel-request' );  //주문처리완료 상태로 변경
                        update_post_meta($postid, '_inicis_escrow_order_confirm_reject', true);
                        $orderinfo->add_order_note( sprintf( __('고객님께서 에스크로 구매확인을 <font color=red><strong>거절</strong></font>하였습니다. 거래번호 : %s, 결과코드 : %s, 처리날짜 : %s, 처리시각 : %s','inicis_payment'), $_POST['tid'], $resultCode, $resultDate, $resultTime) );
                    }
                            
                } else {
                    $my_account_page_id = get_option('woocommerce_myaccount_page_id');  
                    echo "<script type='text/javascript'> 
                    alert('".__("에러가 발생하였습니다. 다시 한번 더 시도해주시거나, 관리자에게 문의하여 주십시오!", 'inicis_payment')." ERROR CODE : ".$resultCode.", MSG : ".mb_convert_encoding($resultMsg, "UTF-8", "EUC-KR")." ');
                    </script>"; 
                }
            }
    
            function inicis_escrow_request_denyconfirm($posted){
                
                if(empty($_POST['dcnf_name']) && empty($_POST['tid']) && empty($_POST['mid']) ) {
                    return false;
                }
                    
                require($this->settings['libfolder']."/libs/INILib.php");
                
                $iniescrow = new INIpay50();
                
                $iniescrow->SetField("inipayhome", $this->settings['libfolder']);       // 이니페이 홈디렉터리(상점수정 필요)
                $iniescrow->SetField("tid",$_POST['tid']);                                          // 거래아이디
                $iniescrow->SetField("mid",$_POST['mid']);                                          // 상점아이디
                $iniescrow->SetField("admin","1111");                                               // 키패스워드(상점아이디에 따라 변경)
                $iniescrow->SetField("type", "escrow");                                             // 고정 (절대 수정 불가)
                $iniescrow->SetField("escrowtype", "dcnf");                                         // 고정 (절대 수정 불가)
                $iniescrow->SetField("dcnf_name",$_POST['dcnf_name']);
                $iniescrow->SetField("debug","true");                                               // 로그모드("true"로 설정하면 상세한 로그가 생성됨)
            
                $iniescrow->startAction();
                
                 $tid          = $iniescrow->GetResult("tid");              // 거래번호
                 $resultCode   = $iniescrow->GetResult("ResultCode");       // 결과코드 ("00"이면 지불 성공)
                 $resultMsg    = $iniescrow->GetResult("ResultMsg");        // 결과내용 (지불결과에 대한 설명)
                 $resultDate   = $iniescrow->GetResult("DCNF_Date");        // 처리 날짜
                 $resultTime   = $iniescrow->GetResult("DCNF_Time");        // 처리 시각
                 
                 $postid = $_POST['postid'];
                 $orderinfo = new WC_Order($_POST['postid']);
                 
                 if($resultCode == '00'){
                    $tmp_settings = get_option('woocommerce_'.$this->id.'_settings', TRUE);
                    $refunded_status = $tmp_settings['order_status_after_refund']; 
                    $orderinfo->update_status( $refunded_status );  //취소처리완료 상태로 변경 
    
                    update_post_meta($_POST['post_id'], '`_inicis_escrow_order_cancelled`', TRUE);
                    $orderinfo->add_order_note( sprintf( __('에스크로 구매거절을 %s님께서 <font color=blue><strong>확인</strong></font>하였습니다. 에스크로 환불처리 완료하였습니다. 거래번호 : %s, 결과코드 : %s, 처리날짜 : %s, 처리시각 : %s','inicis_payment'), $_POST['dcnf_name'], $_POST['tid'], $resultCode, $resultDate, $resultTime) );
                 }else{
                    $orderinfo->add_order_note( sprintf( __('에스크로 구매거절을 %s님께서 <font color=blue><strong>확인실패</strong></font>하였습니다. 에스크로 환불처리를 실패하였습니다. 에러메시지를 확인하세요! 거래번호 : %s, 결과코드 : %s, 에러메시지 : %s, 처리날짜 : %s, 처리시각 : %s','inicis_payment'), $_POST['dcnf_name'], $_POST['tid'], $resultCode, mb_convert_encoding($resultMsg, "UTF-8", "EUC-KR"), $resultDate, $resultTime) );
                    die();
                 }
            }
            

            function inicis_escrow_get_order($posted){
                global $woocommerce;
        
                $payment_method = get_post_meta($_POST['postid'], '_payment_method', true);
                $tmp_settings = get_option('woocommerce_'.$payment_method.'_settings', true);
                $mid = $tmp_settings['merchant_id'];
                $tid = get_post_meta($_POST['postid'], 'inicis_paymethod_tid', true);
                if($mid != false && $tid != false) {
                    $rst = array('mid' => $mid, 'tid' => $tid);
                    echo json_encode($rst); 
                }
                die();
            }

        //class end
		}

		if ( defined('DOING_AJAX') ) {
			$ajax_requests = array('payment_form_inicis_escrow_bank', 'refund_request_inicis_escrow_bank', 'inicis_escrow_bank_order_cancelled');
			if( in_array( $_REQUEST['action'], $ajax_requests ) ){
				new WC_Gateway_Inicis_Escrow_bank();
			}
		}	
	}
	   
} // class_exists function end