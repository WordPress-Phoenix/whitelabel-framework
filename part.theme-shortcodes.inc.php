<?php
function qr_code_builder($atts, $content = null) {
        if(isset($_REQUEST['qrSubmission']) && !empty($_REQUEST['qrSubmission'])) {
			if(!isset($_REQUEST['qrTrackingParam']) || $_REQUEST['qrTrackingParam'] != 'on') $qr_param = '';
			elseif(stristr($_REQUEST['qrSubmission'], '?')) $qr_param = '&qr=yes';
			else $qr_param = '?qr=yes';
			$qr_codes = '<div id="theQR"><p>'.esc_url($_REQUEST['qrSubmission']).
				$qr_param.'</p><img src="http://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data='.
				urlencode(esc_url($_REQUEST['qrSubmission'].$qr_param)).'" /><p class="caption">Actual image may be larger then it appears, right click and "Save Image" to download full size QR.<p></div>';
		}
		else {
			unset($_REQUEST['qrSubmission']);
			$qr_codes = '<div id="theQR"><br /><img src="'.get_stylesheet_directory_uri().'/appearance/images/shortcodes/create-qr-code-sample.png"></div>';
		}
			$qr_code_form_EOL = '<br />';
			$qr_code_form_EOL = apply_filters('wlfw_qr_code_form_line_break', $qr_code_form_EOL);
			$qr_code_form = 
				'<form id="qr-code-form" method="post">'.
					'<label for="qrSubmission"> URL: <input type="text" id="qrSubmission" name="qrSubmission" value="'.$_REQUEST['qrSubmission'].'" /></label>'.$qr_code_form_EOL.
					'<label for="qrTrackingParam">GA Tracking Parameter: <input type="checkbox" '.wlfw_checked($_REQUEST['qrTrackingParam'], 'on', false, 'qrSubmission', true).' id="qrTrackingParam" name="qrTrackingParam" /></label>'.$qr_code_form_EOL.
					'<input type="submit" value="Build QR Code" />'.
				'</form>';
			
			if($atts['display'] == 'before') return $qr_codes.$qr_code_form;
			else return $qr_code_form.$qr_code;
}
add_shortcode("qr-code-builder", "qr_code_builder");