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

class IFW_Meta_Box_Escrow_Register_Delivery {
        public static function output( $post ) {
        global $woocommerce, $post, $wpdb, $wp_roles, $order;
    
        $orderinfo = new WC_Order($post->ID);
    
        //주문에 들어간 상품명 가져오기
        $data = $orderinfo->get_items();
        $product_name = "";
        foreach($data as $item) {
            $product_name = $item['name'];
        }
    
        //주문 총금액 가져와서 소수점 이하 버림처리
        $price_all = $orderinfo -> order_total;
        $price_all = floor($price_all);
    
        //이니페이 설정 페이지 옵션 값에서 가져오기
        $inicis_escrow_bank_setting = get_option('woocommerce_inicis_escrow_bank_settings');
        $delivery_company_name      = $inicis_escrow_bank_setting['delivery_company_name'];     //택배사명
        $delivery_register_name     = $inicis_escrow_bank_setting['delivery_register_name'];    //배송정보 등록자명
        $delivery_sender_name       = $inicis_escrow_bank_setting['delivery_sender_name'];      //송신자명
        $delivery_sender_postnum    = $inicis_escrow_bank_setting['delivery_sender_postnum'];   //송신자우편번호
        $delivery_sender_addr1      = $inicis_escrow_bank_setting['delivery_sender_addr1'];     //송신자 기본주소
        $delivery_sender_addr2      = $inicis_escrow_bank_setting['delivery_sender_addr2'];     //송신자 상세주소
        $delivery_sender_phone      = $inicis_escrow_bank_setting['delivery_sender_phone'];     //송신자 전화번호
        
        $default_data = get_option('woocommerce_inicis_escrow_bank_settings');
        $status_method_arr = $default_data['possible_register_delivery_info_status_for_admin'];
        if(count($status_method_arr) == 1 && empty($status_method_arr[0])) {
            $status_method_arr = array('processing','cancel-request');
        }
        
        $url = admin_url('post.php?post='.$post->ID.'&action=edit');
        
        echo '
        <style type="text/css">
        #mb_inipay { 
            border-bottom: 1px solid #dfdfdf;
            margin:0 -12px;
            padding-bottom:10px;
        }
        #mb_inipay_sub { 
            padding: 0px 12px;  
        }
        .mb_inipay_h4 { 
            margin:0px!important;   
        }
        .mb_inipay_wide { 
            width:100%;
        }
        </style>
        ';
        
        echo '
        <script type="text/javascript">
            function sleep(milliseconds) {
              var start = new Date().getTime();
              for (var i = 0; i < 1e7; i++) {
                if ((new Date().getTime() - start) > milliseconds){
                  break;
                }
              }
            }    
            function onClickCancelRequest(){
                if(confirm("정말 환불 처리하시겠습니까?\n\n처리 이후에 이전 상태로 되돌릴 수 없습니다. 신중하게 선택해주세요.")) {
                    var data = {
                        action: "inicis_escrow_bank_order_cancelled",
                        post_id: '.$post->ID.',
                        inicis_escrow_bank_refund_request: "'.wp_create_nonce('inicis_escrow_bank_refund_request').'",
                    };
            
                    jQuery("[name=\'refund-request\']").attr(\'disabled\',\'true\');
                    jQuery("[name=\'refund-request\']").attr(\'value\', "'.__("처리중...","inicis_payment").'");
                    
                    jQuery.post(ajaxurl, data, function(response) {
                        if( response == "11") {
                            alert("'.__('환불 처리가 완료되었습니다!','inicis_payment').'");
                            location.href="'.$url.'";    
                        } else {
                            alert(response);        
                            alert("'.__('환불 처리가 실패되었습니다!\n\n다시 시도해 주세요!\n\n계속 동일 증상 발생시 주문상태를 확인해주세요!','inicis_payment').'");
                            jQuery("[name=\'refund-request\']").removeAttr("disabled");
                            jQuery("[name=\'refund-request\']").attr(\'value\',"'.__('주문 환불하기','inicis_payment').'");
                            location.href="'.$url.'";
                        }
                    });
                } else {
                    return;
                }
            }
            function checkReceipt(){
                window.open("https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid='.get_post_meta($post->ID, 'inicis_paymethod_tid', true).'");
            } 
        </script>'; ?>
        
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery("#_order_tracking_number").keyup(function(){
                    if(jQuery("#_order_tracking_number").val() != '' && jQuery("#_order_tracking_number").val().length != 0) {
                        jQuery("[name='shipping-number-save-request']").removeAttr('disabled');
                    } else { 
                        jQuery("[name='shipping-number-save-request']").attr('disabled', 'true');
                    }
                });
            });

            function onClickDelivery(){
                if(confirm("배송 정보를 등록하시겠습니까?")){
                    if(jQuery("#_order_shipping_writer").val() != '' && jQuery("#_order_shipping_company").val() != '' && jQuery("#_order_tracking_number").val() != '') { 
                        jQuery("[name='refund-request-inipay']").attr('disabled','true');
                        jQuery("[name='refund-request-inipay']").val('처리중...');
                        jQuery.ajax({
                            type:'POST',
                            dataType:'text',
                            url: '<?php echo home_url().'/wc-api/WC_Gateway_Inicis_Escrow_bank?type=delivery'; ?>',
                            data: {
                                postid: '<?php echo $post->ID; ?>',
                                tid: '<?php $tmp = get_post_meta($post->ID, 'inicis_paymethod_tid', true); echo $tmp; ?>', //거래번호
                                oid: '<?php $tmp = get_post_meta($post->ID, 'txnid', true); echo $tmp; ?>', //주문번호 
                                EscrowType: 'I', //에스크로 타입   I:배송등록, U:배송수정
                                invoice: jQuery("#_order_tracking_number").val(),
                                dlv_name:'<?php echo $delivery_register_name; ?>', //배송정보 등록자(관리자)
                                glsid_temp:'9999', //택배사 코드(9999:기타 택배)
                                dlv_exCode:'9999', //택바사 코드(9999:기타 택배)
                                dlv_exName:'<?php echo $delivery_company_name; ?>', //택배사명(기타일경우 입력)
                                dlv_charge:'SH', //배송비 지급방법 (SH:판매자부담, BH:구매자부담), 우선 SH로 고정처리(모든 비용을 포함하여 계산하기 때문)
                                dlv_invoiceday:'<?php echo date("Y-m-d H:i:s"); ?>', //배송등록 확인일시 (YYYY-MM-DD HH:MM:SS)
                                sendName :'<?php echo $delivery_sender_name; ?>', //송신자 이름
                                sendPost :'<?php echo $delivery_sender_postnum; ?>', //송신자 우편번호
                                sendAddr1 :'<?php echo $delivery_sender_addr1; ?>', //송신자 주소1
                                sendAddr2 :'<?php echo $delivery_sender_addr2; ?>', //송신자 주소2
                                sendTel :'<?php echo $delivery_sender_phone; ?>', //송신자 전화번호
                                recvName :'<?php echo $orderinfo->billing_first_name; ?>', //수신자 이름
                                recvPost :'<?php if ( ! class_exists( 'MC_MShop' ) ) { $tmp = get_post_meta($post->ID, '_shipping_postcode', true); } else { $tmp = get_post_meta($post->ID, '_mshop_shipping_address-postnum', true); }  echo $tmp; ?>', //수신자 우편번호
                                recvAddr :'<?php $tmp = get_post_meta($post->ID, '_shipping_address_1', true); $tmp2 = get_post_meta($post->ID, '_shipping_address_2', true); echo $tmp.' '.$tmp2; ?>', //수신자 주소
                                recvTel :'<?php $tmp = get_post_meta($post->ID, '_billing_phone', true); echo $tmp; ?>', //수신자 전화번호
                                goodsCode :'<?php echo $orderinfo->id; ?>', //상품코드 (선택), 여기서는 주문ID(포스트ID)값을 임의로 입력
                                goods :'<?php echo $product_name; ?>', //상품명(필수)
                                goodCnt :'', //상품 수량(선택)
                                price :<?php echo $price_all; ?>, //상품 가격(필수)
                                reserved1 :'', //상품옵션1(선택)
                                reserved2 :'', //상품옵션2(선택)
                                reserved3 :'', //상품옵션3(선택)
                                mid : '<?php $tmp = get_post_meta($post->ID, '_payment_method', true); $tmp_setting = get_option('woocommerce_'.$tmp.'_settings'); echo $tmp_setting['merchant_id']; ?>'  //이니페이 에스크로 상점 아이디값 (필수)
                            },
                            success:function(data, textStatus, jqXHR){
                                if(data.match("success")) {
                                    register_escrow_once(); //최초 배송정보 등록 확인용 코드 추가 처리
                                    alert('배송정보 등록이 완료되었습니다.\n\n고객님께 물품 수령후에 에스크로 구매확인 및 거절 의사를 표시 요청을 하셔야 합니다.');
                                    location.href='<?php echo $url; ?>';
                                } else {
                                    alert('관리자에게 문의하여주세요.\n\n에러 메시지 : \n' + data);
                                    jQuery("[name='refund-request-inipay']").removeAttr("disabled");
                                    jQuery("[name='refund-request-inipay']").val('배송정보 등록');
                                    location.href='<?php echo $url; ?>';    
                                }
                            }
                        });
                    } else {
                        alert("배송정보 등록을 위한 값이 지정되지 않았습니다.\n\n - 결제 설정 페이지에서 배송정보 등록을 위한 항목을 설정하여 주세요\n\n - 송장번호를 입력하셨는지 확인해 주세요");
                        return;
                    }
                } else {
                    return; 
                }
            }

            function onClickDeliveryModify(){
                if(confirm("배송 정보를 수정하시겠습니까?")){
                    jQuery.ajax({
                        type:'POST',
                        dataType:'text',
                        url: '<?php echo home_url().'/wc-api/WC_Gateway_Inicis_Escrow_bank?type=delivery'; ?>',
                        data: {
                            postid: '<?php echo $post->ID; ?>',
                            tid: '<?php $tmp = get_post_meta($post->ID, 'inicis_paymethod_tid', true); echo $tmp; ?>', //거래번호
                            oid: '<?php $tmp = get_post_meta($post->ID, 'txnid', true); echo $tmp; ?>', //주문번호 
                            EscrowType: 'U', //에스크로 타입   I:배송등록, U:배송수정
                            invoice: jQuery("#_order_tracking_number").val(),
                            dlv_name:'<?php echo $delivery_register_name; ?>', //배송정보 등록자(관리자)
                            glsid_temp:'9999', //택배사 코드(9999:기타 택배)
                            dlv_exCode:'9999', //택바사 코드(9999:기타 택배)
                            dlv_exName:'<?php echo $delivery_company_name; ?>', //택배사명(기타일경우 입력)
                            dlv_charge:'SH', //배송비 지급방법 (SH:판매자부담, BH:구매자부담), 우선 SH로 고정처리(모든 비용을 포함하여 계산하기 때문)
                            dlv_invoiceday:'<?php echo date("Y-m-d H:i:s"); ?>', //배송등록 확인일시 (YYYY-MM-DD HH:MM:SS)
                            sendName :'<?php echo $delivery_sender_name; ?>', //송신자 이름
                            sendPost :'<?php echo $delivery_sender_postnum; ?>', //송신자 우편번호
                            sendAddr1 :'<?php echo $delivery_sender_addr1; ?>', //송신자 주소1
                            sendAddr2 :'<?php echo $delivery_sender_addr2; ?>', //송신자 주소2
                            sendTel :'<?php echo $delivery_sender_phone; ?>', //송신자 전화번호
                            recvName :'<?php echo $orderinfo->billing_first_name; ?>', //수신자 이름
                            recvPost :'<?php if ( ! class_exists( 'MC_MShop' ) ) { $tmp = get_post_meta($post->ID, '_shipping_postcode', true); } else { $tmp = get_post_meta($post->ID, '_mshop_shipping_address-postnum', true); }  echo $tmp; ?>', //수신자 우편번호
                            recvAddr :'<?php $tmp = get_post_meta($post->ID, '_shipping_address_1', true); $tmp2 = get_post_meta($post->ID, '_shipping_address_2', true); echo $tmp.' '.$tmp2; ?>', //수신자 주소
                            recvTel :'<?php $tmp = get_post_meta($post->ID, '_billing_phone', true); echo $tmp; ?>', //수신자 전화번호
                            goodsCode :'<?php echo $orderinfo->id; ?>', //상품코드 (선택), 여기서는 주문ID(포스트ID)값을 임의로 입력
                            goods :'<?php echo $product_name; ?>', //상품명(필수)
                            goodCnt :'', //상품 수량(선택)
                            price :<?php echo $price_all; ?>, //상품 가격(필수)
                            reserved1 :'', //상품옵션1(선택)
                            reserved2 :'', //상품옵션2(선택)
                            reserved3 :'', //상품옵션3(선택)
                            mid : '<?php $tmp = get_post_meta($post->ID, '_payment_method', true); $tmp_setting = get_option('woocommerce_'.$tmp.'_settings'); echo $tmp_setting['merchant_id']; ?>'  //이니페이 에스크로 상점 아이디값 (필수)
                        },
                        success:function(data, textStatus, jqXHR){
                            if(data.match("success")) {
                                register_escrow_once(); //최초 배송정보 등록 확인용 코드 추가 처리
                                alert('배송정보 수정이 완료되었습니다.\n\n고객님께 물품 수령후에 에스크로 구매확인 및 거절 의사를 표시 요청을 하셔야 합니다.');
                                location.href='<?php echo $url; ?>';
                            } else {
                                alert('관리자에게 문의하여주세요.\n\n에러 메시지 : \n' + data);
                                location.href='<?php echo $url; ?>';    
                            }
                            
                        }
                    });
                } else { 
                    return;
                }
            }
            function register_escrow_once(){
                jQuery.ajax({
                    type:'POST',
                    dataType:'text',
                    url: '<?php echo home_url().'/wc-api/WC_Gateway_Inicis_Escrow_bank?type=delivery_okay'; ?>',
                    data: {
                        postid: '<?php echo $post->ID; ?>', //주문번호
                        tid: '<?php $tmp = get_post_meta($post->ID, 'inicis_paymethod_tid', true); echo $tmp; ?>', //거래번호
                        oid: '<?php $tmp = get_post_meta($post->ID, 'txnid', true); echo $tmp; ?>', //주문번호 
                    },
                    success:function(data, textStatus, jqXHR){}
                });
            }
        </script>   
        
        <?php   
        if( in_array($orderinfo->status, $status_method_arr) && (get_post_meta($post->ID, '_payment_method', true) == 'inicis_escrow_bank') ){
    
            /* 이니시스 에스크로 배송등록 버튼 배송수정 버튼으로 표시처리 추가 */
            $shipping_num = get_post_meta($post->ID, 'shipping_number', true);
            $order_cancelled = get_post_meta($post->ID, '_inicis_escrow_order_cancelled', true);
            $delivery_add = get_post_meta($post->ID, 'inicis_paymethod_escrow_delivery_add', true);
            $order_confirm = get_post_meta($post->ID, '_inicis_escrow_order_confirm', true);
            $order_reject = get_post_meta($post->ID, '_inicis_escrow_order_confirm_reject', true);   
            
            if(!empty($order_cancelled) && $delivery_add == 'yes') {
                echo '
                <div id="mb_inipay" class="total_row">
                    <div id="mb_inipay_sub">
                        <div id="mb_inipay_group" class="totals_group">
                            <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_discount">배송정보 등록자 이름</label></h4>
                            <input type="text" class="mb_inipay_wide" id="_order_discount" name="_order_shipping_writer" placeholder="배송정보 등록자 이름" value="'.$delivery_register_name.'" title="결제 플러그인 설정에서 배송정보 등록자 이름을 지정할수 있습니다." disabled></p>
                            <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_discount">배송업체</label></h4>
                            <input type="text" class="mb_inipay_wide" id="_order_discount" name="_order_shipping_company" placeholder="배송업체 이름" value="'.$delivery_company_name.'" title="결제 플러그인 설정에서 배송회사 이름을 지정할수 있습니다." disabled></p>
                            <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_discount">송장번호</label></h4>
                            <input type="text" class="mb_inipay_wide" id="_order_discount" name="_order_tracking_number" placeholder="-를 제외한 숫자만 입력해주세요." value="'.$shipping_num.'" title="배송시 사용된 송장 번호를 입력해주세요." disabled></p>';
                echo '</div>
                    </div>
                </div>
                <p class="order-info">
                <input type="button" class="button button-small tips" name="shipping-number-save-request" value="'.__('배송정보 등록','inicis_payment').'" onClick="javascript:onClickDelivery();" data-tip="에스크로 결제를 환불처리를 합니다.">
                <input type="button" class="button button-small tips" name="refund-request" value="'.__('환불 완료','inicis_payment').'" data-tip="에스크로 결제를 환불처리를 합니다." disabled>
                <input type="button" class="button button-small tips" name="refund-request-check-receipt" value="'.__('영수증 확인','inicis_payment').'" onClick="javascript:checkReceipt();" data-tip="영수증을 확인합니다.">';
                echo '</p>';
            } else {
                echo '
                <div id="mb_inipay" class="total_row">
                    <div id="mb_inipay_sub">
                        <div id="mb_inipay_group" class="totals_group">
                            <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_shipping_writer">배송정보 등록자 이름</label></h4>
                            <input type="text" class="mb_inipay_wide" id="_order_shipping_writer" name="_order_shipping_writer" placeholder="배송정보 등록자 이름" value="'.$delivery_register_name.'" title="결제 플러그인 설정에서 배송정보 등록자 이름을 지정할수 있습니다." disabled></p>
                            <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_shipping_company">배송업체</label></h4>
                            <input type="text" class="mb_inipay_wide" id="_order_shipping_company" name="_order_shipping_company" placeholder="배송업체 이름" value="'.$delivery_company_name.'" title="결제 플러그인 설정에서 배송회사 이름을 지정할수 있습니다." disabled></p>
                            <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_tracking_number">송장번호</label></h4>
                            <input type="text" class="mb_inipay_wide" id="_order_tracking_number" name="_order_tracking_number" placeholder="-를 제외한 숫자만 입력해주세요." value="'.$shipping_num.'" title="배송시 사용된 송장 번호를 입력해주세요."></p>';

                echo '</div>
                    </div>
                </div>
                <p class="order-info">';
    
                if(!empty($delivery_add) && $delivery_add == 'yes') {
                    if(empty($order_confirm) && empty($order_reject) && empty($shipping_num)) {
                    } else if($orderinfo->status == 'cancel-request' || $orderinfo->status == 'cancelled'){
                        echo '<script type="text/javascript">jQuery("#_order_tracking_number").attr("disabled", "true");</script>';
                    } else { 
                        echo '<input type="button" class="button button-small tips" name="refund-request-inipay" value="'.__('배송정보 수정','inicis_payment').'" onClick="javascript:onClickDeliveryModify();" data-tip="에스크로 결제건에 배송정보를 수정합니다.">&nbsp;';
                    }
                } else {
                    if(empty($order_confirm) && empty($order_reject) && empty($shipping_num)) {
                    } else {
                        echo '<input type="button" class="button button-small tips" name="refund-request-inipay" value="'.__('배송정보 등록','inicis_payment').'" onClick="javascript:onClickDelivery();" data-tip="에스크로 결제건에 배송정보를 등록합니다.">&nbsp;';
                    }
                }           
                
                if($delivery_add == 'yes' && $orderinfo->status == "cancel-request"){
                    echo '<input type="button" class="button button-small tips" name="refund-request" value="'.__('구매거절확인(환불처리)','inicis_payment').'" onClick="javascript:onClickCancelRequest();" data-tip="에스크로 결제를 환불처리를 합니다.">&nbsp;';
                }
                else if($delivery_add == 'yes') { 
                    echo '<input type="button" class="button button-small tips" name="refund-request" value="'.__('환불불가','inicis_payment').'" title="배송정보가 등록된 경우에는 사용자가 구매 확인 또는 거절 처리를 하기전에 환불처리를 할 수 없습니다." disabled>&nbsp;';
                } else {
                    echo '<input type="button" class="button button-small tips" name="shipping-number-save-request" value="'.__('배송정보 등록','inicis_payment').'" onClick="javascript:onClickDelivery();" data-tip="에스크로 결제를 환불처리를 합니다." disabled>&nbsp;';
                    echo '<input type="button" class="button button-small tips" name="refund-request" value="'.__('환불하기','inicis_payment').'" onClick="javascript:onClickCancelRequest();" data-tip="에스크로 결제를 환불처리를 합니다.">&nbsp;';
                }
                echo '<input type="button" class="button button-small tips" name="refund-request-check-receipt" value="'.__('영수증 확인','inicis_payment').'" onClick="javascript:checkReceipt();" data-tip="영수증을 확인합니다.">&nbsp;';
                wp_nonce_field('refund_request','refund_request');
                echo '</p>';
            }
        } 
    }

        public static function save( $post_id, $post ) {
    }
}