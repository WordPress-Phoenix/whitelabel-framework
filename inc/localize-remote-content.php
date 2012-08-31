<?php 
if(isset($_REQUEST['remote']) && $_REQUEST['remote'] == '204') {
	header('HTTP/1.0 204 No Content');
	header('Content-Length: 0',true);
	header('Content-Type: text/html',true);
	flush();
	exit;	
}


require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

if ( eregi("MSIE", getenv( "HTTP_USER_AGENT" ) ) || eregi("Internet Explorer", getenv("HTTP_USER_AGENT" ) ) ) {
  // do something
}

if(isset($_REQUEST['remote'])) {
	$iframe_fix_script = "
	<script type=\"text/javascript\">var prevent_bust = 0; window.onbeforeunload = function() { prevent_bust++ }; setInterval(function() {if (prevent_bust > 0) {prevent_bust -= 2;window.top.location = '".get_template_directory_uri()."/inc/localize-remote-content.php?remote=204'}}, 1); window.alert = function() {}; parent.tb_showIframe(); </script>".PHP_EOL;
	
	//delete_transient( 'wlfw_wp_remote_get_response' );
	//use transients to avoid calling to the site too often
	$trans_update = '';
	if ( false === ( $body = get_transient( 'wlfw_wp_remote_get_response' ) ) ) {
		$response = wp_remote_get( esc_url_raw( $_REQUEST['remote'] ) ); // no need to espace entities
				
		if ( !is_wp_error( $response ) ) {
			$body = wp_remote_retrieve_body( $response );
		}
		$trans_update = '<meta transient="updated" date="'.date('Y-m-d_H~i~s').'" />'.PHP_EOL;
		$github_hide_elements = '<style>body.vis-public.env-production div.column.main { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: white; z-index: 99999; }</style>'.PHP_EOL;
		$body = str_replace('</head>', $trans_update.$iframe_fix_script.$github_hide_elements.'</head>', $body);
		set_transient('wlfw_wp_remote_get_response', $body, 15);
	}
	echo trim($body);
//var_export($response);
}