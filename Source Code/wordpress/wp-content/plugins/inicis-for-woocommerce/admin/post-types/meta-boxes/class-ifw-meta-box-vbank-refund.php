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

class IFW_Meta_Box_Vbank_Refund {
        public static function output( $post ) {
        global $woocommerce, $post, $wpdb, $wp_roles, $order, $inicis_payment;

		$woocommerce->payment_gateways();

	    $order = new WC_Order($post->ID);
		$payment_method = get_post_meta($order->id, '_payment_method', true);
		$tid = get_post_meta($order->id, 'inicis_paymethod_tid', true);
        $vbank_refund_add = get_post_meta($order->id, 'inicis_paymethod_vbank_add', true);
        $vbank_noti_received = get_post_meta($order->id, 'inicis_vbank_noti_received', true);

        $url = admin_url('post.php?post='.$order->id.'&action=edit');

?>
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
        .mb_inipay_note {
            padding: 10px;
            background: #efefef;
            position: relative;
            margin-top: 10px;
        }
        .mb_inipay_note p {
            margin: 0;
            padding: 0;
            word-wrap: break-word;
        }
        </style>

        <script type="text/javascript">
            function checkIsJSON(value) {
                try {
                    JSON.parse(value);
                    return true;
                } catch (ex) {
                    return false;
                }
            }
            function sleep(milliseconds) {
              var start = new Date().getTime();
              for (var i = 0; i < 1e7; i++) {
                if ((new Date().getTime() - start) > milliseconds){
                  break;
                }
              }
            }
            function onClickCancelRequest(){
                if(confirm("정말 취소 처리하시겠습니까?\n\n처리 이후에 이전 상태로 되돌릴 수 없습니다. 입금 통보 전에 취소하시는 경우이기 때문에 고객의 요청이 아니라면 취소 처리시 주의하여 주세요.")) {
                    var data = {
                        action: "<?php echo 'refund_request_' . $payment_method; ?>",
                        order_id: "<?php echo $post->ID; ?>",
                        refund_request: "<?php echo wp_create_nonce('refund_request'); ?>"
                    };

                    jQuery("[name='refund-request']").attr('disabled','true');
                    jQuery("[name='refund-request']").attr('value', "<?php echo __("처리중...","inicis_payment"); ?>");

                    jQuery.post(ajaxurl, data, function(response) {
                        if (response) {
                            if (checkIsJSON(response)) {
                                response = JSON.parse(response);
                            }
                            if (response.success == 'true' || response.success) {
                                alert(response.data);
                                location.reload();
                            } else {
                                alert(response.data);
                                jQuery("[name='refund-request']").removeAttr('disabled');
                                jQuery("[name='refund-request']").attr('value', "취소하기");
                            }
                        } else {
                            alert('취소요청 결과를 수신하지 못하였습니다.\n처리 결과 확인을 위해 영수증을 확인해 보시기 바랍니다.');
                            jQuery("[name='refund-request']").removeAttr('disabled');
                            jQuery("[name='refund-request']").attr('value', "취소하기");
                        }
                    });
                } else {
                    return;
                }
            }
            function onClickRefundRequest(){
                if(confirm("정말 환불 처리하시겠습니까?\n\n처리 이후에 이전 상태로 되돌릴 수 없습니다. 신중하게 선택해주세요.")) {
                    var data = {
                        action: "<?php echo $payment_method; ?>_order_cancelled",
                        post_id: "<?php echo $post->ID; ?>",
                        inicis_vbank_refund_request: "<?php echo wp_create_nonce('inicis_vbank_refund_request'); ?>"
                    };

                    jQuery("[name='vbank-refund-request']").attr('disabled','true');
                    jQuery("[name='vbank-refund-request']").attr('value', "<?php echo __("처리중...","inicis_payment"); ?>");

                    jQuery.post(ajaxurl, data, function(response) {
                        if (response) {
                            if (checkIsJSON(response)) {
                                response = JSON.parse(response);
                            }
                            if (response.success == 'true' || response.success) {
                                alert(response.data);
                                location.reload();
                            } else {
                                alert(response.data);
                                jQuery("[name='vbank-refund-request']").removeAttr('disabled');
                                jQuery("[name='vbank-refund-request']").attr('value', "<?php echo __('환불하기','inicis_payment'); ?>");
                            }
                        } else {
                            alert("<?php echo __('환불 처리가 실패되었습니다!\n\n다시 시도해 주세요!\n\n계속 동일 증상 발생시 주문상태를 확인해주세요!','inicis_payment'); ?>");
                            jQuery("[name='vbank-refund-request']").removeAttr('disabled');
                            jQuery("[name='vbank-refund-request']").attr('value', "<?php echo __('환불하기','inicis_payment'); ?>");
                        }
                    });
                } else {
                    return;
                }
            }
            function checkReceipt(){
                window.open("https://iniweb.inicis.com/app/publication/apReceipt.jsp?noMethod=1&noTid=<?php echo get_post_meta($post->ID, 'inicis_paymethod_tid', true); ?>");
            }
            function onVbankRefundAdd() {
                if(confirm("환불 정보를 등록하시겠습니까?")) {
                    if (jQuery("#_order_refund_bankcode").val() != "-1" && jQuery("#_order_refund_vacc_num").val() != "" && jQuery("#_order_refund_vacc_name").val() != "" && jQuery("#_order_refund_reason").val() != "") {
                        jQuery("[name='vbank-refund-request-vacc-add']").attr('disabled', 'true');
                        jQuery("[name='vbank-refund-request-vacc-add']").val('처리중...');

                        jQuery.ajax({
                            type: 'POST',
                            dataType: 'text',
                            url: '<?php echo home_url().'/wc-api/WC_Gateway_Inicis_Vbank?type=vbank_refund_add'; ?>',
                            data: {
                                action: 'vbank_refund_add',
                                orderid: '<?php echo $post->ID; ?>',
                                refund_bankcode: jQuery("#_order_refund_bankcode").val(),
                                refund_vaccnum: jQuery("#_order_refund_vacc_num").val(),
                                refund_vaccname: jQuery("#_order_refund_vacc_name").val(),
                                refund_reason: jQuery("#_order_refund_reason").val(),
                                refund_wpnonce: '<?php echo wp_create_nonce( 'inicis_vbank_refund_add' ); ?>'
                            },
                            success: function (data, textStatus, jqXHR) {
                                if (data.match("success")) {
                                    alert('환불 정보 등록이 완료되었습니다.\n\n환불 정보를 확인하신 후 환불처리를 진행해 주시기 바랍니다.');
                                    location.href = '<?php echo $url; ?>';
                                } else {
                                    alert('관리자에게 문의하여주세요.\n\n에러 메시지 : \n' + data);
                                    jQuery("[name='vbank-refund-request-vacc-add']").removeAttr("disabled");
                                    jQuery("[name='vbank-refund-request-vacc-add']").val('정보등록');
                                    location.href = '<?php echo $url; ?>';
                                }
                            }
                        });
                    } else {
                        alert("환불 정보를 등록을 위한 값이 지정되지 않았습니다.\n\n - 환불은행, 계좌번호, 계좌주명, 취소사유를 확인하여 입력하여 주세요.");
                        return;
                    }
                }
            }
            function onVbankRefundModify() {
                if(confirm("환불 정보를 수정하시겠습니까?")) {
                    if (jQuery("#_order_refund_bankcode").val() != "-1" && jQuery("#_order_refund_vacc_num").val() != "" && jQuery("#_order_refund_vacc_name").val() != "" && jQuery("#_order_refund_reason").val() != "") {
                        jQuery("[name='vbank-refund-request-vacc-modify']").attr('disabled', 'true');
                        jQuery("[name='vbank-refund-request-vacc-modify']").val('처리중...');

                        jQuery.ajax({
                            type: 'POST',
                            dataType: 'text',
                            url: '<?php echo home_url().'/wc-api/WC_Gateway_Inicis_Vbank?type=vbank_refund_modify'; ?>',
                            data: {
                                action: 'vbank_refund_modify',
                                orderid: '<?php echo $post->ID; ?>',
                                refund_bankcode: jQuery("#_order_refund_bankcode").val(),
                                refund_vaccnum: jQuery("#_order_refund_vacc_num").val(),
                                refund_vaccname: jQuery("#_order_refund_vacc_name").val(),
                                refund_reason: jQuery("#_order_refund_reason").val(),
                                refund_wpnonce: '<?php echo wp_create_nonce( 'inicis_vbank_refund_modify' ); ?>'
                            },
                            success: function (data, textStatus, jqXHR) {
                                if (data.match("success")) {
                                    alert('환불 정보 수정이 완료되었습니다.\n\n환불 정보를 확인하신 후 환불처리를 진행해 주시기 바랍니다.');
                                    location.href = '<?php echo $url; ?>';
                                } else {
                                    alert('관리자에게 문의하여주세요.\n\n에러 메시지 : \n' + data);
                                    jQuery("[name='vbank-refund-request-vacc-modify']").removeAttr("disabled");
                                    jQuery("[name='vbank-refund-request-vacc-modify']").val('정보등록');
                                    location.href = '<?php echo $url; ?>';
                                }
                            }
                        });
                    } else {
                        alert("환불 정보 수정을 위한 값이 지정되지 않았습니다.\n\n - 환불은행, 계좌번호, 계좌주명, 취소사유를 확인하여 입력하여 주세요.");
                        return;
                    }
                }
            }
        </script>

        <?php
        $vbank_refund_bankcode = get_post_meta($post->ID, 'vbank_refund_bankcode', true);
        $vbank_refund_vaccnum = get_post_meta($post->ID, 'vbank_refund_vaccnum', true);
        $vbank_refund_vaccname = get_post_meta($post->ID, 'vbank_refund_vaccname', true);
        $vbank_refund_reason = get_post_meta($post->ID, 'vbank_refund_reason', true);

        function isSelected($num,$val){
            if($num == $val) {
                echo " selected";
            } else {
                return;
            }
        }

        function isDisabled(){
            global $post;
            $vbank_refunded = get_post_meta($post->ID, 'inicis_paymethod_vbank_refunded', true);
            if($vbank_refunded == 'yes'){
                echo ' disabled';
            }
        }
        ?>
        <?php
        if($vbank_noti_received == 'yes') {
        ?>
        <div class="mb_inipay_note">
            <p>이니시스 가상계좌 환불처리는 전액환불만 가능하며, 전액환불이 불가한 경우, 별도로 환불절차를 진행하시기 바랍니다. 가상계좌로 환불 처리를 진행하시는 경우, 이니시스 계약에 따라 환불 수수료가 부과됩니다.</p>
        </div>
            <div id="mb_inipay" class="total_row">
                <div id="mb_inipay_sub">
                    <div id="mb_inipay_group" class="totals_group">
                        <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_refund_bankcode">환불 은행(코드)</label>
                        </h4>
                        <select id="_order_refund_bankcode" name="_order_refund_bankcode" class="mb_inipay_wide"
                                title="환불처리할 은행을 선택해주세요."<?php isDisabled(); ?>>
                            <option value="-1"<?php isSelected('-1', $vbank_refund_bankcode); ?>>===== [ 선택 ] =====
                            </option>
                            <option value="02"<?php isSelected('02', $vbank_refund_bankcode); ?>>산업(02)</option>
                            <option value="03"<?php isSelected('03', $vbank_refund_bankcode); ?>>기업(03)</option>
                            <option value="04"<?php isSelected('04', $vbank_refund_bankcode); ?>>국민(04)</option>
                            <option value="05"<?php isSelected('05', $vbank_refund_bankcode); ?>>외환(05)</option>
                            <option value="06"<?php isSelected('06', $vbank_refund_bankcode); ?>>국민(주택)(06)</option>
                            <option value="07"<?php isSelected('07', $vbank_refund_bankcode); ?>>수협(07)</option>
                            <option value="11"<?php isSelected('11', $vbank_refund_bankcode); ?>>농협(11)</option>
                            <option value="12"<?php isSelected('12', $vbank_refund_bankcode); ?>>농협(12)</option>
                            <option value="16"<?php isSelected('16', $vbank_refund_bankcode); ?>>농협(축협)(16)</option>
                            <option value="20"<?php isSelected('20', $vbank_refund_bankcode); ?>>우리(20)</option>
                            <option value="21"<?php isSelected('21', $vbank_refund_bankcode); ?>>조흥(21)</option>
                            <option value="23"<?php isSelected('23', $vbank_refund_bankcode); ?>>제일(23)</option>
                            <option value="25"<?php isSelected('25', $vbank_refund_bankcode); ?>>서울(25)</option>
                            <option value="26"<?php isSelected('26', $vbank_refund_bankcode); ?>>신한(26)</option>
                            <option value="27"<?php isSelected('27', $vbank_refund_bankcode); ?>>한미(27)</option>
                            <option value="31"<?php isSelected('31', $vbank_refund_bankcode); ?>>대구(31)</option>
                            <option value="32"<?php isSelected('32', $vbank_refund_bankcode); ?>>부산(32)</option>
                            <option value="34"<?php isSelected('34', $vbank_refund_bankcode); ?>>광주(34)</option>
                            <option value="35"<?php isSelected('35', $vbank_refund_bankcode); ?>>제주(35)</option>
                            <option value="37"<?php isSelected('37', $vbank_refund_bankcode); ?>>전북(37)</option>
                            <option value="38"<?php isSelected('38', $vbank_refund_bankcode); ?>>강원(38)</option>
                            <option value="39"<?php isSelected('39', $vbank_refund_bankcode); ?>>경남(39)</option>
                            <option value="41"<?php isSelected('41', $vbank_refund_bankcode); ?>>비씨(41)</option>
                            <option value="45"<?php isSelected('45', $vbank_refund_bankcode); ?>>새마을(45)</option>
                            <option value="48"<?php isSelected('48', $vbank_refund_bankcode); ?>>신협(48)</option>
                            <option value="50"<?php isSelected('50', $vbank_refund_bankcode); ?>>상호저축은행(50)</option>
                            <option value="53"<?php isSelected('53', $vbank_refund_bankcode); ?>>씨티(53)</option>
                            <option value="54"<?php isSelected('54', $vbank_refund_bankcode); ?>>홍콩상하이은행(54)</option>
                            <option value="55"<?php isSelected('55', $vbank_refund_bankcode); ?>>도이치(55)</option>
                            <option value="56"<?php isSelected('56', $vbank_refund_bankcode); ?>>ABN암로(56)</option>
                            <option value="70"<?php isSelected('70', $vbank_refund_bankcode); ?>>신안상호(70)</option>
                            <option value="71"<?php isSelected('71', $vbank_refund_bankcode); ?>>우체국(71)</option>
                            <option value="81"<?php isSelected('81', $vbank_refund_bankcode); ?>>하나(81)</option>
                            <option value="87"<?php isSelected('87', $vbank_refund_bankcode); ?>>신세계(87)</option>
                            <option value="88"<?php isSelected('88', $vbank_refund_bankcode); ?>>신한(88)</option>
                        </select>
                        </p>
                        <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_refund_vacc_num">환불 계좌번호</label>
                        </h4>
                        <input type="text" class="mb_inipay_wide" id="_order_refund_vacc_num"
                               name="_order_refund_vacc_num" placeholder="번호(숫자)만 입력하세요."
                               value="<?php echo $vbank_refund_vaccnum; ?>"
                               title="결제 플러그인 설정에서 배송회사 이름을 지정할수 있습니다."<?php isDisabled(); ?>></p>
                        <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_refund_vacc_name">환불 계좌주명</label>
                        </h4>
                        <input type="text" class="mb_inipay_wide" id="_order_refund_vacc_name"
                               name="_order_refund_vacc_name" placeholder="환불 계좌주명"
                               value="<?php echo $vbank_refund_vaccname; ?>" title="배송시 사용된 송장 번호를 입력해주세요."<?php isDisabled(); ?>></p>
                        <p class="wide"><h4 class="mb_inipay_h4"><label for="_order_refund_reason">취소 사유</label></h4>
                        <input type="text" class="mb_inipay_wide" id="_order_refund_reason" name="_order_refund_reason"
                               placeholder="취소 사유" value="<?php echo $vbank_refund_reason; ?>"
                               title="취소 사유를 입력해주세요."<?php isDisabled(); ?>></p>
                    </div>
                </div>
            </div>
        <?php
        }

        echo '<p class="order-info">';
		if( apply_filters( 'ifw_is_admin_refundable_' . $payment_method, false, $order ) ) {
            if($vbank_noti_received == 'yes') {
                if($vbank_refund_add == 'yes') {
                    echo '<input style="margin-right:10px" type="button" class="button button-primary tips" id="ifw-refund-request" name="vbank-refund-request-vacc-modify" value="' . __('정보수정','inicis_payment') . '" onClick="javascript:onVbankRefundModify();" title="이니시스 가상계좌 환불정보를 수정합니다." data-tip="환불정보 수정">';
                    echo '<input style="margin-right:10px" type="button" class="button button-primary tips" id="ifw-refund-request" name="vbank-refund-request" value="' . __('환불하기','inicis_payment') . '" onClick="javascript:onClickRefundRequest();" title="이니시스 가상계좌 환불 처리를 수행합니다." data-tip="환불하기">';
                } else {
                    echo '<input style="margin-right:10px" type="button" class="button button-primary tips" id="ifw-refund-request" name="vbank-refund-request-vacc-add" value="' . __('정보등록','inicis_payment') . '" onClick="javascript:onVbankRefundAdd();" title="가상계좌 무통장입금 환불처리를 위한 환불정보를 등록합니다." data-tip="환불정보 등록">';
                    echo '<input style="margin-right:10px" type="button" class="button button-primary tips" id="ifw-refund-request" name="vbank-refund-request" value="' . __('환불하기','inicis_payment') . '" title="이니시스 가상계좌 환불 처리를 수행할 수 없습니다. 먼저 환불정보를 등록하셔야 환불처리를 할 수 있습니다." data-tip="환불하기" disabled>';
                }
            } else {
                echo '<input style="margin-right:10px" type="button" class="button button-primary tips" id="ifw-refund-request" name="refund-request" value="' . __('취소하기','inicis_payment') . '" onClick="javascript:onClickCancelRequest();" title="이니시스 가상계좌 가상계좌 입금전 취소 처리를 수행합니다." data-tip="가상계좌 입금전 취소하기">';
            }
		}
		if ( !empty($tid) ) {
			echo '<input type="button" class="button button-primary tips" id="ifw-check-receipt" name="refund-request-check-receipt" value="' . __('영수증 확인','inicis_payment') . '" onClick="javascript:checkReceipt();" data-tip="영수증 확인">';
		}
        echo '</p>';
    }

        public static function save( $post_id, $post ) {
    }
}