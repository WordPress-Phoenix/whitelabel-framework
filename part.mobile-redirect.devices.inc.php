<?php 
/*
 * Init the redirect mobile devices functions
 */
$maybe_redirect_mobile_devices = esc_url(get_option(SM_SITEOP_PREFIX.'redirect_mobile_devices'));

//make sure its not empty and not false (when option is not in the database)
//turn off redirection based on URL of Mobile Website
if(!is_admin() && !empty($maybe_redirect_mobile_devices) && $maybe_redirect_mobile_devices && (!isset($_COOKIE['mobile_viewfullsite']) || $_COOKIE['mobile_viewfullsite'] != true) ) {
	
	// get url of mobile page and break down into parts
	$mobile_redirect_url_parts = parse_url($maybe_redirect_mobile_devices);
	
	// determine if the mobile redirection page is local or an external site
	$local_redirect=false;
	if( !isset($mobile_redirect_url_parts['host']) || stristr($mobile_redirect_url_parts['host'], $_SERVER['HTTP_HOST']) ) {
		$local_redirect=true;
	}
	
	// if redirection page is local and we are already on it or a subpage of it do not redirect
	if($local_redirect && stristr( $_SERVER['REQUEST_URI'], $mobile_redirect_url_parts['path']));
	// call redirect function
	else redirect_mobile_devices(array('maybe_redirect_mobile_devices'=>$maybe_redirect_mobile_devices)); 
}


/*
 * Begin Mobile Redirection Functions
 * Turn off redirection based on Redrection Disabled URL Parameter
 */

function redirect_mobile_devices($args = array()) {
	//allow mobile_redirect_disabler paramenter to cancel out the redirection
	$mobile_redirect_disabler_value = get_option(SM_SITEOP_PREFIX.'mobile_redirect_disabler');
	if(isset($_GET[$mobile_redirect_disabler_value]) && (!empty($_GET[$mobile_redirect_disabler_value]) || $_GET[$mobile_redirect_disabler_value] == 'false') ) {
		$_COOKIE['mobile_viewfullsite'] = true;
		setcookie('mobile_viewfullsite', $_COOKIE['mobile_viewfullsite'], 0);
		return false;
	}
	
	//optionally use a different service provider by setting variables, default is currently:
	extract($args);
	if(empty($_COOKIE['browser_type'])) {
		$mobile_detector = detect_mobile_using_curl();
		$_COOKIE['browser_type'] = detect_mobile_using_curl();
		setcookie('browser_type', $_COOKIE['mobile_viewfullsite'], 0);
	}
	else { 
		$mobile_detector = $_COOKIE['browser_type'];
	}

	//redirect mobile device visitors to this URL
	$mobile_redirect_url = apply_filters('wlfw_mobile_redirect_url', $maybe_redirect_mobile_devices);

	//on error, display the error
	if(is_array($mobile_detector) && isset($mobile_detector['error'])) add_action('body_enqueue', create_function('', 'echo $mobile_detector["error"];'));
	
	//mobile browser is detected, redirect to mobile website using HTML meta tag
	elseif($mobile_detector) {
		echo( '<html><head><meta http-equiv="Refresh" content="1;url='.$mobile_redirect_url.'" /></head><body>Mobile Device Detected, redirecting you to the mobile website at '.$mobile_redirect_url.'.</body></html>' );
		exit;
	}
	
	//mobile browser was not detected message output when debug is requested using URL parameter
	elseif(isset($_GET['mobile_detector_debug'])) echo 'Mobile Device not detected'; 
}


function detect_mobile_using_curl($service_url = 'detectmobilebrowsers.com', $html_response_tag = 'h2', $not_mobile_result_string = 'no mobile') {
	$ch = curl_init(); // initialize curl handle 
	curl_setopt($ch, CURLOPT_URL,$service_url); // set url to post to 
	curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
	curl_setopt($ch, CURLOPT_VERBOSE, 0); // if you want more details, or for testing, change this to 1
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);// dis-allow redirects to comply with safe mode
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); // times out after 4s 
	curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
	curl_setopt($ch, CURLOPT_POSTFIELDS, ''); // add POST fields 
		
	//echo $post_string; //use to debug the post by manually submitting it with a url
	//run the whole process - you will get a response in the $result which will have the result code from the lead router
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
