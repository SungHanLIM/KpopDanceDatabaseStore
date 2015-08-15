<script>
	jQuery(function ($) {
		$.blockUI.defaults.overlayCSS.opacity = 0.3;

		$("#license_activation_wrapper_<?php echo $this->slug; ?>").accordion({
			heightStyle: "content",
			active: false,
			collapsible: true
		});

		function msl_request_activation_<?php echo str_replace("-", "_", $this->slug); ?>() {
			$('#license_activation_wrapper_<?php echo $this->slug; ?>').block({message: ''});

			var data = {};

			$('#license_activation_wrapper_<?php echo $this->slug; ?> input.custom-field').each( function() {
				data[$(this).attr('name')] = { 'title' : $(this).data('name'), 'value' : $(this).val() };
			});

			$.ajax({
				type: "post",
				dataType: "json",
				url: ajaxurl,
				data: {
					action: "msl_activation_<?php echo $this->slug; ?>",
					msl_license_key: $('#license_activation_wrapper_<?php echo $this->slug; ?> #msl_license_key').val(),
					msl_data : data
				},
				success: function (response) {
					if (response.success) {
						alert('라이센스가 정상적으로 활성화되었습니다.')
						window.location.reload();
					} else {
						alert(response.data.message);
					}
					$('#license_activation_wrapper_<?php echo $this->slug; ?>').unblock();
				},
				error: function () {
					$('#license_activation_wrapper_<?php echo $this->slug; ?>').unblock();
				}
			});
		}

		function msl_request_reset_<?php echo str_replace("-", "_", $this->slug); ?>() {
			$('#license_activation_wrapper_<?php echo $this->slug; ?>').block({message: ''});
			$.ajax({
				type: "post",
				dataType: "json",
				url: ajaxurl,
				data: {
					action: "msl_reset_<?php echo $this->slug; ?>"
				},
				success: function (response) {
					if (response.success) {
						$('#license_activation_wrapper_<?php echo $this->slug; ?> .license_activation_form').html(response.data.html);
						$('#license_activation_wrapper_<?php echo $this->slug; ?> #msl_activation').on('click', msl_request_activation_<?php echo str_replace("-", "_", $this->slug); ?>);
						$('#license_activation_wrapper_<?php echo $this->slug; ?> #msl_reset').on('click', msl_request_reset_<?php echo str_replace("-", "_", $this->slug); ?>);

					} else {
						$('#license_activation_wrapper_<?php echo $this->slug; ?> .license_activation_form').html('라이센스 정보를 확인할 수 없습니다.');
					}
					$('#license_activation_wrapper_<?php echo $this->slug; ?>').unblock();
				},
				error: function () {
					$('#license_activation_wrapper_<?php echo $this->slug; ?>').unblock();
				}
			});
		}

		$('#license_activation_wrapper_<?php echo $this->slug; ?>').block({message: ''});

		$.ajax({
			type: "post",
			dataType: "json",
			url: ajaxurl,
			data: {
				action: "msl_verify_<?php echo $this->slug; ?>"
			},
			success: function (response) {
				if (response.success) {
					$('#license_activation_wrapper_<?php echo $this->slug; ?> .license_activation_form').html(response.data.html);
					$('#license_activation_wrapper_<?php echo $this->slug; ?> #msl_activation').on('click', msl_request_activation_<?php echo str_replace("-", "_", $this->slug); ?>);
					$('#license_activation_wrapper_<?php echo $this->slug; ?> #msl_reset').on('click', msl_request_reset_<?php echo str_replace("-", "_", $this->slug); ?>);
				} else {
					$('#license_activation_wrapper_<?php echo $this->slug; ?> .license_activation_form').html('라이센스 정보를 확인할 수 없습니다.');
				}
				$('#license_activation_wrapper_<?php echo $this->slug; ?>').unblock();
			},
			error: function () {
				$('#license_activation_wrapper_<?php echo $this->slug; ?>').unblock();
			}
		});
	});
</script>
<style>
	#license_activation_wrapper_<?php echo $this->slug; ?> {
		font-size: 0.95em;
	}

	#license_activation_wrapper_<?php echo $this->slug; ?> div {
		border-radius: 0 !important;
	}

	.license_activation_form {
		padding: 5px !important;
	}
</style>
<div id="license_activation_wrapper_<?php echo $this->slug; ?>">
	<div>라이센스 정보</div>
	<div class="license_activation_form">
	</div>
</div>