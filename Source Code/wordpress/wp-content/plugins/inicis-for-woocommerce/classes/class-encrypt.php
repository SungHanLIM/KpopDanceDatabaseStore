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
if ( ! defined( 'ABSPATH' ) ) exit;

//PHP 5.3 User hex2bin function support
if( !function_exists( 'hex2bin' ) ) {
    define('HEX2BIN_WS', " \t\n\r");
    function hex2bin($hex_string) {
        $pos = 0;
        $result = '';
        while ($pos < strlen($hex_string)) {
            if (strpos(HEX2BIN_WS, $hex_string{$pos}) !== FALSE) {
                $pos++;
            } else {
                $code = hexdec(substr($hex_string, $pos, 2));
                $pos = $pos + 2;
                $result .= chr($code);
            }
        }
        return $result;
    }
}

if( !function_exists( 'aes128_cbc_encrypt' ) ) {
    function aes128_cbc_encrypt($key, $data, $iv) {
      if(16 !== strlen($key)) $key = hash('MD5', $key, true);
      if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
      $padding = 16 - (strlen($data) % 16);
      $data .= str_repeat(chr($padding), $padding);
      return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv));
    }
}

if( !function_exists( 'aes256_cbc_encrypt' ) ) {
    function aes256_cbc_encrypt($key, $data, $iv) {
      if(32 !== strlen($key)) $key = hash('SHA256', $key, true);
      if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
      $padding = 16 - (strlen($data) % 16);
      $data .= str_repeat(chr($padding), $padding);
      return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv));
    }
} 
 
if( !function_exists( 'aes128_cbc_decrypt' ) ) {
    function aes128_cbc_decrypt($key, $data, $iv) {
      if(16 !== strlen($key)) $key = hash('MD5', $key, true);
      if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
      $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, hex2bin($data), MCRYPT_MODE_CBC, $iv);
      $padding = ord($data[strlen($data) - 1]);
      return substr($data, 0, -$padding);
    }
}

if( !function_exists( 'aes256_cbc_decrypt' ) ) {
    function aes256_cbc_decrypt($key, $data, $iv) {
      if(32 !== strlen($key)) $key = hash('SHA256', $key, true);
      if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
      $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, hex2bin($data), MCRYPT_MODE_CBC, $iv);
      $padding = ord($data[strlen($data) - 1]);
      return substr($data, 0, -$padding);
    }
}