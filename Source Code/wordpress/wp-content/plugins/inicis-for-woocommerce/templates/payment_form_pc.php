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

if (defined('ICL_LANGUAGE_CODE')) {
	$lang_code = ICL_LANGUAGE_CODE;

	if ($use_ssl == 'yes') {
		?>
			<form name=ini method=post action="<?php echo untrailingslashit( WC()->api_request_url(get_class($this) . '?type=pc&lang=' . $lang_code, true)); ?>" onSubmit="return pay(this)">
		<?php
	} else {
		?>
			<form name=ini method=post action="<?php echo untrailingslashit( WC()->api_request_url(get_class($this) . '?type=pc&lang=' . $lang_code, false)); ?>" onSubmit="return pay(this)">
		<?php
	}
} else {
	if ($use_ssl == 'yes') {
		?>
			<form name=ini method=post action="<?php echo untrailingslashit( WC()->api_request_url(get_class($this) . '?type=pc', true)); ?>" onSubmit="return pay(this)">
		<?php
	} else {
		?>
			<form name=ini method=post action="<?php echo untrailingslashit( WC()->api_request_url(get_class($this) . '?type=pc', false)); ?>" onSubmit="return pay(this)">
		<?php
	}
} ?>
	
    <input type="hidden" name="goodname" size=20 value="<?php echo esc_attr($productinfo); ?>" />
    <input type="hidden" name="oid" size=40 value="<?php echo $txnid; ?>" />
    <input type="hidden" name="buyername" size=20 value="<?php echo $order->billing_last_name . $order->billing_first_name; ?>" />
    <input type="hidden" name="buyeremail" size=20 value="<?php echo $order->billing_email; ?>" />
    <input type="hidden" name="buyertel" size=20 value="<?php echo $order->billing_phone; ?>" />
    <input type="hidden" name="gopaymethod" size=20 value="<?php echo $this->settings['gopaymethod']; ?>" />
    <input type="hidden" name="currency" size=20 value="WON" />
    <input type="hidden" name="acceptmethod" size=20 value="<?php echo $acceptmethod; ?>" />
    <input type="hidden" name="ini_logoimage_url" value="<?php echo $this->settings['logo_upload']; ?>" />
    <input type=hidden name=quotainterest value="">
    <input type=hidden name=paymethod value="">
    <input type=hidden name=cardcode value="">
    <input type=hidden name=cardquota value="">
    <input type=hidden name=rbankcode value="">
    <input type=hidden name=reqsign value="DONE">
    <input type=hidden name=encrypted value="">
    <input type=hidden name=sessionkey value="">
    <input type=hidden name=uid value=""> 
    <input type=hidden name=sid value="">
    <input type=hidden name=version value=4000>
    <input type=hidden name=clickcontrol value="">
    <input type=hidden name=hash value="<?php echo $hash; ?>">
    <input type=hidden name=txnid value="<?php echo $txnid; ?>">
    <input type=hidden name=Amount value="<?php echo $this->inicis_get_order_total($order); ?>">
    <input type=submit name="결제">
	<input type=hidden name=ini_encfield value="<?php echo $inipay->GetResult("encfield"); ?>">
    <input type=hidden name=ini_certid value="<?php echo $inipay->GetResult("certid"); ?>">
</form>
    