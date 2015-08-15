<?php

if ( !class_exists('LicenseManager_20150718') ) {

	Class LicenseManager_20150718
	{
        private $rev = '20150718';

		private $slug;

		private $debug = false;

		private $license_server_url;
		private $update_server_url;

		public function __construct($slug, $dir, $file, $debug = false)
		{
			$this->slug = $slug;
			$this->debug = $debug;

			$this->license_server_url = 'http://lic.codemshop.com/manager_' . $this->rev;
			$this->update_server_url  = 'http://lic.codemshop.com/update';

			require 'plugin-updates/plugin-update-checker.php';

			$license_info = get_option('msl_license_' . $this->slug, null);
			if ($license_info) {
				$license_info = json_decode($license_info);
			}

			$this->update_checker = PucFactory::buildUpdateChecker(
				$this->update_server_url . '?action=get_metadata&slug=' . $this->slug . '&license_key=' . ($license_info ? $license_info->license_key : '') . '&activation_key=' . ($license_info ? $license_info->activation_key : '') . '&domain=' . ($license_info ? $license_info->site_url : ''),
				$file,
				$this->slug
			);

			add_action("in_plugin_update_message-" . basename($dir) . '/' . basename($file), array($this , "in_plugin_update_message"), 10, 2);
			add_action('wp_ajax_msl_activation_' . $this->slug, array(&$this, 'msl_activation'));
			add_action('wp_ajax_msl_verify_' . $this->slug, array(&$this, 'msl_verify'));
			add_action('wp_ajax_msl_reset_' . $this->slug, array(&$this, 'msl_reset'));

			add_action( 'admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
		}

		public function admin_enqueue_scripts(){
			wp_enqueue_style( 'mshop-license-checker', plugin_dir_url( __FILE__ ) . 'assets/css/license-manager.css' );

		}

		public function in_plugin_update_message($plugin_data, $r)
		{
			echo '<br>' . $plugin_data['upgrade_notice'] . '</br>';
		}

		public function msl_activation()
		{
			$license_key = $_REQUEST['msl_license_key'];
			$site_url = site_url();
			$site_url = preg_replace('#^https?://#', '', $site_url);

			$response = wp_remote_post( $this->license_server_url, array(
					'method' => 'POST',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => array( 'action' => 'activation', 'slug' => $this->slug, 'license_key' => $license_key, 'domain' => $site_url, 'data' => json_encode($_REQUEST['msl_data'] ) ),
					'cookies' => array()
				)
			);

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				$result = $error_message;
			} else {
				$result = json_decode($response['body']);
			}

			if (!empty( $result) && $result->result >= 0) {
				$license_info = array(
					'site_url' => $site_url,
					'slug' => $this->slug,
					'license_key' => $license_key,
					'activation_key' => $result->activation_key,
					'expire_date' => $result->expire_date,
					'status' => $result->result,
					'data' => json_encode( $_REQUEST['msl_data'] )
				);

				update_option('msl_license_' . $this->slug, json_encode($license_info));
				wp_send_json_success(array('message' => $result->notice));
			} else {
				wp_send_json_error(array('message' => $result->notice));
			}
		}

		public function msl_reset()
		{
			delete_option('msl_license_' . $this->slug);

			$this->msl_verify();
		}

		public function msl_verify()
		{
			ob_start();

			$license_info = get_option('msl_license_' . $this->slug, null);
			$site_url = site_url();
			$site_url = preg_replace('#^https?://#', '', $site_url);

			if ($license_info) {
				$license_info = json_decode($license_info);

				$response = wp_remote_post( $this->license_server_url, array(
						'method' => 'POST',
						'timeout' => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => array(),
						'body' => array( 'action' => 'verify', 'slug' => $license_info->slug, 'license_key' => $license_info->license_key, 'domain' => $site_url, 'activation_key' =>  $license_info->activation_key ),
						'cookies' => array()
					)
				);

				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					$result = $error_message;
				} else {
					$result = json_decode($response['body']);
				}

				if ($result->result != 1) {
					$message = $result->notice;
				}

			}

			if ($license_info && $license_info->status >= 0) {
				?>
				<div class="msl_table_wrapper">
					<div class="msl_table">
						<div class="msl_row">
							<div class="msl_cell">상태</div>
							<div class="msl_cell">
								<?php
								if (empty($message)) {
									?>
									<script>
										jQuery(function ($) {
											$('div.mshop-setting-page-wrapper').css('display', 'block');
										});
									</script>
									<span>정상</span><br>
								<?php
								}else{
									?>
									<script>
										jQuery(function ($) {
											$('#license_activation_wrapper_<?php echo $this->slug; ?> .ui-accordion-header').first().contents().eq(1)[0].textContent = '라이센스정보 확인이 필요합니다.';
											$('#license_activation_wrapper_<?php echo $this->slug; ?> .ui-accordion-header').css('color', 'red');
										});
									</script>
									<span style="color:red;"><b><?php echo $message; ?></b></span>
									<input type="button" class='button-primary' name="msl_reset" id="msl_reset" value="재입력">
								<?php
								}
								?>
							</div>
						</div>
						<div class="msl_row">
							<div class="msl_cell">상품코드</div>
							<div class="msl_cell"><?php echo $license_info->slug; ?></div>
						</div>
						<div class="msl_row">
							<div class="msl_cell">도메인</div>
							<div class="msl_cell"><?php echo $license_info->site_url; ?></div>
						</div>
						<div class="msl_row">
							<div class="msl_cell">라이센스코드</div>
							<div class="msl_cell"><?php echo $license_info->license_key; ?></div>
						</div>
						<div class="msl_row">
							<div class="msl_cell">활성화코드</div>
							<div class="msl_cell"><?php echo $license_info->activation_key; ?></div>
						</div>
						<div class="msl_row">
							<div class="msl_cell">유효기간</div>
							<div class="msl_cell"><?php echo $license_info->expire_date; ?></div>
						</div>
						<?php
						if( !empty( $license_info->data ) ){
							$additional_datas =  json_decode( $license_info->data );
							foreach( $additional_datas as $data ){
								?>
								<div class="msl_row">
									<div class="msl_cell"><?=$data->title;?></div>
									<div class="msl_cell"><?=$data->value?></div>
								</div>
							<?php
							}
						}
						?>
					</div>
				</div>
			<?php

			} else {
				?>
				<script>
					jQuery(function ($) {
						$('#license_activation_wrapper_<?php echo $this->slug; ?> .ui-accordion-header').first().contents().eq(1)[0].textContent = '라이센스정보를 등록하세요.';
						$('#license_activation_wrapper_<?php echo $this->slug; ?> .ui-accordion-header').css('color', 'red');
					});
				</script>
				<div class="msl_table_wrapper">
					<div class="msl_table">
						<div class="msl_row">
							<div class="msl_cell">상품코드</div>
							<div class="msl_cell"><?php echo $this->slug; ?></div>
						</div>
						<div class="msl_row">
							<div class="msl_cell">도메인</div>
							<div class="msl_cell"><?php echo $site_url; ?></div>
						</div>
						<?php do_action( 'mshop_license_activation_data_' . $this->slug ); ?>
						<div class="msl_row">
							<div class="msl_cell">라이센스코드</div>
							<div class="msl_cell">
								<input type="text" size="40" name="msl_license_key" id="msl_license_key"/>
								<input type="button" class='button-primary' name="activation" id="msl_activation"
									   value="활성화">
								<input type="hidden" name="msl_slug" id="msl_slug" value="<?php echo $this->slug; ?>">
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			$data = ob_get_clean();

			wp_send_json_success( array( 'html' => $data ) );
		}

		public function load_activation_form()
		{
			wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script( 'jquery-blockui', plugin_dir_url(__FILE__) . 'assets/js/blockui/jquery.blockUI.js' );

			include( 'templates/html-license-activation-form.php' );
		}
	}

}