<?php

/*
Plugin Name: INICIS for WooCommerce
Plugin URI: http://www.codemshop.com
Description: 엠샵에서 개발한 KG 이니시스의 워드프레스 우커머스 이용을 위한 결제 시스템 플러그인 입니다. KG INICIS Payment Gateway Plugin for Wordpress WooCommerce that developed by MShop.
Version: 4.0.3
Author: CODEM
Author URI: http://www.codemshop.com
License: Commercial License
*/


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

if ( ! class_exists( 'INICIS_Payment_Gateway' ) ) {

    class INICIS_Payment_Gateway {

        protected $slug;
                
        /**
         * @var string
         */
        public $version = '4.0.3';
    
        /**
         * @var string
         */
        public $plugin_url;
    
        /**
         * @var string
         */
        public $plugin_path;
    
        private $_body_classes = array();
        
        protected $update_checker;
        
        public function __construct() {
            define( 'INICIS_PAYMENT_VERSION', $this->version );
            
            $this->slug = 'inicis-for-woocommerce';

            $this->init_update();

            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                add_action('init', array($this, 'init'), 0);
                add_action('send_headers', array($this, 'inicis_mypage_cancel_order'), 0);
                add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
                add_action('plugins_loaded', array($this, 'plugins_loaded'));
                add_action('mshop_license_activation_data_' . $this->slug, array($this, 'mshop_license_activation_data'), 0);

                add_filter('woocommerce_available_payment_gateways', array($this, 'mobile_check_payment'), 10, 1);
            } else {
                add_action( 'admin_notices', array( $this, 'admin_notice_activate_woocommerce') );
            }

            register_activation_hook( __FILE__, array( $this, 'activation_process' ) );
            register_deactivation_hook( __FILE__, array( $this, 'deactivation_process' ) );
        }

        function admin_notice_activate_woocommerce() {
            echo '<div id="message" class="error">';
            echo __('<p>INICIS for WooCommerce 플러그인은 WooCommerce플러그인과 함께 동작합니다. WooCommerce 플러그인을 활성화해 주세요.</p>','inicis_payment');
            echo '</div>';
        }

        function activation_process() {
            global $inicis_payment;
            WP_Filesystem();
             if ( !file_exists( WP_CONTENT_DIR . '/inicis' ) ) {
                $old = umask(0); 
                mkdir( WP_CONTENT_DIR . '/inicis', 0755, true );
                umask($old);

                if ( file_exists( plugin_dir_path(__FILE__) . '/lib' ) ) {
                    rename( plugin_dir_path(__FILE__) . '/lib/', WP_CONTENT_DIR . '/inicis' );
                }
            }
            update_option('ifw_ver', $this->version);
        }
        
        function deactivation_process() {
            delete_option( 'woocommerce_inicis_card_settings' );
            delete_option( 'woocommerce_inicis_bank_settings' );
            delete_option( 'woocommerce_inicis_hpp_settings' );
            delete_option( 'woocommerce_inicis_vbank_settings' );
            delete_option( 'woocommerce_inicis_escorw_bank_settings' );
            delete_option( '_codem_inicis_activate_key' );
            delete_option( 'ifw_ver' );
        }

        function mshop_license_activation_data(){
            ?>
            <div class="msl_row">
                <div class="msl_cell">이니페이 상점 아이디</div>
                <div class="msl_cell"><input type="text" class='custom-field' data-name="이니페이 상점 아이디" name="msl_license_inipay_merchant_id"></div>
            </div>
            <div class="msl_row">
                <div class="msl_cell">에스크로 상점 아이디</div>
                <div class="msl_cell"><input type="text" class='custom-field' data-name="에스크로 상점 아이디"  name="msl_license_escrow_merchant_id"></div>
            </div>
        <?php
        }

        function mobile_check_payment($_available_gateways){
            if ( wp_is_mobile() ) {
                unset($_available_gateways['inicis_escrow_bank']);
            }
            return $_available_gateways;
        }

        function plugins_loaded() {

            $payment_method_list = array( 'card', 'bank', 'vbank', 'hpp', 'escrow_bank' );

            include_once( 'classes/class-wc-inicis-payment.php' );

            foreach( $payment_method_list as $type ) {
                include_once( 'classes/class-wc-inicis-payment-'.$type.'.php' );
            }
        }

        function woocommerce_payment_gateways( $methods ) {
            $payment_method_list = array( 'card', 'bank', 'vbank', 'hpp', 'escrow_bank' );

            include_once( 'classes/class-wc-inicis-payment.php' );

            foreach( $payment_method_list as $type ) {
                include_once( 'classes/class-wc-inicis-payment-'.$type.'.php' );
                $methods[] = 'WC_Gateway_Inicis_' . ucfirst( $type );
            }

            return $methods;
        }

        function init_update() {
            require 'admin/update/LicenseManager.php';

            $this->license_manager = new LicenseManager_20150718( $this->slug, __DIR__, __FILE__ );
        }
            
        public function plugin_url() {
            if ( $this->plugin_url ) 
                return $this->plugin_url;
            
            return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
        }
    
    
        public function plugin_path() {
            if ( $this->plugin_path ) 
                return $this->plugin_path;
    
            return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
        }
        
        function includes() {
            if ( is_admin() )
                $this->admin_includes();
    
            if ( defined('DOING_AJAX') )
                $this->ajax_includes();
    
            if ( ! is_admin() || defined('DOING_AJAX') )
                $this->frontend_includes();
            
        }
    
        public function admin_includes() {
            global $inicis_payment, $page;

            wp_register_style( 'ifw-style', $this->plugin_url() . '/assets/css/style.css' );
            wp_enqueue_style( 'ifw-style' );

            include_once('admin/class-ifw-admin-meta-boxes.php');
        }
        
        public function ajax_includes() {
            
        }
        
        public function frontend_includes() {
            
        }

        public function frontend_scripts() {
            if( is_page( 'checkout' ) || is_page( get_option('woocommerce_checkout_page_id') ) ) {
                if (is_ssl()) {
                    $url = 'https://plugin.inicis.com/pay61_secunissl_cross.js';
                } else {
                    $url = 'http://plugin.inicis.com/pay61_secuni_cross.js';
                }

                echo '<script language=javascript src="'.$url.'"></script>
                <script language=javascript>
                    StartSmartUpdate();
                </script>';

                if(wp_is_mobile()){
                    wp_register_script( 'ifw_payment-js', $this->plugin_url() . '/assets/js/ifw_payment.mobile.js' );
                }else{
                    wp_register_script( 'ifw_payment-js', $this->plugin_url() . '/assets/js/ifw_payment.js' );
                }

                wp_enqueue_script( 'ifw_payment-js' );
                wp_localize_script( 'ifw_payment-js', '_ifw_payment', array(
                    'ajax_loader_url' =>  $this->plugin_url() . '/assets/images/ajax_loader.gif',
                ) );

                wp_register_style( 'ifw-style', $this->plugin_url() . '/assets/css/style.css' );
                wp_enqueue_style( 'ifw-style' );
            }
        }
        
        public function init() {
            if ( ! is_admin() || defined('DOING_AJAX') ) {}
    
            $this->includes();
            $this->inicis_register_post_status();
            
            add_action( 'wp_head', array( $this, 'inicis_ajaxurl') );
            add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
            add_filter( 'wc_order_statuses', array( $this, 'add_order_statuses' ), 10, 1);
            add_filter( 'woocommerce_payment_gateways',  array( $this, 'woocommerce_payment_gateways' ) );
            add_filter( 'woocommerce_pay_order_button_html', array($this, 'woocommerce_pay_order_button_html' ) );
            add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'woocommerce_my_account_my_orders_actions' ), 1, 2 );
            //add_action( 'woocommerce_view_order', array( 'WC_Gateway_Inicis_Escrow_bank', 'inicis_escrow_mypage_cancell_request' ), 4 );
            add_action( 'woocommerce_view_order', array( 'WC_Gateway_Inicis_Escrow_bank', 'inicis_escrow_mypage_accept_request' ), 5 );
            add_action( 'woocommerce_view_order', array( 'WC_Gateway_Inicis_Escrow_bank', 'inicis_escrow_mypage_refund_request' ), 6 );
            add_action( 'woocommerce_view_order', array( 'WC_Gateway_Inicis_Vbank', 'inicis_vbank_view_order' ), 5 );
            add_filter( 'the_title', array( $this,'inicis_order_received_title'), 10, 2 );
        }

        public function inicis_order_received_title( $title, $id ) {
            if ( is_order_received_page() && get_the_ID() === $id ) {
                global $wp;

                $order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $wp->query_vars['order-received'] ) );
                $order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] ) );

                if( !empty($order_id) ) {
                    $order = new WC_Order($order_id);
                    if($order->get_status() == 'failed') {
                        $title = __('결제 실패로, 결제를 다시한번 진행 해 주시기 바랍니다.', 'inicis_payment');
                    } else {
                        $title = __('정상적인 결제완료로 주문이 접수되었습니다.', 'inicis_payment');
                    }
                } else {
                    $title = __('정상적인 결제완료로 주문이 접수되었습니다.', 'inicis_payment');
                }
            }
            return $title;
        }
        
        public function woocommerce_my_account_my_orders_actions($actions, $order){
            global $woocommerce;
            $woocommerce->payment_gateways();
            $payment_method = get_post_meta($order->id, '_payment_method', true);
            return apply_filters('woocommerce_my_account_my_orders_actions_' . $payment_method, $actions, $order);
        }
        
        function inicis_register_post_status() {
            register_post_status( 'wc-shipped', array(
                'label'                     => _x( '배송완료', 'Order status', 'inicis_payment' ),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( '배송완료 <span class="count">(%s)</span>', '배송완료 <span class="count">(%s)</span>', 'inicis_payment' )
            ) );            
            register_post_status( 'wc-cancel-request', array(
                'label'                     => _x( '주문취소요청', 'Order status', 'inicis_payment' ),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( '주문취소요청 <span class="count">(%s)</span>', '주문취소요청 <span class="count">(%s)</span>', 'inicis_payment' )
            ) );            
            
        }

        function add_order_statuses($order_statuses) {
            $order_statuses = array_merge($order_statuses, array(    
                'wc-shipped'            => _x( '배송완료', 'Order status', 'inicis_payment' ),
                'wc-cancel-request'     => _x( '주문취소요청', 'Order status', 'inicis_payment' ),
            ));
            return $order_statuses;
        }
        
        function inicis_mypage_cancel_order(){
            global $woocommerce;
            if ( isset( $_GET['inicis-cancel-order'] ) && isset( $_GET['order'] ) && isset( $_GET['order_id'] ) ) {
                $woocommerce->payment_gateways();
                $payment_method = get_post_meta( $_GET['order_id'], '_payment_method', true );
                do_action( 'inicis_mypage_cancel_order_' . $payment_method, $_GET['order_id'] );
                wp_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ), 301);
                die();
            }
        }

        function woocommerce_pay_order_button_html() {
            $orderid = wc_get_order_id_by_order_key($_REQUEST['key']);
            
            if(wp_is_mobile()){
                wp_register_script( 'ifw-pay-for-order', $this->plugin_url() . '/assets/js/ifw_pay_for_order.mobile.js' );
            }else{
                wp_register_script( 'ifw-pay-for-order', $this->plugin_url() . '/assets/js/ifw_pay_for_order.js' );
            }

            wp_enqueue_script( 'ifw-pay-for-order' );
            wp_localize_script( 'ifw-pay-for-order', '_ifw_pay_for_order', array(
                'ajax_loader_url' =>  $this->plugin_url() . '/assets/images/ajax_loader.gif',
                'order_id' => $orderid,
                'order_key' => $_REQUEST['key']
            ) );

            $pay_order_button_text = apply_filters( 'woocommerce_pay_order_button_text', __( 'Pay for order', 'woocommerce' ) );
            return '<input type="button" class="button alt" id="place_order" value="' . esc_attr( $pay_order_button_text ) . '" data-value="' . esc_attr( $pay_order_button_text ) . '" />';
        }
        
        function inicis_ajaxurl() {
            ?>
            <script type="text/javascript">
            <?php
                $use_ssl = get_option('woocommerce_force_ssl_checkout');
                
                if(function_exists('icl_object_id')) {
                    if ($use_ssl == 'yes') {
                        $html_ajax_url = admin_url('admin-ajax.php?lang=' . ICL_LANGUAGE_CODE, 'https');
                    } else {
                        $html_ajax_url = admin_url('admin-ajax.php?lang=' . ICL_LANGUAGE_CODE, 'http');
                    }
                } else {
                    if ($use_ssl == 'yes') {
                        $html_ajax_url = admin_url('admin-ajax.php', 'https');
                    } else {
                        $html_ajax_url = admin_url('admin-ajax.php', 'http');
                    }
                }
            ?>

            var ifw_ajaxurl = '<?php echo $html_ajax_url; ?>';
            </script>
            <?php
        }
        
        public function add_body_class( $class ) {
            $this->_body_classes[] = sanitize_html_class( strtolower($class) );
        }
        
        public function output_body_class( $classes ) {
            return $classes;
        }

        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'inicis_payment', false, dirname( plugin_basename(__FILE__) ) . "/languages/" );
        }
    }

    $inicis_payment = new INICIS_Payment_Gateway();
}
