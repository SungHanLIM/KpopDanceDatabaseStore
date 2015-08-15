=== INICIS for WooCommerce ===
Contributors: CODEM(c), CodeMShop, MShop, Inicis
Donate link: http://www.codemshop.com 
Tags: WooCommerce, eCommerce, Inicis, Payment, Gateway, PG, KG, KGINICIS, wordpress, MShop, CodeMStory, CodeMShop, CODEM(c), 이니시스, 우커머스, 결제, 코드엠, 엠샵
Requires at least: 4.2.3
Tested up to: 4.2.3
Stable tag: 2.3.13

엠샵에서 개발한 KG 이니시스의 워드프레스 우커머스 이용을 위한 결제 시스템 플러그인 입니다.

== License ==

 1. 코드엠에서 개발한 워드프레스 우커머스용 결제 플러그인의 저작권은 ㈜코드엠에게 있습니다.
 2. 당사의 플러그인의 설치, 인증에 따른 절차는 플러그인 라이센스 규정에 동의하는 것으로 간주합니다.
 3. 결제 플러그인의 사용권은 쇼핑몰 사이트의 결제 서비스 사용에 국한되며, 그 외의 상업적 사용을 금지합니다.
 4. 결제 플러그인의 소스 코드를 복제 또는 수정 및 재배포를 금지합니다. 이를 위반 시 민형사상의 책임을 질 수 있습니다.
 5. 플러그인 사용에 있어 워드프레스, 테마, 플러그인과의 호환 및 버전 관리의 책임은 사이트 당사자에게 있습니다.
 6. 위 라이센스는 개발사의 사정에 의해 임의로 변경될 수 있으며, 변경된 내용은 해당 플러그인 홈페이지를 통해 공개합니다.

== Description ==

워드프레스 쇼핑몰 우커머스에 사용이 가능한 결제 플러그인 입니다.
"INICIS ? for WooCommerce" plugin is available for Wordpress's 'WooCommerce' Plugin .

= 결제 지원 범위(Support Features) =
* PC Desktop : 신용카드(Credit Card), 은행 계좌이체(Direct Bank Transfer), 에스크로 실시간 계좌이체(Escrow Direct Bank Transfer)
* Mobile(Smart Phone) : 신용카드(Credit Card), 은행 계좌이체(Direct Bank Transfer)
* 카드 포인트 사용(Option To Use Credit Card Point)
* PG 플러그인 스킨 색상 지정(Setting For Changing Skin Color of Payment Gateway Program)
* 할부 개월수 지정(Option To Select Installments)
* 무이자 할부 개월수 지정(Option To Set Interest-free Instalments)

= 간편한 결제 설정(Easy Payment Setting) =
* 개발자를 통하지 않고, PG 계약 이후에 제공받은 KEY 파일을 설정하고 몇가지 간단한 설정을 지정하시면 곧바로 사용이 가능합니다.(Payment Gateway service is available for you with simple setting in wordpress admin panel without going through any development overhead) 

= 온라인 결제를 위한 KG 이니시스 서비스 신청 안내(KG Inicis Services Application Guide) = 
* 사업자 확인 및 카드사 심사를 위해 이니시스 결제 서비스 신청 후 정상적인 서비스를 이용하실 수 있습니다.
 (To use this plugin service properly you must gone through business License Number Check by signing up with INICIS Service and also Credit Card Companies Settlement Examination. )
  
* PG 서비스 상세 이용 설명(For Detailed description of the PG service please go through) : http://www.inicis.com/, http://www.inicis.com/eng/

* PG 서비스 신청 지원(PG Service Application Support) : http://www.codemshop.com

= 설치 영상 =
[youtube http://www.youtube.com/watch?v=u22OAROS08M]

== Installation ==

= 사용 가능 환경(Requirements) =

* 워드프레스 4.2 또는 최신 버전 (Wordpress 4.2 or later)
* 우커머스 2.3 또는 최신 버전 (WooCommerce 2.3 or later)
* PHP 5.3.0 또는 최신 버전 (PHP 5.3.0 or later)
* PHP 확장(Extension): OpenSSL, LibXML, mcrypt, socket 설치필요 (--with-openssl, --with-mcrypt, --enable-sockets)
* MySQL 5.0 또는 최신 버전 (MySQL 5.0 or later)
* 방화벽 설정 확인 (Check Firewall Setting Manual Provided By KG INICIS ) 

= 수동 설치 방법(Manually Install) =

수동으로 설치할 경우에는 플러그인을 다운로드 받아서 웹서버에 원하는 FTP 프로그램을 이용해서 플러그인을 업로드하여 설치하시면 됩니다.
(To Install plugin manually to the Web Server. First download it, then upload it using FTP program and then install the plugin.)

* 플러그인을 컴퓨터에 다운로드 받아 압축을 풉니다.(Plugin download and unzip on your computer.)
* FTP 프로그램을 이용하거나, 호스팅의 관리페이지 또는 플러그인 업로드 페이지를 이용해서 워드프레스가 설치된 경로의 하위에 /wp-content/plugins/ 디렉토리안에 압축을 푼 파일을 업로드 합니다.
(Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.)
* 워드프레스 관리자 페이지 플러그인 메뉴에서 해당 플러그인을 활성화 시킵니다.(Activate the plugin from the Plugins Panel within the WordPress admin.) 

== Frequently Asked Questions ==

* TX로 시작하는 에러가 발생합니다.
이니시스 사이트에 보시면 에러 코드를 이용하여 에러 내용을 조회할 수 있습니다. 해당 내용을 확인해 보시기 바랍니다. 
(Showing TX error.
 Check the TX error code. Please Find This Error Code On INICIS Website http://www.inicis.com/err_search)

* "PHP 확장 설치 필요"라는 메시지를 보게 되었습니다. 어떻게 해야 하나요?
PHP 확장은 결제 서비스를 사용하는데 필요한 필수 요소입니다. 이 부분은 서버 관리자가 환경을 구성해주어야만 되는 문제로, 서버 관리자에게 문의하여 주셔야 합니다.
(Its showing "PHP extension installation required" message. What should I do?
 This means PHP extension is required to use payment services.So Please contact to your hosting service provider and ask him to configure the environment.)

* 잘 이해가 되지 않습니다. 도움을 요청할수 있을까요?
잘 모르는 부분이 있으시다면, http://www.wordpressshop.co.kr 사이트에 있는 포럼 게시판에 글을 남겨주시기 바랍니다. 
(For any other queries. you may contact to us by feel free to write to us on  http://www.wordpressshop.co.kr website under Forum Menu. )

== Screenshots ==

1. 환경설정 화면(1) / Setting Screen(1)
2. 환경설정 화면(2) / Setting Screen(2)
3. 지불 페이지 / Client Payment Page
4. 실제 결제 플러그인 동작 화면 / Payment Plugin Working Screen
5. 모바일/스마트폰 결제 동작 화면 / Mobile Payment Working Screen

== Changelog ==

= 4.0.3 - 2015/08/04 =
* 주문 처리 프로세스 개선
* 우커머스 비활성화 예외 처리 추가

= 4.0.2 - 2015/07/06 =
* 인증 처리 수정

= 4.0.1 - 2015/07/02 =
* 마이너 버그 수정

= 4.0.0 - 2015/07/02 =
* '휴대폰 소액결제' 방법 추가
* wrapper tag 옵션 제거

= 3.1.7 - 2015/06/30 =
* 언어팩 템플릿 .pot 추가
* 언어팩 호출 시점 변경

= 3.1.6 - 2015/06/30 =
* Pending 상태 주문 내 계정 취소 처리 수정

= 3.1.5 - 2015/06/23 =
* 이니시스 결제 스크립트 로딩 처리 변경

= 3.1.4 - 2015/06/16 =
* 결제시 상품명 누락 현상 개선(한글 인코딩 함수 변경)
* SSL 처리 개선

= 3.1.3 - 2015/06/15 =
* 에스크로 계좌이체 모바일 결제 노출 제한 처리 추가
* 모바일 결제 도중 취소시 취소 안내 처리 개선
* 결제페이지 고유주소(Permalink) 설정 개선 처리

= 3.1.2 - 2015/06/04 =
* PHP 5.3 사용자를 위한 암호화 호환코드 추가

= 3.1.1 - 2015/05/28 =
* 에스크로 계좌이체 비회원 처리 버그 수정
* 스크립트 파일 로딩 처리 개선

= 3.1.0 - 2015/05/27 =
* 이니시스 무통장입금(가상계좌) - 결제 방법 추가
* 이니시스 무통장입금(가상계좌) - 실시간 입금 통보 기능 추가
* 워드프레스 4.2 호환 - 내 계정에서 주문 취소시 redirect 기능 수정
* 모바일 결제 시도 중 뒤로가기 버튼을 눌러 결제를 취소한 경우 결제(Checkout)페이지로 이동 처리 추가
* 결제시 오류 발생시 주문상태 failed(실패) 처리 수정
* 결제시 오류 발생하는 경우 에러 메시지가 잘려서 주문노트에 추가되는 현상 수정
* 결제 설정 중 로고 설정 기본값 변경
* 계좌이체 결제수단 - 현금영수증 발행 차단 옵션 추가

= 3.0.9 - 2015/02/14 =
* 결제창 태그 지정 기능 추가

= 3.0.8 - 2015/02/13 =
* 우커머스 2.3 지원 처리
  Support WooCommerce 2.3

= 3.0.7 - 2015/01/27 =
* 모바일 ISP 결제 처리 개선

= 3.0.6 - 2014/12/22 =
* 암호화 코드 개선

= 3.0.5 - 2014/12/19 =
* 결제 플러그인 스크립트 호환성 개선

= 3.0.4 - 2014/12/09 =
* IE8 스크립트 동작 개선
* 모바일 결제 주문 처리 개선

= 3.0.3 - 2014/11/27 =
* 모바일 결제시 팝업창 차단으로 인해 발생하는 문제점 개선

= 3.0.2 - 2014/11/26 =
* 스크립트 수정
* 워드프레스 4.0.1 결제 테스트 완료

= 3.0.1 - 2014/11/12 =
* 에스크로 처리 수정

= 3.0.0 - 2014/11/11 =
* 에스크로 실시간 계좌 이체 결제 방법 추가
* 비회원 에스크로 결제 처리 추가  

= 2.0.10 - 2014/10/23 =
* 라이센스 수정
  License Fix.

= 2.0.9 - 2014/09/17 =
* 환불 처리 스크립트 수정
  Order Refund Script Fix.

= 2.0.8 - 2014/09/16 =
* 상품명 특수 기호 필터링 처리 추가
  Goods Name Special Character Filter function added.

= 2.0.7 - 2014/09/16 =
* 구버전 호환코드 추가
  Old version Support code added.

= 2.0.6 - 2014/09/15 =
* Wordpress 4.0 & WooCommerce 2.2 지원
  Support Wordpress 4.0 and WooCommerce 2.2 
* WPML 지원(Order Received) 처리 추가
  Support WPML for Order Received process. 
* 환불 처리 스크립트 수정
  Order Refund Script Fix.

= 2.0.5 - 2014/08/28 =
* '사용자/관리자 주문취소 가능 상태' 옵션 수정
  Option 'Customer & Shop Manager can Order Cancel Status' Fix.

= 2.0.4 - 2014/08/25 =
* SSL 지원 처리 수정
  HTTPS(SSL) Support Fix.

= 2.0.3 - 2014/08/05 =
* 결제 완료 페이지 처리 변경
  Change Order-Received Process.
* 기타 버그 수정
  Etc, Bug Fix.

= 2.0.2 - 2014/08/01 =
* 라이브러리 폴더 변경 처리 수정
  Library Folder Path Change Process Fix.
* 기타 버그 수정
  Etc, Bug Fix.
    

= 2.0.1 - 2014/07/31 =
* 언어팩 수정 및 기타 항목 수정
  Language Pack Translate add and Other things Fix. 

= 2.0.0 - 2014/07/30 =

* 소스 구조 변경
  Source Structure Change.
* 모바일 결제 처리 변경
  Mobile Payment Process Fix.
* 결제 페이지에서 결제 가능
  Possible Payment Process to Checkout page.
* 관리자 화면 구성 변경
  Change Payment Option Manage page.
* 상점 키파일 업로드 기능 추가
  Possible to Shopping Mall Keyfile Upload at Payment Option Manage page.
* 대기시간 초과 상품 처리 추가
  Add Timeout Waiting Order Process.    


= 1.0.5 - 2014/07/10 =

* 관리자 주문 환불 처리 수정
  Admin Order Refund Process Fix.


= 1.0.4 - 2014/07/07 =

* 모바일 결제시 ISP 처리 수정
  Mobile Checkout Process Fix about ISP paymethod.


= 1.0.3 - 2014/04/05 =

* 모바일 결제시 ISP 결제 처리 코드 추가
  Mobile Checkout Process add code about ISP paymethod.
* 결제 플러그인 관련 공지사항 추가
  Payment Gateway Notice Funtion add.
* 결제 플러그인 파일명과 폴더명 변경
  Plugin Filename and Folder Name Change.


= 1.0.2 - 2014/03/12 =

* IE8에서 스크립트 오류 수정
  IE8 Javascript Bug Fix.


= 1.0.1 - 2014/02/17 =

* 우커머스 2.1.0 업데이트 호환 대응 처리 (WooCommerce 2.1.2 + Wordpress 3.8.1 에서 테스트 완료)
  Support WooCommerce 2.1.x Now! (Tested WooCommerce 2.1.2)
* 신규 옵션 추가 - 사용자/관리자 환불가능 주문상태, 결제완료/취소시 변경될 주문상태 옵션 추가 (4개 항목 추가)
  New Option Added. User/Admin Possible Refund Status Option and After Payment Complete or Cancel, change Order Status Option. 
* 사용자 내 계정(My-Account) 페이지에서의 환불 요청 처리 기능 추가
  Order Cancel Requset Function added in 'My Account Order Detail View' page. 
* 관리자 페이지 주문 편집시 우측에 '이니시스 결제 주문 환불 처리' 메타 박스 추가 및 환불 처리 기능 추가
  Order Cancel Requset Function Metabox added in 'Order Edit' page.
* 플러그인 셋팅 링크 제거
  Remove 'Settings' Link at Plugins List.


= 1.0.0 - 2014/01/10 =

* 최초 버전 릴리즈. (First version Release)

== Upgrade Notice ==

= 2.0.0 =
주의사항! 1.0.x 버전 사용자 분들은 업그레이드시에 키파일과 로그파일들이 삭제되오니 필히 백업 후에 업데이트를 진행하시기 바랍니다. 
Warning! 1.0.x Version Users, please backup keyfile and log files before update. because, if you keep going update, it remove inside keyfile and log files.

= 2.0.6 =
주의사항! 워드프레스 4.0과 우커머스 2.2 버전 사용자가 아닌 경우 업데이트를 진행하지 마시기 바랍니다. 
Warning! If you are using wordpress 4.0 below and woocommerce 2.2 below, please do not update. 