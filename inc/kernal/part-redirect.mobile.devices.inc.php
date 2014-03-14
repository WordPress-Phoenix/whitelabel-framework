<?php
/*
 * Init the redirect mobile devices functions
 */
$maybe_redirect_mobile_devices = esc_url(get_option(SM_SITEOP_PREFIX.'redirect_mobile_devices'));

//make sure its not empty and not false (when option is not in the database)
//turn off redirection based on URL of Mobile Website
if(!is_admin() && !empty($maybe_redirect_mobile_devices) && $maybe_redirect_mobile_devices && (!isset($_COOKIE['mobile_viewfullsite']) || $_COOKIE['mobile_viewfullsite'] != true) ) {

    //allow custom filters to be built to programmatically disable redirect
    //return false to disable the redirect based on any logic (per page, post type, etc)
    if(apply_filters('maybe_redirect_mobile_devices',true)) {

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
}


/*
 * Begin Mobile Redirection Functions
 * Turn off redirection based on Redirection Disabled URL Parameter
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
        //$mobile_detector = detect_mobile_using_curl();
        require_once(__DIR__.'/lib.mobiledetect.class.php');
        $detect = new Mobile_Detect;
        $deviceType =      ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
        $mobile_detector = ($detect->isMobile() ? ($detect->isTablet() ? true     : true   ) : false);
        $_COOKIE['browser_type'] = $deviceType;
        setcookie('browser_type', $_COOKIE['mobile_viewfullsite'], 0);
    }
    else {
        $mobile_detector = $_COOKIE['browser_type'];
    }

    //redirect mobile device visitors to this URL
    $mobile_redirect_url = apply_filters('wlfw_mobile_redirect_url', $maybe_redirect_mobile_devices);

    //mobile browser is detected, redirect to mobile website using HTML meta tag
    if($mobile_detector) {
        echo( '<html><head><meta http-equiv="Refresh" content="1;url='.$mobile_redirect_url.'" /></head><body>Mobile Device Detected, redirecting you to the mobile website at '.$mobile_redirect_url.'.</body></html>' );
        exit;
    }

    //mobile browser was not detected message output when debug is requested using URL parameter
    elseif(isset($_GET['mobile_detector_debug'])) echo 'Mobile Device not detected';
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
