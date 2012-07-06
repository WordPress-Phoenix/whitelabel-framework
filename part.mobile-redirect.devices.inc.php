<?php 

/*
 * Init the redirect mobile devices functions
 */
 
global $maybe_redirect_mobile_devices;
$maybe_redirect_mobile_devices = get_option(SM_SITEOP_PREFIX.'redirect_mobile_devices');
$maybe_redirect_mobile_devices = apply_filters('maybe_redirect_mobile_devices', $maybe_redirect_mobile_devices);

//make sure its not empty and not false (when option is not in the database)
//also make sure we didn't already redirect, so that mobile redirection can 
//be set to a location on the same domain as the desktop website
if(!is_admin() && !empty($maybe_redirect_mobile_devices) && $maybe_redirect_mobile_devices && (!isset($_COOKIE['mobile_redirected']) || $_COOKIE['mobile_redirected'] != true) ) {
	redirect_mobile_devices(get_option(SM_SITEOP_PREFIX.'redirect_mobile_devices')); 
}


/*
 * Begin Mobile Redirection Functions
 */

function redirect_mobile_devices($args = array()) {
	global $maybe_redirect_mobile_devices;
	//optionally use a different service provider by setting variables, default is currently:
	//detect_mobile_using_curl($service_url = 'detectmobilebrowsers.com', $html_response_tag = 'h2', $not_mobile_result_string = 'no mobile')
	$mobile_detector = detect_mobile_using_curl();
	
	//allow mobile_redirect_disabler paramenter to cancel out the redirection
	$mobile_redirect_disabler_value = get_option(SM_SITEOP_PREFIX.'mobile_redirect_disabler');
	if(isset($_GET[$mobile_redirect_disabler_value]) && (!empty($_GET[$mobile_redirect_disabler_value]) || $_GET[$mobile_redirect_disabler_value] == 'false') )
		$mobile_detector = false;

	//redirect mobile device visitors to this URL
	//$mobile_redirect_url = 'm.'.$_SERVER['HTTP_HOST'];
	//$mobile_redirect_url = $_SERVER['HTTP_HOST'].'/mobile/';
	$mobile_redirect_url = apply_filters('wlfw_mobile_redirect_url', esc_url($maybe_redirect_mobile_devices));
	$mobile_redirect_url_parts = parse_url($mobile_redirect_url);

	//on error, display the error
	if(is_array($mobile_detector) && isset($mobile_detector['error'])) echo $mobile_detector['error'];
	
	//mobile browser is detected, redirect to mobile website using HTML meta tag
	elseif($mobile_detector) {
		//set the cookie only if the redirection is NOT an external website
		if(!isset($mobile_redirect_url_parts['host'])) {
			$_COOKIE['mobile_redirected'] = true;
			setcookie('mobile_redirected', $_COOKIE['mobile_redirected'], time()+60*60*24*30);
		}
		echo( '<html><head><meta http-equiv="Refresh" content="1;url='.$mobile_redirect_url.'" /><meta name="viewport" content="width=device-width, initial-scale=1"></head><body>Mobile Device Detected, redirecting you to the mobile website at '.$mobile_redirect_url.'.</body></html>' );
		exit;
	}
	
	//mobile browser was not detected message output when debug is requested using URL parameter
	elseif(isset($_GET['mobile_detector_debug'])) echo 'Mobile Device not detected'; 
	
	//most likely you will remove the else and do nothing when mobile is not detected.
	//else echo 'Mobile Device not detected';
}


function detect_mobile_using_curl($service_url = 'detectmobilebrowsers.com', $html_response_tag = 'h2', $not_mobile_result_string = 'no mobile') {
	
	$ch = curl_init(); // initialize curl handle 
	curl_setopt($ch, CURLOPT_URL,$service_url); // set url to post to 
	curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
	curl_setopt($ch, CURLOPT_VERBOSE, 0); // if you want more details, or for testing, change this to 1
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); // times out after 4s 
	curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
	curl_setopt($ch, CURLOPT_POSTFIELDS, ''); // add POST fields 
		
	//echo $post_string; //use to debug the post by manually submitting it with a url
	//run the whole process - you will get a respone in the $result which will have the result code from the lead router
	$curlProcess = curl_exec($ch);
	//echo '<pre>'.print_r($curlProcess, true);exit;
	if(!$curlProcess) return array('error' => 'Server does not support cUrl. Please contact your server administrator or hosting provider.');
	
	$content = getTextBetweenTags($html_response_tag, $curlProcess);
	if(count($content) < 1)	return array('error' => 'HTML Tag &lt;'.$html_response_tag.'&gt; configured in mobile detector was not found in the <a href="'.$service_url.'">service providers</a> response. Please reconfigure the mobile detection function and try again.');
	
	$content = $content[array_search('detected', $content)];
	if(stristr($content, $not_mobile_result_string)) return false;
	else return true;
}

function getTextBetweenTags($tag, $html, $strict=0)
{
    $dom = new domDocument;
    if($strict==1) $dom->loadXML($html);
    else $dom->loadHTML($html);

    $dom->preserveWhiteSpace = false;

    $content = $dom->getElementsByTagname($tag);

    $out = array();
    foreach ($content as $item) $out[] = $item->nodeValue;
    return $out;
}
