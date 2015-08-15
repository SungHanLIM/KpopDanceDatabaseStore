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

    include_once('class-encrypt.php');
    
    class WC_Gateway_Inicis extends WC_Payment_Gateway{
        public function __construct(){
            add_filter( 'woocommerce_my_account_my_orders_actions',  array($this, 'woocommerce_my_account_my_orders_actions'), 10, 2 );
			add_filter( 'woocommerce_get_checkout_order_received_url',  array($this, 'woocommerce_get_checkout_order_received_url'), 99, 2 );
        }
		
		function woocommerce_get_checkout_order_received_url($order_received_url, $order_class) {

			if (defined('ICL_LANGUAGE_CODE')) {
				$checkout_pid = wc_get_page_id( 'checkout' ); 
				if( !empty($_REQUEST['lang']) ) {
					if(function_exists('icl_object_id')) {
						$checkout_pid = icl_object_id($checkout_pid, 'page', true, $_REQUEST['lang']);
					} 
				}
				$order_received_url = wc_get_endpoint_url( 'order-received', $order_class->id, get_permalink( $checkout_pid ) );
		
				if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) || is_ssl() ) {
					$order_received_url = str_replace( 'http:', 'https:', $order_received_url );
				}
		
				$order_received_url = add_query_arg( 'key', $order_class->order_key, $order_received_url );
				return $order_received_url;
				
			} else {
				return $order_received_url;
			}
		}
        
        function get_payment_description( $paymethod ) {
            switch($paymethod){
                case "card": 
                    return __( '신용카드(안심클릭)', 'inicis_payment' );
                    break;
                case "vcard":
                    return __( '신용카드(ISP)', 'inicis_payment' );
                    break;
                case "directbank":
                    return __( '실시간계좌이체', 'inicis_payment' );
                    break;
                case "wcard": 
                    return __( '신용카드(모바일)', 'inicis_payment' );
                    break;
                case "vbank": 
                    return __( '가상계좌 무통장입금', 'inicis_payment' );
                    break;
                case "bank":
                    return __( '실시간계좌이체(모바일)', 'inicis_payment' );
                    break;
                case "hpp":
                    return __( '휴대폰 소액결제', 'inicis_payment' );
                    break;
                case "mobile":
                    return __( '휴대폰 소액결제(모바일)', 'inicis_payment' );
                    break;
                default:
                    return $paymethod;
                    break;
            }
        }
        
        public function wp_ajax_refund_request() {
            global $woocommerce;
            $valid_order_status = $this->settings['possible_refund_status_for_admin'];
            $after_refund_order_status = $this->settings['order_status_after_refund'];

            $order_id = $_REQUEST['order_id'];
            $order = new WC_Order( $order_id );

        
            if( !in_array($order->status, $valid_order_status) ){
                wp_send_json_error( __('주문을 취소할 수 없는 상태입니다.', 'inicis_payment' ) );
            }
        
            $paymethod = get_post_meta($order_id, "inicis_paymethod", true);
            $paymethod = strtolower($paymethod); 
            $paymethod_tid = get_post_meta($order_id, "inicis_paymethod_tid", true); 

            if( empty($paymethod) || empty($paymethod_tid) ) {
                wp_send_json_error( __( '주문 정보에 오류가 있습니다.', 'inicis_payment' ) );
            }
            
            $rst = $this->cancel_request($paymethod_tid, __( '관리자 주문취소', 'inicis_payment' ), __( 'CM_CANCEL_002', 'inicis_payment' ) );
            if($rst == "success"){
                if($_POST['refund_request']) {
                    unset($_POST['refund_request']);
                }
                
                $order->update_status( $after_refund_order_status );
                $order->add_order_note( sprintf( __('관리자의 요청으로 주문(%s)이 취소 처리 되었습니다.', 'inicis_payment'), $this->get_payment_description($paymethod)) );
                update_post_meta($order->id, '_codem_inicis_order_cancelled', TRUE);
                wp_send_json_success( __( '주문이 정상적으로 취소되었습니다.', 'inicis_payment' ) );
            } else {
                wp_send_json_error( __( '주문 취소 시도중 오류가 발생했습니다.', 'inicis_payment' ) );
                wc_add_notice( __( '주문 취소 시도중 오류가 발생했습니다. 관리자에게 문의해주세요.', 'inicis_payment' ), 'error' );
            }
        }

        public function woocommerce_payment_complete_order_status($new_order_status, $id) {
            $order_status = $this->settings['order_status_after_payemnt'];
            if ( !empty($order_status) ) {
                return $order_status;
            } else {
                return $new_order_status;
            }
        }

        public function inicis_mypage_cancel_order($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);

            $valid_order_status = $this->settings['possible_refund_status_for_mypage'];
            $after_refund_order_status = $this->settings['order_status_after_refund'];

            if( $order->status == 'pending') {
                $order->update_status('cancelled');
                wc_add_notice( __( '주문이 정상적으로 취소되었습니다.', 'inicis_payment' ), 'success' );
                return;
            }

            if( !in_array($order->status, $valid_order_status) ){
                wc_add_notice( __( '주문을 취소할 수 없는 상태입니다. 관리자에게 문의해 주세요.', 'inicis_payment' ), 'error' );
                return;
            }
            
            $paymethod = get_post_meta($order_id, "inicis_paymethod", true);
            $paymethod = strtolower($paymethod); 
            $paymethod_tid = get_post_meta($order_id, "inicis_paymethod_tid", true); 

            if( empty($paymethod) || empty($paymethod_tid) ) {
                wc_add_notice( __( '주문 정보에 오류가 있습니다.관리자에게 문의해 주세요.', 'inicis_payment' ), 'error' );
                return;
            }

            $rst = $this->cancel_request($paymethod_tid, __( '사용자 주문취소', 'inicis_payment' ), __( 'CM_CANCEL_001', 'inicis_payment' ) );
            if($rst == "success"){
                if($_POST['refund_request']) {
                    unset($_POST['refund_request']);
                }
                $order->update_status( $after_refund_order_status );
                wc_add_notice( __( '주문이 정상적으로 취소되었습니다.', 'inicis_payment' ), 'success' );
                $order->add_order_note( sprintf( __('사용자의 요청으로 주문(%s)이 취소 처리 되었습니다.', 'inicis_payment'), $this->get_payment_description($paymethod)) );
                update_post_meta($order->id, '_codem_inicis_order_cancelled', TRUE);
            } else {
                wc_add_notice( __( '주문 취소 시도중 오류가 발생했습니다. 관리자에게 문의해주세요.', 'inicis_payment' ), 'error' );
                $order->add_order_note( sprintf( __('사용자 주문취소 시도 실패 (에러메세지 : %s)', 'inicis_payment'), $rst) );
            }
        }
        
        public function ifw_is_admin_refundable($refundable, $order) {
            $valid_order_status = $this->settings['possible_refund_status_for_admin'];
        
            if( !empty($valid_order_status) && $valid_order_status != '-1' && in_array($order->status, $valid_order_status) ){
                return true;
            }else{
                return false;
            }
        }
        
        public function woocommerce_my_account_my_orders_actions($actions, $order){
            $payment_method = get_post_meta($order->id, '_payment_method', true);

            if($payment_method == $this->id) {
                $valid_order_status = $this->settings['possible_refund_status_for_mypage'];
            
                if( !empty($valid_order_status) && $valid_order_status != '-1' && in_array($order->status, $valid_order_status) ){ 
                    
                    $cancel_endpoint = get_permalink( wc_get_page_id( 'cart' ) );
                    $myaccount_endpoint = get_permalink( wc_get_page_id( 'myaccount' ) );
                
                    $actions['cancel'] = array(
                        'url'  => wp_nonce_url( add_query_arg( array( 'inicis-cancel-order' => 'true', 'order' => $order->order_key, 'order_id' => $order->id, 'redirect' => $myaccount_endpoint ), $cancel_endpoint ), 'mshop-cancel-order' ),
                        'name' => __( 'Cancel', 'woocommerce' )
                    );
                }else{
                    unset($actions['cancel']);
                }
            } 
        
            return $actions;
        }
    
        public function validate_ifw_order_status_field($key) {
            $option_key = $this->id . '_' . $key;
            if( empty($_POST[$option_key]) ) {
                return "-1";
            } else {
                return $_POST[$option_key];    
            }
        }
        
        public function validate_ifw_logo_upload_field($key) {
            return $_POST[$key];
        }     
        
        public function validate_ifw_keyfile_upload_field($key) {
            if( empty($_FILES['upload_keyfile']) && !isset($_FILES['upload_keyfile']) ) {
                return; 
            }    
            if ( !file_exists( WP_CONTENT_DIR . '/inicis/upload' )) {
                $old = umask(0); 
                mkdir( WP_CONTENT_DIR . '/inicis/upload', 0777, true );
                umask($old);
            }
            
            if( $_FILES['upload_keyfile']['size'] > 4086 ) {
                return false;
            }

            if( !class_exists('ZipArchive') ) {
                return false;
            } 
            
            $zip = new ZipArchive();
            if(isset($_FILES['upload_keyfile']['tmp_name']) && !empty($_FILES['upload_keyfile']['tmp_name'])) {
                if($zip->open($_FILES['upload_keyfile']['tmp_name']) == TRUE) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if( !in_array( $filename, array('readme.txt', 'keypass.enc', 'mpriv.pem', 'mcert.pem') ) ) {
                            return false;
                        }
                    }
                }
                
                $movefile = move_uploaded_file($_FILES['upload_keyfile']['tmp_name'], WP_CONTENT_DIR . '/inicis/upload/' . $_FILES['upload_keyfile']['name'] );
                if ( $movefile ) {
                    WP_Filesystem();
                    $filepath = pathinfo( WP_CONTENT_DIR . '/inicis/upload/' . $_FILES['upload_keyfile']['name'] );
                    $unzipfile = unzip_file( WP_CONTENT_DIR . '/inicis/upload/' . $_FILES['upload_keyfile']['name'], WP_CONTENT_DIR . '/inicis/key/' . $filepath['filename'] );
    
                    $this->init_form_fields();
    
                    if ( !is_wp_error($unzipfile) ) {
                        if ( !$unzipfile )  {
                            return false;    
                        }
                        return true;
                    }
                } else {
                    return false;
                }   
            }
        }
      
        public function clean_status($arr_status) {
			if( !empty($arr_status) ) {
				$reoder = array();
				foreach($arr_status as $status => $status_name) {
					$status = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
					$reoder[$status] = $status_name;
				}
				return $reoder;
			} else {
				return $arr_status;
			}
        }
		
        public function generate_ifw_order_status_html($key, $value) {
            $option_key = $this->id . '_' . $key;
			
			if(version_compare( WOOCOMMERCE_VERSION, '2.2.0', '>=' )) {
				$shop_order_status = $this->clean_status(wc_get_order_statuses());	
			} else {
	            $shop_order_status = get_terms(array('shop_order_status'), array('hide_empty' => false));
			}			
						
            $selections = $this->settings[$key];
            
            if( empty($selections) ) {
                $selections = $value['default'];
            } else if( $selections == '-1' ) {
                $selections = null;
            }
            
            ob_start();
            ?><tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                    <?php echo $this->get_tooltip_html($value); ?>
                </th>
                <td class="forminp">
                    <select multiple="multiple" name="<?php echo esc_attr( $option_key ); ?>[]" style="width:350px" data-placeholder="<?php _e( '주문 상태를 선택하세요.', 'inicis_payment' ); ?>" title="<?php _e( 'Order Status', 'inicis_payment' ); ?>" class="chosen_select">
                        <?php
                            if ( $shop_order_status ) {
								if(version_compare( WOOCOMMERCE_VERSION, '2.2.0', '>=' )) {
	                            	foreach ( $shop_order_status as $status => $status_name ) {
	                                    if( !empty($selections) ) {
	                                        $selected = selected( in_array( $status, $selections ), true, false );
	                                    } else {
	                                        $selected = '';
	                                    }
	                                    echo '<option value="' . esc_attr( $status ) . '" ' . $selected .'>' . $status_name . '</option>';
	                                }									
								} else {
	                                foreach ( $shop_order_status as $status ) {
	                                    if( !empty($selections) ) {
	                                        $selected = selected( in_array( $status->slug, $selections ), true, false );
	                                    } else {
	                                        $selected = '';
	                                    }
	                                    echo '<option value="' . esc_attr( $status->slug ) . '" ' . $selected .'>' . $status->name . '</option>';
	                                }									
								}
                            }
                        ?>
                    </select><br>
                    <a class="select_all button" href="#"><?php _e( 'Select all', 'inicis_payment' ); ?></a> <a class="select_none button" href="#"><?php _e( 'Select none', 'inicis_payment' ); ?></a>
                </td>
            </tr><?php
            return ob_get_clean();
        }

        public function generate_ifw_keyfile_upload_html($key, $value) {
            ob_start();
            ?><tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                    <?php echo $this->get_tooltip_html($value); ?>
                </th>
                <td class="forminp">
                    <input id="upload_keyfile" type="file" size="36" name="upload_keyfile" />
                </td>
            </tr><?php
            return ob_get_clean();
        }
        
        public function generate_ifw_logo_upload_html($key, $value) {
            $imgsrc = $this->settings[$key];
            
            if( empty($imgsrc) ){
                $imgsrc = $value['default'];
            }
            
            ob_start();
            ?><tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                    <?php echo $this->get_tooltip_html($value); ?>
                </th>
                <td class="forminp">
                    <img src="<?php echo $imgsrc; ?>" id="upload_logo_preview" style="border: solid 1px #666;"><br>
                    <input id="upload_logo" type="text" size="36" name="<?php echo $key; ?>" value="<?php echo $imgsrc; ?>" />
                    <input class="button" id="upload_logo_button" type="button" value="<?php _e( 'Upload/Select Logo', 'inicis_payment' ); ?>" />
                    <br>                    
                </td>
            </tr><?php
            return ob_get_clean();
        }

        public function generate_ifw_vbank_url_html($key, $value) {
            ob_start();
            ?><tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                <?php echo $this->get_tooltip_html($value); ?>
            </th>
            <td class="forminp">
                <?php echo untrailingslashit(WC()->api_request_url(get_class($this) . '?type=vbank_noti'),true); ?><br>
            </td>
            </tr><?php
            return ob_get_clean();
        }

        function check_mid($mid){
            if(!empty($mid)) {
                $tmpmid = substr($mid, 0, 3);
                $tmpmid_escrow = substr($mid, 0, 5);
                if( !($tmpmid == base64_decode("SU5J") || $tmpmid == base64_decode("Q09E") || $tmpmid == base64_decode("Y29k") || $tmpmid == base64_decode("SUVT") || $mid == base64_decode("Y29kZW1zdG9yeQ==") || $tmpmid_escrow == base64_decode("RVNDT0Q=") || $tmpmid_escrow == base64_decode("aW5pZXM=")) )  {
                    $tmparr = get_option('woocommerce_'.$this->id.'_settings');    
                    $tmparr['merchant_id'] = base64_decode('SU5JcGF5VGVzdA==');
                    $this->settings['merchant_id'] = base64_decode('SU5JcGF5VGVzdA==');
                    update_option( 'woocommerce_'.$this->id.'_settings', $tmparr );
                    return false; 
                }
                return true;
            }
            return false;   
        }

        public function admin_options() {
            global $woocommerce, $inicis_payment;

            wp_register_script( 'ifw-upload', $inicis_payment->plugin_url() . '/assets/js/ifw_admin_upload.js', array( 'jquery', 'media-upload', 'thickbox' ) );
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_script( 'ifw-upload' );
            wp_enqueue_style( 'thickbox' );

            $inicis_payment->license_manager->load_activation_form();

            if ( isset( $this->method_description ) && $this->method_description != '' ) {
                $tip = '<img class="help_tip" data-tip="' . esc_attr( $this->method_description ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
            } else {
                $tip = '';
            }

            if(!empty($_POST['woocommerce_'.$this->id.'_merchant_id'])) {
                $mid = trim($_POST['woocommerce_'.$this->id.'_merchant_id']);
                if(!$this->check_mid($mid)) {
                    echo '<div id="message" class="error fade"><p><strong>' . __( '상점 아이디가 정확하지 않습니다. 상점 아이디를 확인하여 주세요. 문제가 계속 된다면 메뉴얼 또는 <a href="http://www.wordpressshop.co.kr" target="_blank">http://www.wordpressshop.co.kr</a> 사이트에 문의하여 주세요.', 'inicis_payment' ) . '</strong></p></div>';
                }
            }
            ?>
	        <div class="mshop-setting-page-wrapper" style="display:none">
            <h3><?php echo $this->method_title; echo $tip;?></h3>

            <?php if( !$this->is_valid_for_use() ) { ?>
                <div class="inline error"><p><strong><?php _e( '해당 결제 방법 비활성화', 'inicis_payment' ); ?></strong>: <?php _e( '이니시스 결제는 KRW, USD 이외의 통화로는 결제가 불가능합니다. 상점의 통화(Currency) 설정을 확인해주세요.', 'inicis_payment' ); ?></p></div>
            <?php
            } else {
                $this->generate_pg_notice();
                ?>
                <table class="form-table">
                    <?php $this->generate_settings_html(); ?>
                </table>
            <?php
            }
	        ?></div><?php
	        }

        function generate_pg_notice(){
            if(isset($_GET['noti_close'])) {
                if($_GET['noti_close'] == '1') {
                    update_option('inicis_notice_close', '1');
                } else if($_GET['noti_close'] == '0') {
                    update_option('inicis_notice_close', '0');
                }   
            }    
        
            $css = '';
            if(get_option('inicis_notice_close') == '1') {
                $css = 'display:none;';
                $admin_noti_url = admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_'.$this->id.'&noti_close=0');
                $admin_noti_txt = __('열기', 'inicis_payment');
            }else{
                $css = '';
                $admin_noti_url = admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_'.$this->id.'&noti_close=1');
                $admin_noti_txt = __('닫기', 'inicis_payment');
            }
            ?>
            <div id="welcome-panel" class="welcome-panel" style="padding-top:15px;">
                <div class="welcome-panel-content">
                    <h3 style="font-size:16px;font-weight:bold;margin-bottom: 15px;"><?php _e('공지사항', 'inicis_payment'); ?></h3>
                    <a class="welcome-panel-close" style="padding-top:15px;" href="<?php echo $admin_noti_url; ?>"><?php echo $admin_noti_txt; ?></a>
                    <div class="tab_contents" style="line-height:16px;<?php echo $css; ?>">
                        <ul>
            <?php
                $url = "http://www.wordpressshop.co.kr/category/pg_notice/feed";
                $response = wp_remote_get($url);
                $xmldata = new SimpleXMLElement($response['body']);
                $limit = 5;
                $maxitem = count($xmldata->channel->item);
                if($maxitem <= 0) {
                    echo '
                    <li style="font-size:12px;">
                        <span>' . __( '아직 공지사항이 없거나 데이터를 가져오지 못했습니다. 페이지를 새로고침 하여 주시기 바랍니다.', 'inicis_payment') . '</span>
                    </li>';
                }

                for($i=0;$i<$maxitem;$i++)
                {
                    if($i < $limit){
                        $item = $xmldata->channel->item[$i];
                        echo '<li style="font-size: 13px;font-weight: bold;">
                                <span class="label blue"><i class="icon-bullhorn"></i></span>
                                <span class="text_gray italic">'.date("Y-m-d", strtotime($item->pubDate)).'</span> | 
                                <a href="'.$item->link.'" target="_blank">'.$item->title.'</a> 
                              </li>';    
                    }
                }
            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php   
        }

        public function init_form_fields() {
            global $inicis_payment;
            
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('사용', 'inicis_payment'), 
                    'type' => 'checkbox', 
                    'label' => $this->title, 
                    'default' => 'no'
                    ), 
                'title' => array(
                    'title' => __('결제모듈 이름', 'inicis_payment'), 
                    'type' => 'text', 
                    'description' => __('사용자들이 체크아웃(결제진행)시에 나타나는 이름으로 사용자들에게 보여지는 이름입니다.', 'inicis_payment'), 
                    'default' => $this->title, 
                    'desc_tip' => true, 
                    ), 
                'description' => array(
                    'title' => __('결제모듈 설명', 'inicis_payment'), 
                    'type' => 'textarea', 
                    'description' => __('사용자들이 체크아웃(결제진행)시에 나타나는 설명글로 사용자들에게 보여지는 내용입니다.', 'inicis_payment'), 
                    'default' => $this->description, 
                    'desc_tip' => true, 
                    ), 
                'libfolder' => array(
                    'title' => __('이니페이 설치 경로', 'inicis_payment'), 
                    'type' => 'text', 
                    'description' => __('이니페이 설치 경로 안에 key 폴더(키파일)와 log 폴더(로그)가 위치한 경로를 입력해주세요. 키파일 폴더와 로그 폴더의 권한 설정은 가이드를 참고해주세요. <br><br><span style="color:red;font-weight:bold;">주의! 사용하시는 호스팅이나 서버 상태에 따라서 웹상에서 접근 불가능한 경로에 업로드 하시고 절대경로 주소를 입력해주세요. 웹상에서 접근 가능한 경로에 폴더가 위치한 경우 키파일 및 로그 파일 노출로 인한 보안사고가 발생할 수 있으며 이 경우 발생하는 문제는 상점의 책임입니다.</span>', 'inicis_payment'), 
                    'default' => WP_CONTENT_DIR . '/inicis/', 
                    'desc_tip' => true, 
                    ), 
                'merchant_id' => array(
                    'title' => __('상점 아이디', 'inicis_payment'), 
                    'class' => 'chosen_select',
                    'type' => 'select',
                    'options' => $this->get_keyfile_list(),
                    'description' => __('이니시스 상점 아이디(MID)를 선택하세요.', 'inicis_payment'), 
                    'default' => __('INIpayTest', 'inicis_payment'), 
                    'desc_tip' => true, 
                    ), 
                'merchant_pw' => array(
                    'title' => __('키파일 비밀번호', 'inicis_payment'), 
                    'type' => 'password', 
                    'description' => __('키파일 비밀번호를 입력해주세요. 기본값은 1111 입니다. ', 'inicis_payment'), 
                    'default' => __('1111', 'inicis_payment'), 
                    'desc_tip' => true, 
                    ), 
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
                    'default' => array('processing'), 
                    'desc_tip' => true, 
                    ), 
                'order_status_after_payemnt' => array(
                    'title' => __('결제완료시 변경될 주문상태', 'inicis_payment'), 
                    'class' => 'chosen_select',
                    'type' => 'select',
                    'options' => $this->get_order_status_list( array( 'cancelled', 'failed', 'on-hold', 'refunded' ) ),
                    'default' => 'processing',
                    'description' => __('이니시스 플러그인을 통한 결제건에 한해서, 결제후 주문접수가 완료된 경우 해당 주문의 상태를 지정하는 필수옵션입니다.', 'inicis_payment'),
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
            );
        }

        function get_keyfile_list() {

            if( empty( $this->settings['libfolder'] ) ) {
                $library_path = WP_CONTENT_DIR . '/inicis';
            } else {
                $library_path = $this->settings['libfolder'];
            }

            $dirs = glob( $library_path . '/key/*', GLOB_ONLYDIR);
            if( count($dirs) > 0 ) {
                $result = array();
                foreach ($dirs as $val) {
                    $tmpmid = substr( basename($val), 0, 3 );					
					$tmpmid_escrow = substr( basename($val), 0, 5 );
                    if( ($tmpmid == base64_decode("SU5J") || $tmpmid == base64_decode("Q09E") || $tmpmid == base64_decode("Y29k") || $tmpmid == base64_decode("SUVT") || $tmpmid_escrow == base64_decode("RVNDT0Q=") || $tmpmid_escrow == base64_decode("aW5pZXM=") ) )  {
                        if ( file_exists( $val . '/keypass.enc' )  && file_exists( $val . '/mcert.pem' ) && file_exists( $val . '/mpriv.pem' ) && file_exists( $val . '/readme.txt' )) {
                            $result[basename($val)] = basename($val);    
                        }
                    }
                }
                return $result;         
            } else {
                return array( -1 => __( '=== 키파일을 업로드 해주세요 ===', 'inicis_payment' ) );
            }
        }       

        function get_order_status_list($except_list) {

            if(version_compare( WOOCOMMERCE_VERSION, '2.2.0', '>=' )) {
	            $shop_order_status = $this->clean_status(wc_get_order_statuses());
	
	            $reorder = array();
	            foreach ($shop_order_status as $status => $status_name) {
	                $reorder[$status] = $status_name;
	            }
	
	            foreach ($except_list as $val) {
	                unset($reorder[$val]);
	            }
	            
	            return $reorder;
			} else {
				
	            $shop_order_status = get_terms(array('shop_order_status'), array('hide_empty' => false));
	
	            $reorder = array();
	            foreach ($shop_order_status as $key => $value) {
	                $reorder[$value->slug] = $value->name;
	            }
	
	            foreach ($except_list as $val) {
	                unset($reorder[$val]);
	            }
	            
	            return $reorder;				
			} 
        }
        
        function is_valid_for_use() {
            if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_inicis_card_supported_currencies', array( 'USD', 'KRW' ) ) ) ) {
            	return false;
            } 
                
            if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_inicis_bank_supported_currencies', array( 'USD', 'KRW' ) ) ) ) {
                return false;
            } 
    
            return true;
        }
        
        function cancel_request($tid, $msg, $code="1"){
            global $woocommerce;
    
            require_once($this->settings['libfolder']."/libs/INILib.php");
            $inipay = new INIpay50();
            
            $inipay->SetField("inipayhome", $this->settings['libfolder']);
            $inipay->SetField("type", "cancel");
            $inipay->SetField("debug", "true");
            $inipay->SetField("mid", $this->merchant_id);
            $inipay->SetField("admin", $this->merchant_pw);                            
            $inipay->SetField("tid", $tid);
            $inipay->SetField("cancelmsg", $_REQUEST['msg']);
        
            if($code != ""){
                $inipay->SetField("cancelcode", $code);
            }
    
            $inipay->startAction();
            
            if($inipay->getResult('ResultCode') == "00"){
                return "success";
            }else{
                return $inipay->getResult('ResultMsg');
            }
        }
   
        function successful_request_pc( $posted ) {
            global $woocommerce;
            
            if( !file_exists($this->settings['libfolder'] . "/libs/INILib.php" ) ) {
                die('<span style="color:red;font-weight:bold;">' . __( '에러 : 상점 키파일 설정에 문제가 있습니다. 사이트 관리자에게 문의하여 주십시오.', 'inicis_payment' ) . '</span>');
                wc_add_notice( __( '상점 키파일 설정에 문제가 있습니다. 사이트 관리자에게 문의하여 주십시오.', 'inicis_payment' ), 'error' );
            }
            require_once ($this->settings['libfolder'] . "/libs/INILib.php");
            
            
            if(isset($_REQUEST['txnid']))
            {
                $txnid = $_REQUEST['txnid'];
                $userid = get_current_user_id();
                $orderid = explode('_', $_REQUEST['txnid']);
                $orderid = (int)$orderid[0];
                $order = new WC_Order($orderid);
                
                if( $order->get_order($orderid) == false ){
                    wc_add_notice( __( '유효하지않은 주문입니다.', 'inicis_payment'), 'error' );
                    $order->add_order_note( __('결제 승인 요청 에러 : 유효하지않은 주문입니다.', 'inicis_payment' ) );
                    $order->update_status('failed');
                    return;
                }
                
                $productinfo = $this->make_product_info($order);
                $order_total = $this->inicis_get_order_total($order);

                if($order->status != 'on-hold' && $order->status != 'pending' && $order->status != 'failed'){
                    $paid_result = get_post_meta($order->id, '_paid_date', true);
                    $postmeta_txnid = get_post_meta($order->id, 'txnid', true);
                    $postmeta_paymethod = get_post_meta($order->id, 'inicis_paymethod', true);
                    $postmeta_tid = get_post_meta($order->id, 'inicis_paymethod_tid', true);

                    if(empty($paid_result)) {
                        wc_add_notice( __('주문에 따른 결제대기 시간 초과로 결제가 완료되지 않았습니다. 다시 주문을 시도 해 주세요.', 'inicis_payment'), 'error' );
                        $order->add_order_note( sprintf( __('<font color="red">주문요청(%s)에 대한 상태(%s)가 유효하지 않습니다.</font>', 'inicis_payment' ), $txnid, __($order->status, 'woocommerce') ) );
                        $order->update_status('failed');
                        return;
                    } else {
                        wc_add_notice( __( '이미 결제된 주문입니다.', 'inicis_payment'), 'error' );
                        $order->add_order_note( sprintf( __('<font color="blue">이미 결제된 주문(%s)에 주문 요청이 접수되었습니다. 현재 주문상태 : %s</font>', 'inicis_payment' ), $postmeta_txnid, __($order->status, 'woocommerce') ) );
                        $order->add_order_note( sprintf( __('이미 주문이 완료되었습니다. 결제방법 : %s, 이니시스 거래번호(TID) : <a href="https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s" target=_blank>[영수증 확인]</a>, 몰 고유 주문번호 : %s', 'inicis_payment'), $postmeta_paymethod, $postmeta_tid, $postmeta_txnid));
                        return;
                    }
                }

                if($this->validate_txnid($order, $txnid) == false){
                    wc_add_notice( sprintf( __( '유효하지 않은 주문번호(%s) 입니다.', 'inicis_payment' ), $txnid ), 'error' );
                    $order->add_order_note( sprintf( __( '<font color="red">유효하지 않은 주문번호(%s) 입니다.</font>', 'inicis_payment' ), $txnid ) );
                    $order->update_status('failed');
                    return;
                }
                
                $checkhash = hash('sha512', "$this->merchant_id|$txnid|$userid|$order_total|$productinfo|$order->billing_first_name|$order->billing_email|||||||||||");
                
                if($_REQUEST['hash'] != $checkhash){
                    wc_add_notice( sprintf( __('주문요청(%s)에 대한 위변조 검사 오류입니다.', 'inicis_payment' ), $txnid ), 'error' );
                    $order->add_order_note( sprintf( __('<font color="red">주문요청(%s)에 대한 위변조 검사 오류입니다.</font>', 'inicis_payment' ), $txnid ) );
                    $order->update_status('failed');
                    return;
                }
                
                $ini_rn = get_post_meta($order->id, "ini_rn", true);
                $ini_enctype = get_post_meta($order->id, "ini_enctype", true);    
                
                $inipay = new INIpay50();
                $inipay->SetField("inipayhome", $this->settings['libfolder']);
                $inipay->SetField("type", "securepay");
                $inipay->SetField("pgid", "INIphp". $pgid);
                $inipay->SetField("subpgip","203.238.3.10");
                $inipay->SetField("admin", $this->merchant_pw);
                $inipay->SetField("debug", "true");
                $inipay->SetField("uid", $uid);
                $inipay->SetField("goodname", mb_convert_encoding($goodname, "EUC-KR", "UTF-8"));
                $inipay->SetField("currency", $currency);
                $inipay->SetField("mid", $this->merchant_id);
                $inipay->SetField("price", $this->inicis_get_order_total($order));
                $inipay->SetField("rn", $ini_rn);
                $inipay->SetField("enctype", $ini_enctype);
                $inipay->SetField("buyername", mb_convert_encoding($buyername, "EUC-KR", "UTF-8"));
                $inipay->SetField("buyertel",  $buyertel);
                $inipay->SetField("buyeremail",$buyeremail);
                $inipay->SetField("paymethod", $paymethod);
                $inipay->SetField("encrypted", $encrypted);
                $inipay->SetField("sessionkey",$sessionkey);
                $inipay->SetField("url", home_url());
                $inipay->SetField("cardcode", $cardcode);
                $inipay->SetField("parentemail", $parentemail);
                $inipay->SetField("recvname",$recvname);
                $inipay->SetField("recvtel",$recvtel);
                $inipay->SetField("recvaddr",$recvaddr);
                $inipay->SetField("recvpostnum",$recvpostnum);
                $inipay->SetField("recvmsg",$recvmsg);
                $inipay->SetField("joincard",$joincard);
                $inipay->SetField("joinexpire",$joinexpire);
                $inipay->SetField("id_customer",$id_customer);
        
                $inipay->startAction();

                try
                {
                    if($inipay->GetResult('ResultCode') != "00"){
                        wc_add_notice( sprintf( __( '결제 승인 요청 과정에서 오류가 발생했습니다. 관리자에게 문의해주세요. 오류코드(%s), 오류메시지(%s)', 'inicis_payment' ), mb_convert_encoding($inipay->GetResult('ResultCode'), "UTF-8", "EUC-KR"), mb_convert_encoding($inipay->GetResult('ResultMsg'), "UTF-8", "EUC-KR") ), 'error');
                        $order->add_order_note( sprintf( __('<font color="red">결제 승인 요청 과정에서 오류가 발생했습니다. 오류코드(%s), 오류메시지(%s)</font>', 'inicis_payment' ),  mb_convert_encoding($inipay->GetResult('ResultCode'), "UTF-8", "EUC-KR"), mb_convert_encoding($inipay->GetResult('ResultMsg'), "UTF-8", "EUC-KR") ) );
                        $order->update_status('failed');
                        return;
                    }
                    add_post_meta($orderid, "inicis_paymethod", $paymethod);
                    add_post_meta($orderid, "inicis_paymethod_tid", $inipay->GetResult('TID'));

                    if(strtolower($paymethod) == 'vbank') {
                        wc_add_notice( __( '결제가 정상적으로 완료되었습니다.', 'inicis_payment'), 'success' );
                        $VACT_ResultMsg     = mb_convert_encoding($inipay->GetResult('VACT_ResultMsg'), "UTF-8", "CP949");
                        $VACT_Name          = mb_convert_encoding($inipay->GetResult('VACT_Name'), "UTF-8", "CP949");
                        $VACT_InputName     = mb_convert_encoding($inipay->GetResult('VACT_InputName'), "UTF-8", "CP949");
                        $TID                = $inipay->GetResult('TID');
                        $MOID               = $inipay->GetResult('MOID');
                        $VACT_RegNum        = $inipay->GetResult('VACT_RegNum');
                        $VACT_Num           = $inipay->GetResult('VACT_Num');
                        $VACT_BankCode      = $inipay->GetResult('VACT_BankCode');
                        switch($VACT_BankCode) {
                            case "03":
                                $VACT_BankCodeName = __('기업은행', 'inicis_payment');
                                break;
                            case "04":
                                $VACT_BankCodeName = __('국민은행', 'inicis_payment');
                                break;
                            case "05":
                                $VACT_BankCodeName = __('외환은행', 'inicis_payment');
                                break;
                            case "06":
                                $VACT_BankCodeName = __('국민은행(구,주택은행)', 'inicis_payment');
                                break;
                            case "07":
                                $VACT_BankCodeName = __('수협중앙회', 'inicis_payment');
                                break;
                            case "11":
                                $VACT_BankCodeName = __('농협중앙회', 'inicis_payment');
                                break;
                            case "12":
                                $VACT_BankCodeName = __('단위농협', 'inicis_payment');
                                break;
                            case "20":
                                $VACT_BankCodeName = __('우리은행', 'inicis_payment');
                                break;
                            case "21":
                                $VACT_BankCodeName = __('조흥은행', 'inicis_payment');
                                break;
                            case "23":
                                $VACT_BankCodeName = __('제일은행', 'inicis_payment');
                                break;
                            case "32":
                                $VACT_BankCodeName = __('부산은행', 'inicis_payment');
                                break;
                            case "71":
                                $VACT_BankCodeName = __('우체국', 'inicis_payment');
                                break;
                            case "81":
                                $VACT_BankCodeName = __('하나은행', 'inicis_payment');
                                break;
                            case "88":
                                $VACT_BankCodeName = __('신한은행', 'inicis_payment');
                                break;
                            default:
                                break;
                        }
                        $VACT_Date          = $inipay->GetResult('VACT_Date');
                        $VACT_Time          = $inipay->GetResult('VACT_Time');

                        update_post_meta($orderid, 'VACT_Num', $VACT_Num);  //입금계좌번호
                        update_post_meta($orderid, 'VACT_BankCode', $VACT_BankCode);    //입금은행코드
                        update_post_meta($orderid, 'VACT_BankCodeName', $VACT_BankCodeName);    //입금은행명/코드
                        update_post_meta($orderid, 'VACT_Name', $VACT_Name);    //예금주
                        update_post_meta($orderid, 'VACT_InputName', $VACT_InputName);   //송금자
                        update_post_meta($orderid, 'VACT_Date', $VACT_Date);    //입금예정일

                        $resultmsg = sprintf(
                            __( '주문이 완료되었습니다. 가상계좌 무통장 입금이 완료되었습니다. 이니시스 거래번호(TID) : %s, 몰 고유 주문번호 : %s, 가상계좌 결과메시지 : %s, 입금 계좌번호 : %s, 입금은행코드 : %s, 예금주명 : %s, 송금자명 : %s, 입금예정일 : %s', 'inicis_payment'),
                            $TID,
                            $MOID,
                            $VACT_ResultMsg,
                            $VACT_Num,
                            $VACT_BankCodeName,
                            $VACT_Name,
                            $VACT_InputName,
                            $VACT_Date
                        );
                        $result_id = $order->add_order_note( $resultmsg );
                        $order->update_status('on-hold');
                    } else {
                        wc_add_notice( __( '결제가 정상적으로 완료되었습니다.', 'inicis_payment'), 'success' );
                        $resultmsg = sprintf( __( '주문이 완료되었습니다. 결제방법 : %s, 이니시스 거래번호(TID) : <a href="https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s" target=_blank>[영수증 확인]</a>, 몰 고유 주문번호 : %s', 'inicis_payment'), $paymethod,$inipay->GetResult('TID'), $inipay->GetResult('MOID') );
                        $result_id = $order->add_order_note( $resultmsg );
                        $order->payment_complete();
                    }

                    $woocommerce->cart->empty_cart();
                }
                catch(Exception $e)
                {
                    $msg = "Error";
                    $order->add_order_note( sprintf( __( '결제 승인 요청 에러 : 예외처리 에러 ( %s )', 'inicis_payment'), $e->getMessage() ) );
                    $order->update_status('failed');
                }
                
                delete_post_meta($orderid, "ini_rn");
                delete_post_meta($orderid, "ini_enctype");
                //delete_post_meta($orderid, 'txnid');
            }else{
                wc_add_notice( __( 'Invalid Request. (ERROR: 0xF54D)', 'inicis_payment' ), 'error' );
            }
        }

        function successful_request_vbank_noti( $posted ) {
            global $woocommerce;

            $TEMP_IP = getenv("REMOTE_ADDR");
            $PG_IP  = substr($TEMP_IP,0, 10);

            if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138" )  //PG에서 보냈는지 IP로 체크
            {
                $msg_id = $_POST['msg_id'];             //메세지 타입
                $no_tid = $_POST['no_tid'];             //거래번호
                $no_oid = $_POST['no_oid'];             //상점 주문번호
                $id_merchant = $_POST['id_merchant'];   //상점 아이디
                $cd_bank = $_POST['cd_bank'];           //거래 발생 기관 코드
                $cd_deal = $_POST['cd_deal'];           //취급 기관 코드
                $dt_trans = $_POST['dt_trans'];         //거래 일자
                $tm_trans = $_POST['tm_trans'];         //거래 시간
                $no_msgseq = $_POST['no_msgseq'];       //전문 일련 번호
                $cd_joinorg = $_POST['cd_joinorg'];     //제휴 기관 코드

                $dt_transbase = $_POST['dt_transbase']; //거래 기준 일자
                $no_transeq = $_POST['no_transeq'];     //거래 일련 번호
                $type_msg = $_POST['type_msg'];         //거래 구분 코드
                $cl_close = $_POST['cl_close'];         //마감 구분코드
                $cl_kor = $_POST['cl_kor'];             //한글 구분 코드
                $no_msgmanage = $_POST['no_msgmanage']; //전문 관리 번호
                $no_vacct = $_POST['no_vacct'];         //가상계좌번호
                $amt_input = $_POST['amt_input'];       //입금금액
                $amt_check = $_POST['amt_check'];       //미결제 타점권 금액
                $nm_inputbank = mb_convert_encoding($_POST['nm_inputbank'], "UTF-8", "CP949"); //입금 금융기관명
                $nm_input = mb_convert_encoding($_POST['nm_input'], "UTF-8", "CP949");         //입금 의뢰인
                $dt_inputstd = $_POST['dt_inputstd'];   //입금 기준 일자
                $dt_calculstd = $_POST['dt_calculstd']; //정산 기준 일자
                $flg_close = $_POST['flg_close'];       //마감 전화

                //가상계좌채번시 현금영수증 자동발급신청시에만 전달
                $dt_cshr = $_POST['dt_cshr'];       //현금영수증 발급일자
                $tm_cshr = $_POST['tm_cshr'];       //현금영수증 발급시간
                $no_cshr_appl = $_POST['no_cshr_appl'];  //현금영수증 발급번호
                $no_cshr_tid = $_POST['no_cshr_tid'];   //현금영수증 발급TID

                $file = "ININoti" . $this->merchant_id . "_" . date("Ymd") . ".log";
                $logfile = fopen($this->settings['libfolder'] . "/log/" . $file, "a+");

                fwrite($logfile, "************************************************\r\n");
                fwrite($logfile, "DATETIME(발생시간) : ".date("Y-m-d H:i:s"). "\r\n");
                fwrite($logfile, "ID_MERCHANT(상점아이디) : " . $id_merchant . "\r\n");
                fwrite($logfile, "NO_TID(거래번호) : " . $no_tid . "\r\n");
                fwrite($logfile, "NO_OID(상점거래번호) : " . $no_oid . "\r\n");
                fwrite($logfile, "NO_VACCT(계좌번호) : " . $no_vacct . "\r\n");
                fwrite($logfile, "AMT_INPUT(입금액) : " . $amt_input . "\r\n");
                fwrite($logfile, "NM_INPUTBANK(입금은행명) : " . $nm_inputbank . "\r\n");
                fwrite($logfile, "NM_INPUT(입금자명) : " . $nm_input . "\r\n");
                fwrite($logfile, "************************************************\r\n");

                fwrite( $logfile,"전체 결과값"."\r\n");
                fwrite( $logfile, $msg_id."\r\n");
                fwrite( $logfile, $no_tid."\r\n");
                fwrite( $logfile, $no_oid."\r\n");
                fwrite( $logfile, $id_merchant."\r\n");
                fwrite( $logfile, $cd_bank."\r\n");
                fwrite( $logfile, $dt_trans."\r\n");
                fwrite( $logfile, $tm_trans."\r\n");
                fwrite( $logfile, $no_msgseq."\r\n");
                fwrite( $logfile, $type_msg."\r\n");
                fwrite( $logfile, $cl_close."\r\n");
                fwrite( $logfile, $cl_kor."\r\n");
                fwrite( $logfile, $no_msgmanage."\r\n");
                fwrite( $logfile, $no_vacct."\r\n");
                fwrite( $logfile, $amt_input."\r\n");
                fwrite( $logfile, $amt_check."\r\n");
                fwrite( $logfile, $nm_inputbank."\r\n");
                fwrite( $logfile, $nm_input."\r\n");
                fwrite( $logfile, $dt_inputstd."\r\n");
                fwrite( $logfile, $dt_calculstd."\r\n");
                fwrite( $logfile, $flg_close."\r\n");
                fwrite( $logfile, "\r\n");

                fclose( $logfile );

                //OID 에서 주문번호 확인
                $arr_oid = explode('_', $no_oid);
                $order_id = $arr_oid[0];
                $order_date = $arr_oid[1];
                $order_time = $arr_oid[2];

                $txnid = get_post_meta($order_id, 'txnid', true);  //상점거래번호(OID)
                $order_tid = get_post_meta($order_id, 'inicis_paymethod_tid', true);  //거래번호(TID)
                $VACT_Num = get_post_meta($order_id, 'VACT_Num', true);  //입금계좌번호
                $VACT_BankCode = get_post_meta($order_id, 'VACT_BankCode', true);    //입금은행코드
                $VACT_BankCodeName = get_post_meta($order_id, 'VACT_BankCodeName', true);    //입금은행명/코드
                $VACT_Name = get_post_meta($order_id, 'VACT_Name', true);    //예금주
                $VACT_InputName = get_post_meta($order_id, 'VACT_InputName', true);   //송금자
                $VACT_Date = get_post_meta($order_id, 'VACT_Date', true);    //입금예정일

                $order = new WC_Order($order_id);
                if( !in_array($order->get_status(), array('completed', 'cancelled', 'refunded') ) ) {  //주문상태 확인
                    if($txnid != $no_oid) {    //거래번호(oid) 체크
                        echo 'FAIL_11';
                        exit();
                    }
                    if($cd_bank != $VACT_BankCode) {    //입금은행 코드 체크
                        echo 'FAIL_12';
                        exit();
                    }
                    if($VACT_Num != $no_vacct) {    //입금계좌번호 체크
                        echo 'FAIL_13';
                        exit();
                    }
                    if((int)$amt_input != (int)$order->get_total()) {    //입금액 체크
                        echo 'FAIL_14';
                        exit();
                    }

                    update_post_meta($order->id, 'inicis_vbank_noti_received', 'yes');
                    update_post_meta($order->id, 'inicis_vbank_noti_received_tid', $no_tid);
                    $order->add_order_note( sprintf( __('가상계좌 무통장 입금이 완료되었습니다.  거래번호(TID) : %s, 상점거래번호(OID) : %s', 'inicis_payment'), $no_tid, $no_oid ) );
                    $order->payment_complete();
                    $order->update_status($this->settings['order_status_after_vbank_noti']);
                    echo 'OK';
                    exit();
                } else { //주문상태가 이상한 경우
                    $order->add_order_note( sprintf( __('입금통보 내역이 수신되었으나, 주문 상태가 문제가 있습니다. 이미 완료된 주문이거나, 환불된 주문일 수 있습니다. 거래번호(TID) : %s, 상점거래번호(OID) : %s','inicis_payment'), $no_tid, $no_oid ) );
                    echo 'FAIL_20';    //가맹점 관리자 사이트에서 재전송 가능하나 주문건 확인 필요
                    exit();
                }
            }
        }
          
        function successful_request_mobile_next( $posted ) {
            global $woocommerce;

            if (!file_exists($this->settings['libfolder'] . "/libs/INImx.php")) {
                die( __('<span style="color:red;font-weight:bold;">에러 : 상점 키파일 설정에 문제가 있습니다. 사이트 관리자에게 문의하여 주십시오.</span>', 'inicis_payment') );
                wc_add_notice( __( '상점 키파일 설정에 문제가 있습니다. 사이트 관리자에게 문의하여 주십시오.', 'inicis_payment' ), 'error' );
            }
            require_once ($this->settings['libfolder'] . "/libs/INImx.php");

            if( $_REQUEST['P_STATUS'] == '00' )
            {
                $notification = $this->decrypt_notification($_REQUEST['P_NOTI']);
                if( empty($notification) ){
                    wc_add_notice( __( '유효하지않은 주문입니다.(01xf1)', 'inicis_payment' ), 'error' );
                    return;
                }

                $txnid = $notification->txnid;
                $hash = $notification->hash;

                if(empty($txnid)){
                    wc_add_notice( __( '유효하지않은 주문입니다.(01xf2)', 'inicis_payment' ), 'error' );
                    return;
                }

                $userid = get_current_user_id();
                $orderid = explode('_', $txnid);
                $orderid = (int)$orderid[0];
                $order = new WC_Order($orderid);

                if( empty($order) || !is_numeric($orderid) || $order->get_order($orderid) == false ){
                    wc_add_notice( __( '유효하지않은 주문입니다.(01xf3)', 'inicis_payment' ), 'error' );
                    return;
                }

                $productinfo = $this->make_product_info($order);
                $order_total = $this->inicis_get_order_total($order);

                if($order->status != 'on-hold' && $order->status != 'pending' && $order->status != 'failed'){
                    $paid_result = get_post_meta($order->id, '_paid_date', true);
                    $postmeta_txnid = get_post_meta($order->id, 'txnid', true);
                    $postmeta_paymethod = get_post_meta($order->id, 'inicis_paymethod', true);
                    $postmeta_tid = get_post_meta($order->id, 'inicis_paymethod_tid', true);

                    if(empty($paid_result)) {
                        wc_add_notice( __( '주문에 따른 결제대기 시간 초과로 결제가 완료되지 않았습니다. 다시 주문을 시도 해 주세요.', 'inicis_payment' ), 'error' );
                        $order->add_order_note( sprintf( __('<font color="red">주문요청(%s)에 대한 상태(%s)가 유효하지 않습니다.</font>', 'inicis_payment' ), $txnid, __($order->status, 'woocommerce') ) );
                        $order->add_order_note( __('결제 승인 요청 에러 : 유효하지않은 주문입니다.', 'inicis_payment' ) );
                        $order->update_status('failed');
                        return;
                    } else {
                        wc_add_notice( __( '이미 결제된 주문입니다.', 'inicis_payment'), 'error' );
                        $order->add_order_note( sprintf( __('<font color="blue">이미 결제된 주문(%s)에 주문 요청이 접수되었습니다. 현재 주문상태 : %s</font>', 'inicis_payment' ), $postmeta_txnid, __($order->status, 'woocommerce') ) );
                        $order->add_order_note( sprintf( __('이미 주문이 완료되었습니다. 결제방법 : %s, 이니시스 거래번호(TID) : <a href="https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s" target=_blank>[영수증 확인]</a>, 몰 고유 주문번호 : %s', 'inicis_payment'), $postmeta_paymethod, $postmeta_tid, $postmeta_txnid));
                        return;
                    }
                }

                if($this->validate_txnid($order, $txnid) == false){
                    wc_add_notice( sprintf( __('유효하지 않은 주문번호(%s) 입니다.', 'inicis_payment' ), $txnid), 'error' );
                    $order->add_order_note( sprintf( __('<font color="red">유효하지 않은 주문번호(%s) 입니다.</font>', 'inicis_payment' ), $txnid) );
                    $order->update_status('failed');
                    return;
                }

                $checkhash = hash('sha512', "$this->merchant_id|$txnid||$order_total|$productinfo|$order->billing_first_name|$order->billing_email|||||||||||");

                if($hash != $checkhash){
                    wc_add_notice( sprintf( __( '주문요청(%s)에 대한 위변조 검사 오류입니다.', 'inicis_payment' ), $txnid ), 'error' );
                    $order->add_order_note( sprintf( __('<font color="red">주문요청(%s)에 대한 위변조 검사 오류입니다.</font>', 'inicis_payment' ), $txnid) );
                    $order->update_status('failed');
                    return;
                }

                $inimx = new INImx();
                $inimx->reqtype             = "PAY";
                $inimx->inipayhome          = $this->settings['libfolder'];
                $inimx->id_merchant         = $this->merchant_id;
                $inimx->status              = $P_STATUS;
                $inimx->rmesg1              = $P_RMESG1;
                $inimx->tid                 = $P_TID;
                $inimx->req_url             = $P_REQ_URL;
                $inimx->noti                = $P_NOTI;
                $inimx->startAction();
                $inimx->getResult();

                try
                {
                    if($inimx->m_resultCode != "00"){
                        wc_add_notice( sprintf( __( '결제 승인 요청 과정에서 오류가 발생했습니다. 관리자에게 문의해주세요. 오류코드(%s), 오류메시지(%s)', 'inicis_payment' ), esc_attr($inimx->m_resultCode), esc_attr($inimx->m_resultMsg) ), 'error' );
                        $order->add_order_note( sprintf( __('<font color="red">결제 승인 요청 과정에서 오류가 발생했습니다. 오류코드(%s), 오류메시지(%s)</font>', 'inicis_payment' ), esc_attr($inimx->m_resultCode), esc_attr($inimx->m_resultMsg) ) );
                        $order->update_status('failed');
                        return;
                    }

                    $inimx_txnid = $inimx->m_moid;
                    $inimx_orderid = explode('_', $inimx_txnid);
                    $inimx_orderid = (int)$inimx_orderid[0];

                    if( $txnid != $inimx_txnid || $orderid != $inimx_orderid ){
                        wc_add_notice( __( '주문요청에 대한 위변조 검사 오류입니다. 관리자에게 문의해주세요.', 'inicis_payment' ), 'error' );
                        $order->add_order_note( sprintf( __( '<font color="red">주문요청(%s, %s, %s, %s)에 대한 위변조 검사 오류입니다. 결재는 처리되었으나, 결재요청에 오류가 있습니다. 이니시스 결재내역을 확인하신 후, 고객에게 연락을 해주시기 바랍니다.</font>', 'inicis_payment' ), $txnid, $inimx_txnid, $orderid, $inimx_orderid ) );
                        $order->update_status('failed');
                        return;
                    }

                    add_post_meta($orderid, "inicis_paymethod", $inimx->m_payMethod);
                    add_post_meta($orderid, "inicis_paymethod_tid",  $inimx->m_tid);

                    if(strtolower($inimx->m_payMethod) == 'vbank') {
                        $VACT_ResultMsg     = mb_convert_encoding($inimx->m_resultMsg, "UTF-8", "CP949");
                        $VACT_Name          = mb_convert_encoding($inimx->m_nmvacct, "UTF-8", "CP949");
                        $VACT_InputName     = mb_convert_encoding($inimx->m_buyerName, "UTF-8", "CP949");
                        $TID                = $inimx->m_tid;
                        $MOID               = $inimx->m_moid;
                        $VACT_Num           = $inimx->m_vacct;
                        $VACT_BankCode      = $inimx->m_vcdbank;
                        switch($VACT_BankCode) {
                            case "03":
                                $VACT_BankCodeName = __('기업은행', 'inicis_payment');
                                break;
                            case "04":
                                $VACT_BankCodeName = __('국민은행', 'inicis_payment');
                                break;
                            case "05":
                                $VACT_BankCodeName = __('외환은행', 'inicis_payment');
                                break;
                            case "06":
                                $VACT_BankCodeName = __('국민은행(구,주택은행)', 'inicis_payment');
                                break;
                            case "07":
                                $VACT_BankCodeName = __('수협중앙회', 'inicis_payment');
                                break;
                            case "11":
                                $VACT_BankCodeName = __('농협중앙회', 'inicis_payment');
                                break;
                            case "12":
                                $VACT_BankCodeName = __('단위농협', 'inicis_payment');
                                break;
                            case "20":
                                $VACT_BankCodeName = __('우리은행', 'inicis_payment');
                                break;
                            case "21":
                                $VACT_BankCodeName = __('조흥은행', 'inicis_payment');
                                break;
                            case "23":
                                $VACT_BankCodeName = __('제일은행', 'inicis_payment');
                                break;
                            case "32":
                                $VACT_BankCodeName = __('부산은행', 'inicis_payment');
                                break;
                            case "71":
                                $VACT_BankCodeName = __('우체국', 'inicis_payment');
                                break;
                            case "81":
                                $VACT_BankCodeName = __('하나은행', 'inicis_payment');
                                break;
                            case "88":
                                $VACT_BankCodeName = __('신한은행', 'inicis_payment');
                                break;
                            default:
                                break;
                        }
                        $VACT_Date          = $inimx->m_dtinput;
                        $VACT_Time          = $inimx->m_tminput;

                        update_post_meta($orderid, 'VACT_Num', $VACT_Num);  //입금계좌번호
                        update_post_meta($orderid, 'VACT_BankCode', $VACT_BankCode);    //입금은행코드
                        update_post_meta($orderid, 'VACT_BankCodeName', $VACT_BankCodeName);    //입금은행명/코드
                        update_post_meta($orderid, 'VACT_Name', $VACT_Name);    //예금주
                        update_post_meta($orderid, 'VACT_InputName', $VACT_InputName);   //송금자
                        update_post_meta($orderid, 'VACT_Date', $VACT_Date);    //입금예정일

                        $resultmsg = sprintf(
                            __( '주문이 완료되었습니다. [모바일] 무통장(가상계좌) 입금을 기다려주시기 바랍니다. 입금 계좌번호 : %s, 입금은행코드 : %s, 예금주명 : %s, 송금자명 : %s, 입금예정일 : %s', 'inicis_payment'),
                            $VACT_Num,
                            $VACT_BankCodeName,
                            $VACT_Name,
                            $VACT_InputName,
                            $VACT_Date
                        );
                        $order->add_order_note( $resultmsg );
                        $order->update_status('on-hold');
                    } else {
                        $order->add_order_note( sprintf( __( '주문이 완료되었습니다. 결제방법 : [모바일] %s, 이니시스 거래번호(TID) : <a href="https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s" target=_blank>[영수증 확인]</a>, 몰 고유 주문번호 : %s', 'inicis_payment'), $inimx->m_payMethod, $inimx->m_tid, $inimx->m_moid ) );
                        $order->payment_complete();
                    }

                    $woocommerce->cart->empty_cart();
                }
                catch(Exception $e)
                {
                    $msg = "Error";
                    $order->add_order_note( sprintf( __( '결제 승인 요청 에러 : 예외처리 에러 ( %s )', 'inicis_payment'), $e->getMessage() ) );
                    $order->update_status('failed');
                }

                delete_post_meta($orderid, "ini_rn");
                delete_post_meta($orderid, "ini_enctype");
            } else if( $_REQUEST['P_STATUS'] == '01' ) {
                wc_add_notice( __('결제를 취소하셨습니다.  (ERROR: 0xF53D)', 'inicis_payment' ), 'error' );
                wp_redirect( WC()->cart->get_checkout_url() );
                exit();

            } else {
                wc_add_notice( __('Invalid Request. (ERROR: 0xF54D)', 'inicis_payment' ), 'error' );
            }
        }
        
        function successful_request_mobile_noti( $posted ) {
            global $woocommerce;

            $PGIP = $_SERVER['REMOTE_ADDR'];
            if($PGIP == "211.219.96.165" || $PGIP == "118.129.210.25")
            {
                $P_TID;                 
                $P_MID;                 
                $P_AUTH_DT;             
                $P_STATUS;              
                $P_TYPE;                
                $P_OID;                 
                $P_FN_CD1;              
                $P_FN_CD2;              
                $P_FN_NM;               
                $P_AMT;                 
                $P_UNAME;               
                $P_RMESG1;              
                $P_RMESG2;              
                $P_NOTI;                
                $P_AUTH_NO;             
    
                $P_TID = $_REQUEST['P_TID'];
                $P_MID = $_REQUEST['P_MID'];
                $P_AUTH_DT = $_REQUEST['P_AUTH_DT'];
                $P_STATUS = $_REQUEST['P_STATUS'];
                $P_TYPE = $_REQUEST['P_TYPE'];
                $P_OID = $_REQUEST['P_OID'];
                $P_FN_CD1 = $_REQUEST['P_FN_CD1'];
                $P_FN_CD2 = $_REQUEST['P_FN_CD2'];
                $P_FN_NM = $_REQUEST['P_FN_NM'];
                $P_AMT = $_REQUEST['P_AMT'];
                $P_UNAME = $_REQUEST['P_UNAME'];
                $P_RMESG1 = $_REQUEST['P_RMESG1'];
                $P_RMESG2 = $_REQUEST['P_RMESG2'];
                $P_NOTI = $_REQUEST['P_NOTI'];
                $P_AUTH_NO = $_REQUEST['P_AUTH_NO'];

                //모바일 무통장입금(가상계좌) 입금통보 처리
                if($P_TYPE == "VBANK")
                {
                    if($P_STATUS == "02") {
                        //OID 에서 주문번호 확인
                        $arr_oid = explode('_', $P_OID);
                        $order_id = $arr_oid[0];
                        $order_date = $arr_oid[1];
                        $order_time = $arr_oid[2];

                        //$P_RMESG1 에서 입금계좌 및 입금예정일 확인
                        $arr_tmp = explode('|', $P_RMESG1);
                        $p_vacct_no_tmp = explode('=', $arr_tmp[0]);
                        $p_vacct_no = $p_vacct_no_tmp[1];
                        $p_exp_datetime_tmp = explode('=', $arr_tmp[1]);
                        $p_exp_datetime = $p_exp_datetime_tmp[1];

                        $txnid = get_post_meta($order_id, 'txnid', true);  //상점거래번호(OID)
                        $order_tid = get_post_meta($order_id, 'inicis_paymethod_tid', true);  //거래번호(TID)
                        $VACT_Num = get_post_meta($order_id, 'VACT_Num', true);  //입금계좌번호
                        $VACT_BankCode = get_post_meta($order_id, 'VACT_BankCode', true);    //입금은행코드
                        $VACT_BankCodeName = get_post_meta($order_id, 'VACT_BankCodeName', true);    //입금은행명/코드
                        $VACT_Name = get_post_meta($order_id, 'VACT_Name', true);    //예금주
                        $VACT_InputName = get_post_meta($order_id, 'VACT_InputName', true);   //송금자
                        $VACT_Date = get_post_meta($order_id, 'VACT_Date', true);    //입금예정일

                        $order = new WC_Order($order_id);
                        if( !in_array($order->get_status(), array('completed', 'cancelled', 'refunded') ) ) {  //주문상태 확인
                            if($txnid != $P_OID) {    //거래번호(oid) 체크
                                echo 'FAIL_M11';
                                exit();
                            }
                            if($P_FN_CD1 != $VACT_BankCode) {    //입금은행 코드 체크
                                echo 'FAIL_M12';
                                exit();
                            }
                            if($VACT_Num != $p_vacct_no) {    //입금계좌번호 체크
                                echo 'FAIL_M13';
                                exit();
                            }
                            if((int)$P_AMT != (int)$order->get_total()) {    //입금액 체크
                                echo 'FAIL_M14';
                                exit();
                            }

                            update_post_meta($order->id, 'inicis_vbank_noti_received', 'yes');
                            update_post_meta($order->id, 'inicis_vbank_noti_received_tid', $P_TID);
                            $order->add_order_note( sprintf( __('입금통보 내역이 수신되었습니다. 가맹점 관리자에서 주문 확인후 처리해주세요. 전송서버IP : %s, 거래번호(TID) : %s, 상점거래번호(OID) : %s, 입금은행코드 : %s, 입금은행명 : %s, 입금가상계좌번호 : %s, 입금액 : %s, 입금자명 : %s', 'inicis_payment'), $PGIP,  $P_TID, $P_OID, $P_FN_CD1, mb_convert_encoding($P_FN_NM, "UTF-8", "EUC-KR"), $p_vacct_no, number_format($P_AMT), mb_convert_encoding($P_UNAME, "UTF-8", "EUC-KR") ) );
                            $order->payment_complete();
                            $order->update_status($this->settings['order_status_after_vbank_noti']);
                            echo 'OK';
                            exit();
                        } else { //주문상태가 이상한 경우
                            $order->add_order_note( sprintf( __('입금통보 내역이 수신되었으나, 주문 상태가 문제가 있습니다. 이미 완료된 주문이거나, 환불된 주문일 수 있습니다. 전송서버IP : %s, 거래번호(TID) : %s, 상점거래번호(OID) : %s, 입금은행코드 : %s, 입금은행명 : %s, 입금가상계좌번호 : %s, 입금액 : %s, 입금자명 : %s','inicis_payment'), $PGIP,  $P_TID, $P_OID, $P_FN_CD1, mb_convert_encoding($P_FN_NM, "UTF-8", "EUC-KR"), $p_vacct_no, number_format($P_AMT), mb_convert_encoding($P_UNAME, "UTF-8", "EUC-KR") ) );
                            echo 'FAIL_20';    //가맹점 관리자 사이트에서 재전송 가능하나 주문건 확인 필요
                            exit();
                        }
                    } else {
                        echo "OK";
                        return;
                    }
                }
    	
                $notification = $this->decrypt_notification($_POST['P_NOTI']);
                if( empty($notification) ){
                    $this->inicis_noti_print_log( __( '유효하지않은 주문입니다. (invalid notification)', 'inicis_payment' ) );
                    echo "FAIL";
                    exit();
                }
                    
                $txnid = $notification->txnid;
                $hash = $notification->hash;
                
                if( $_REQUEST['P_STATUS'] == '00' && !empty($txnid) )
                {
                    $userid = get_current_user_id();
                    $orderid = explode('_', $txnid);
                    $orderid = (int)$orderid[0];
                    $order = new WC_Order($orderid);

                    if( empty($order) || !is_numeric($orderid) || $order->get_order($orderid) == false ){
                        $this->inicis_noti_print_log( __( '유효하지않은 주문입니다. (invalid orderid)', 'inicis_payment' ) );
                        echo "FAIL";
                        exit();
                    }
                    
                    $productinfo = $this->make_product_info($order);
                    $order_total = $this->inicis_get_order_total($order);

                    if($order->status == 'failed' || $order->status == 'cancelled' ){
                        $this->inicis_noti_print_log( sprintf( __('주문요청(%s)에 대한 상태(%s)가 유효하지 않습니다.', 'inicis_payment' ), $txnid, __($order->status, 'woocommerce')));
                        $order->add_order_note( sprintf( __('<font color="red">주문요청(%s)에 대한 상태(%s)가 유효하지 않습니다.</font>', 'inicis_payment' ), $txnid, __($order->status, 'woocommerce')));
                        $rst = $this->cancel_request($_REQUEST['P_TID'], __('주문시간 초과오류 : 자동결재취소', 'inicis_payment'), __('CM_CANCEL_100', 'inicis_payment') );  
                        if($rst == "success"){
                            $order->add_order_note( sprintf( __('<font color="red">[결재알림]</font>주문시간 초과오류건(%s)에 대한 자동 결제취소가 진행되었습니다.', 'inicis_payment'), $_REQUEST['P_TYPE']) );
                            update_post_meta($order->id, '_codem_inicis_order_cancelled', TRUE);
                        } else {
                            $order->add_order_note( sprintf( __('<font color="red">주문시간 초과오류건(%s)에 대한 자동 결제취소가 실패했습니다.</font>', 'inicis_payment'), $_REQUEST['P_TYPE']) );
                        }
                        echo "FAIL";
                        exit();
                    }
                    
                    if($this->validate_txnid($order, $txnid) == false){
                        $this->inicis_noti_print_log( sprintf( __( '유효하지 않은 주문번호(%s) 입니다', 'inicis_payment'), $txnid) );
                        $order->add_order_note( sprintf( __('<font color="red">유효하지 않은 주문번호(%s) 입니다.</font>', 'inicis_payment'), $txnid) );  
                        echo "FAIL";
                        exit();
                    }
                    
                    $checkhash = hash('sha512', "$this->merchant_id|$txnid||$order_total|$productinfo|$order->billing_first_name|$order->billing_email|||||||||||");
                    
                    if($hash != $checkhash){                
                        $this->inicis_noti_print_log("$this->merchant_id|$txnid||$order_total|$productinfo|$order->billing_first_name|$order->billing_email|||||||||||");
                        $this->inicis_noti_print_log( sprintf( __( '주문요청(%s)에 대한 위변조 검사 오류입니다.', 'inicis_payment'), $txnid) );  
                        $order->add_order_note( sprintf( __('<font color="red">주문요청(%s)에 대한 위변조 검사 오류입니다.</font>', 'inicis_payment'), $txnid) );  
                        echo "FAIL";
                        exit();
                    }
                                    
                    $inimx_txnid = $_REQUEST['P_OID'];
                    $inimx_orderid = explode('_', $inimx_txnid);
                    $inimx_orderid = (int)$inimx_orderid[0];
                    
                    if( $txnid != $inimx_txnid || $orderid != $inimx_orderid ){
                        $this->inicis_noti_print_log( sprintf( __( '주문요청(%s, %s, %s, %s)에 대한 위변조 검사 오류입니다. 결재는 처리되었으나, 결재요청에 오류가 있습니다. 이니시스 결재내역을 확인하신 후, 고객에게 연락을 해주시기 바랍니다.', 'inicis_payment' ), $txnid, $inimx_txnid, $orderid, $inimx_orderid) );
                        $order->add_order_note( sprintf( __('<font color="red">주문요청(%s, %s, %s, %s)에 대한 위변조 검사 오류입니다. 결재는 처리되었으나, 결재요청에 오류가 있습니다. 이니시스 결재내역을 확인하신 후, 고객에게 연락을 해주시기 바랍니다.</font>', 'inicis_payment' ), $txnid, $inimx_txnid, $orderid, $inimx_orderid) );  
                        echo "FAIL";
                        exit();
                    }
    
                    add_post_meta($orderid, "inicis_paymethod", $_REQUEST['P_TYPE']);
                    add_post_meta($orderid, "inicis_paymethod_tid",  $_REQUEST['P_TID']);

                    $this->inicis_noti_print_log( sprintf( __( '주문이 완료되었습니다. 결제방법 : [모바일] %s, 이니시스 거래번호(TID) : %s, 몰 고유 주문번호 : %s', 'inicis_payment'), $_REQUEST['P_TYPE'], $_REQUEST['P_TID'], $_REQUEST['P_OID'] ) );
                    $order->add_order_note( sprintf( __( '주문이 완료되었습니다. 결제방법 : [모바일] %s, 이니시스 거래번호(TID) : <a href="https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=%s" target=_blank>[영수증 확인]</a>, 몰 고유 주문번호 : %s', 'inicis_payment'),$_REQUEST['P_TYPE'], $_REQUEST['P_TID'], $_REQUEST['P_OID'] ) );

                    $order->payment_complete();

                    $woocommerce->cart->empty_cart();
                    
                    delete_post_meta($orderid, "ini_rn");
                    delete_post_meta($orderid, "ini_enctype");
                    //delete_post_meta($orderid, 'txnid');
                    
                    echo "OK";
                    exit();
                }else{
                    $this->inicis_noti_print_log( __( '유효하지않은 주문입니다. (invalid status or txnid)', 'inicis_payment') );
                    echo "FAIL";
                    exit();
                }   
            }
        }        
        
        function successful_request_mobile_return( $posted ) {
            global $woocommerce;

            if($this->id == 'inicis_bank' && wp_is_mobile()) {
                $get_type = $_GET['type'];
                $tmp_rst = explode(',', $get_type);
                $tmp_oid = $tmp_rst[1];
                $tmp_rst = explode('=', $tmp_oid);
                $oid = $tmp_rst[1];
                $tmp_rst = explode('_', $oid);
                $orderid = $tmp_rst[0];

                $order = new WC_Order($orderid);
                if(in_array($order->get_status(), array('pending', 'failed'))){
                    wc_add_notice( __('결제를 취소하셨습니다.  (ERROR: 0xF53D)', 'inicis_payment' ), 'error' );
                    wp_redirect( WC()->cart->get_checkout_url() );
                    exit();
                }
            }
        }
       
        
        function process_payment($orderid){
    
            global $woocommerce;
    
            $order = new WC_Order($orderid);
            
            //WooCommerce Version Check
            if(version_compare( WOOCOMMERCE_VERSION, '2.1.0', '>=' )) { 
                return array(
                    'result'    => 'success',
                    'redirect'  => $order->get_checkout_payment_url( true ),
                    'order_id'  => $order->id,
                    'order_key' => $order->order_key
                );
            } else { 
                return array(
                    'result' => 'success', 
                    'redirect' => add_query_arg('order',$order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('woocommerce_pay_page_id')))),
                    'order_id'  => $order->id,
                    'order_key' => $order->order_key
                );
            }
        }
        
        function receipt_page( $order ) {
        }
        
        function encrypt_notification($data, $hash) {
            $param = array(
                'txnid' => $data,
                'hash' => $hash 
            );
            
            return aes256_cbc_encrypt("inicis-for-woocommerce", json_encode($param), "codemshop" );
        }

        function decrypt_notification($data) {
            return json_decode(aes256_cbc_decrypt("inicis-for-woocommerce", $data, "codemshop" ));
        }
        
        function make_txnid($order) {
        	$txnid = get_post_meta($order->id, 'txnid', true);
			if( empty($txnid) ) {
	            $txnid = $order->id . '_' . date("ymd") . '_' . date("his");
	            update_post_meta($order->id, 'txnid', $txnid);
			}
            return $txnid;
        }
        
        function validate_txnid($order, $txnid) {
            $org_txnid = get_post_meta($order->id, 'txnid', true);
            return $org_txnid == $txnid;
        }
        
        function make_product_info($order) {
            $items = $order->get_items();
            
            if(count($items) == 1){
                $keys = array_keys($items);
                return $items[$keys[0]]['name'];
            }else{
                $keys = array_keys($items);
                return sprintf( __('%s 외 %d건', 'inicis_payment'), $items[$keys[0]]['name'], count($items)-1);
            }
        }
        
        function wp_ajax_generate_payment_form()
        {
            global $woocommerce, $inicis_payment;

            $orderid = $_REQUEST['orderid'];

            if (!file_exists($this->settings['libfolder'] . "/libs/INILib.php")) {
                wp_send_json_error(__('결제오류 : 상점 키파일 설정에 문제가 있습니다. 사이트 관리자에게 문의하여 주십시오.', 'inicis_payment'));
            }

            try {
                require_once($this->settings['libfolder'] . "/libs/INILib.php");
            } catch (Exception $e) {
                wp_send_json_error(__('결제오류 : 결제 모듈을 불러올 수 없습니다. 사이트 관리자에게 문의하여 주십시오.', 'inicis_payment') . ' [' . $e->getMessage() . ']');
            }

            $use_ssl = get_option('woocommerce_force_ssl_checkout');

            //옵션값 기준으로 옵션 설정
            $arr_accept_method = array();
            if (wp_is_mobile()) {
                $arr_accept_method[] = 'ismart_use_sign=Y';
                $arr_accept_method[] = 'twotrs_isp=Y';
            }

            if ($this->id == 'inicis_card' && !empty($this->settings)) {
                if ($this->settings['cardpoint'] == 'yes') {
                    $arr_accept_method[] = 'cardpoint';
                }
                if ($this->settings['skincolor'] != '') {
                    $arr_accept_method[] = $this->settings['skincolor'];
                }
                $acceptmethod = implode(":", $arr_accept_method);
            } else if ($this->id == 'inicis_bank' && !empty($this->settings)) {
                if (wp_is_mobile()) {
                    if ($this->settings['receipt'] == 'no') {
                        $arr_accept_method[] = 'bank_receipt=N';
                    }
                    $acceptmethod = implode("&", $arr_accept_method);
                } else {
                    if ($this->settings['receipt'] == 'no') {
                        $arr_accept_method[] = 'no_receipt';
                    }
                    $acceptmethod = implode(":", $arr_accept_method);
                }
            } else if ($this->id == 'inicis_vbank' && !empty($this->settings)) {
                if (wp_is_mobile()) {
                    if ($this->settings['receipt'] == 'yes') {
                        $arr_accept_method[] = 'vbank_receipt=Y';
                    }
                    $acceptmethod = implode("&", $arr_accept_method);
                } else {
                    if ($this->settings['receipt'] == 'yes') {
                        $arr_accept_method[] = 'va_receipt';
                    }
                    $acceptmethod = implode(":", $arr_accept_method);
                }
            } else if ($this->id == 'inicis_hpp' && !empty($this->settings)) {
                if (!empty($this->settings['hpp_method'])) {
                    $arr_accept_method[] = 'HPP('.$this->settings['hpp_method'].')';
                } else {
                    $arr_accept_method[] = 'HPP(2)';
                }
                $acceptmethod = implode(":", $arr_accept_method);
            } else if ($this->id == 'inicis_escrow_bank' && !empty($this->settings)) {
                if ($this->settings['receipt'] == 'no') {
                    $arr_accept_method[] = 'no_receipt';
                }
                $acceptmethod = implode(":", $arr_accept_method);
            } else {
                $acceptmethod = '';
            }

            $userid = get_current_user_id();
            $order = new WC_Order($orderid);
            $txnid = $this->make_txnid($order);
            $productinfo = $this->make_product_info($order);
            $order_total = $this->inicis_get_order_total($order);

            $inipay = new INIpay50();
            $inipay->SetField("inipayhome", $this->settings['libfolder']);
            $inipay->SetField("type", "chkfake");
            $inipay->SetField("debug", "true");
            $inipay->SetField("enctype", "asym");
            $inipay->SetField("admin", $this->merchant_pw);
            $inipay->SetField("checkopt", "false");
            $inipay->SetField("mid", $this->merchant_id);
            $inipay->SetField("price", $order_total);
            $inipay->SetField("nointerest", $this->settings['nointerest']);
            $inipay->SetField("quotabase", mb_convert_encoding($this->settings['quotabase'], "EUC-KR", "UTF-8"));

            $inipay->startAction();

            if ($inipay->GetResult("ResultCode") != "00") {
                wp_send_json_error($inipay->GetResult("ResultMsg"));
            }
            
            update_post_meta($orderid, "ini_rn", $inipay->GetResult("rn"));
            update_post_meta($orderid, "ini_enctype", $inipay->GetResult("enctype"));
            
            if (wp_is_mobile()) {
                $str = "$this->merchant_id|$txnid||$order_total|$productinfo|$order->billing_first_name|$order->billing_email|||||||||||";
                $hash = hash('sha512', $str);
                $notification = $this->encrypt_notification($txnid, $hash);
                ob_start();
                include($inicis_payment->plugin_path() . '/templates/payment_form_mobile.php');
                $form_tag = ob_get_clean();
            } else {
                $str = "$this->merchant_id|$txnid|$userid|$order_total|$productinfo|$order->billing_first_name|$order->billing_email|||||||||||";
                $hash = hash('sha512', $str);
                ob_start();
                include($inicis_payment->plugin_path() . '/templates/payment_form_pc.php');
                $form_tag = ob_get_clean();
            }

            wp_send_json_success('<div data-id="mshop-payment-form" style="display:none">' . $form_tag . '</div>');
        }
        
        function successful_request_cancelled( $posted ) {
            global $woocommerce;
    
            require_once($this->settings['libfolder']."/libs/INILib.php");
            $inipay = new INIpay50();
            
            //$inipay->SetField("inipayhome", $_REQUEST['home']);
            $inipay->SetField("inipayhome", $this->settings['libfolder']);
            $inipay->SetField("type", "cancel");
            $inipay->SetField("debug", "true");
            $inipay->SetField("mid", $_REQUEST['mid']);
            $inipay->SetField("admin", "1111");
            $inipay->SetField("tid", $_REQUEST['tid']);
            $inipay->SetField("cancelmsg", $_REQUEST['msg']);
        
            if($code != ""){
                $inipay->SetField("cancelcode", $_REQUEST['code']);
            }
    
            $inipay->startAction();
            
            if($inipay->getResult('ResultCode') == "00"){
                echo "success";
                return;
                //exit();
            }else{
                echo $inipay->getResult('ResultMsg');
                return;
                //exit();
            }
        }

        function check_inicis_payment_response() {
            if (!empty($_REQUEST)) {
                	
                header('HTTP/1.1 200 OK');
				header("Content-Type: text; charset=euc-kr");
				header("Cache-Control: no-cache");
				header("Pragma: no-cache");

                if (!empty($_REQUEST['type'])) {
                    if(strpos($_REQUEST['type'],'?') !== false) {
                        $return_type = explode('?', $_REQUEST['type']);
                        $_REQUEST['type'] = $return_type[0];
                        $tmp_status = explode('=', $return_type[1]);
                        $_REQUEST['P_STATUS'] = $tmp_status[1];
                    } else {
                        $return_type = explode(',', $_REQUEST['type']);
                    }

                    if( $_REQUEST['txnid'] ) {
                        $orderid = explode('_', $_REQUEST['txnid']);
                    } else if( $_POST['P_NOTI'] ) {
                        $notification = $this->decrypt_notification($_POST['P_NOTI']);
                        $orderid = explode('_', $notification->txnid);
                    } else if( $_REQUEST['P_OID'] ) {
                        $orderid = explode('_', $_REQUEST['P_OID']);
                    } else if( $_GET['oid'] ) {
                        $orderid = explode('_', $_GET['oid']);
                    } else if ( $return_type[1] ) {
                        $temp_oid = explode('=', $return_type[1]);
                        $orderid = explode('_', $temp_oid[1]);
                    }
                    
                    if( !empty( $orderid ) ) {
                        $orderid = (int)$orderid[0];
                        $order = new WC_Order($orderid);
                    }
                    
                    switch($return_type[0]) {
                        case "cancelled" :
                            $this->successful_request_cancelled($_POST);
                            $this->inicis_redirect_page($order);
                            break;
                        case "pc" :
                            $this->successful_request_pc($_POST);
                            $this->inicis_redirect_page($order);
                            break;
                        case "vbank_noti" :
                            $this->successful_request_vbank_noti($_POST);
                            $this->inicis_redirect_page($order);
                            break;
                        case "mobile_next" :
                            $this->successful_request_mobile_next($_POST);
                            $this->inicis_redirect_page($order);
                            break;
                        case "mobile_noti" :
                            $this->successful_request_mobile_noti($_POST);
                            $this->inicis_redirect_page($order);
                            break;
                        case "mobile_return" :
                            $this->successful_request_mobile_return($_POST);
                            $this->inicis_redirect_page($order);
                            break;
                        case "cancel_payment" :
                            do_action("valid-inicis-request_cancel_payment", $_POST);
                            $this->inicis_redirect_page($order);
                            break;
                        case "delivery": 
                            if(get_class($this) == 'WC_Gateway_Inicis_Escrow_bank') {
                                $this->inicis_escrow_delivery_add($_POST);    
                            }
                            break; 
                        case "delivery_okay":
                            if(get_class($this) == 'WC_Gateway_Inicis_Escrow_bank') {
                                $this->inicis_escrow_delivery_okay($_POST);    
                            }
                            break; 
                        case "confirm":
                            if(get_class($this) == 'WC_Gateway_Inicis_Escrow_bank') {
                                $this->inicis_escrow_request_confirm($_POST);
                                $this->inicis_redirect_page($order);    
                            }
                            break; 
                        case "denyconfirm":
                            if(get_class($this) == 'WC_Gateway_Inicis_Escrow_bank') {
                                $this->inicis_escrow_request_denyconfirm($_POST);
                            }
                            break; 
                        case "cancel":
                            if(get_class($this) == 'WC_Gateway_Inicis_Escrow_bank') {
                                $this->inicis_escrow_request_cancel_before_confirm($_POST);
                            }
                            break;
                        case "get_order":
                            if(get_class($this) == 'WC_Gateway_Inicis_Escrow_bank') {
                                $this->inicis_escrow_get_order($_POST);    
                            }
                            break;
                        case "vbank_refund_add":
                            if(get_class($this) == 'WC_Gateway_Inicis_Vbank') {
                                $this->inicis_vbank_refund_add($_POST);
                            }
                            break;
                        case "vbank_refund_modify":
                            if(get_class($this) == 'WC_Gateway_Inicis_Vbank') {
                                $this->inicis_vbank_refund_modify($_POST);
                            }
                            break;
                        default :
                            wp_die( __( '결제 요청 실패 : 관리자에게 문의하세요!', 'inicis_payment' ) );
                            break;
                    }
                } else {
                    wp_die( __( '결제 요청 실패 : 관리자에게 문의하세요!', 'inicis_payment' ) );
                }
            } else {
                wp_die( __( '결제 요청 실패 : 관리자에게 문의하세요!', 'inicis_payment' ) );
            }
        }
        
        function inicis_redirect_page($order) {
            if ( version_compare( WOOCOMMERCE_VERSION, '2.1.0', '>=') ) {
                if( isset( $order ) && !empty( $order ) ) {
                    wp_redirect( $order->get_checkout_order_received_url() );
                } else {
                    if( is_user_logged_in() ){
                        $tmp_myaccount_pid = get_option( 'woocommerce_myaccount_page_id', true );
                        if ( empty( $tmp_myaccount_pid ) ) {
                            $myaccount_page = home_url();
                        } else {
                            $myaccount_page = get_permalink( get_option( 'woocommerce_myaccount_page_id', true ) );
                        }
                        wp_redirect( $myaccount_page );  
                    }else{
                        wp_redirect( $_SERVER['HTTP_REFERER'] );
                    }                 
                }
            } else {
                if( is_user_logged_in() ){
                    $tmp_myaccount_pid = get_option( 'woocommerce_myaccount_page_id', true );
                    if ( empty( $tmp_myaccount_pid ) ) {
                        $myaccount_page = home_url();
                    } else {
                        $myaccount_page = get_permalink( get_option( 'woocommerce_myaccount_page_id', true ) );
                    }
                    wp_redirect( $myaccount_page );
                }else{
                    wp_redirect( $_SERVER['HTTP_REFERER'] );
                }                 
            }            
        }
        
        function inicis_noti_print_log($msg)
        {
            $path = $this->settings['libfolder']."/log/";
            $file = "ININoti" . $this->merchant_id ."_".date("Ymd").".log";
            
            if(!is_dir($path)) 
            {
                mkdir($path, 0755);
            }
            if(!($fp = fopen($path.$file, "a+"))) return 0;
    
            if(fwrite($fp, " ".$msg."\n") === FALSE)
            {
                fclose($fp);
                return 0;
            }
            fclose($fp);
            return 1;
        }

        function inicis_get_order_total($order) {
            if(version_compare( WOOCOMMERCE_VERSION, '2.3.0', '>=' )) {
                return $order->get_total();
            } else {
                return $order->get_order_total();
            }
        }
    }
}