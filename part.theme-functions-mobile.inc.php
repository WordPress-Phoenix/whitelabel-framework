<?php
add_filter( 'page_container_div', function($title) { return '<div id="container-fluid" class="type-interior" data-role="page">'; } );
add_action( 'wlfw_before_logo', function($title) { echo '<a data-icon="back" data-rel="back" data-iconpos="notext">Back</a>'; } );
add_action( 'wlfw_after_logo', function($title) { echo ' <a href="#" id="bookmark" data-icon="star" data-iconpos="notext" class="ui-btn-right">Share</a>'; } );
add_action('after_logo_aside', 'get_template_part', 10, 2);
add_action('wlfw_before_dynamic_sidebar', 'wlfw_display_nav');
add_action('wlfw_before_dynamic_sidebar', 'wlfw_add_mobile_menu_header_to_nav_output');

add_filter('wlfw_primary_nav_output', 'wlfw_add_mobile_menu_header_to_nav_output'); 
add_filter('wlfw_add_mobile_menu_header', function($title) { return 'More Info'; } );
add_action('wlfw_after_footer_sidebar', 'wlfw_mobile_top_bttn');

// how to make this only happen on front page (is_front_page doesnt work)
add_action('wlfw_mobile_frontpage_sticky_buttons', 'get_template_part', 10, 2);


//add_filter('wlfw_before_title_output', 'wlfw_remove_page_title_h1s');



//Define global options
define('SM_MOBILE_FULLSITE_URL', get_option(SM_SITEOP_PREFIX.'fullsite_url'));
define('SM_MOBILE_FULLSITE_QUERY', get_option(SM_SITEOP_PREFIX.'fullsite_query'));
define('ISAPPLE', !(strpos($_SERVER['HTTP_USER_AGENT'], 'like Mac OS') === false));

if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(	array(
	  'primary' => 'Mobile Front Page Primary Pages Menu',
	  'more' => 'Mobile Front Page More Pages Menu',
	  'secondary' => 'Mobile Front Page Secondary Links Menu (Blog, etc)',
	  'topbar' => 'Mobile Sticky Top Buttons',
	));
}

//params: $args array: default_title
if(!function_exists('sm_get_nav_menu_title')) { function sm_get_nav_menu_title($id, $args = array('default_title'=>'')) { 
	if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[$id] ) ) {
		$menu_locations = get_nav_menu_locations();
		$menu = get_term_by('id', $menu_locations[$id], 'nav_menu');
		return $menu->name;
	}
	elseif(!empty($args['default_title'])) return $args['default_title'];
	else return __('Browse Pages', 'sm-mobile');
}}

function wlfw_add_mobile_menu_header_to_nav_output($primary_menu) {
	$menu_header_text = apply_filters('wlfw_add_mobile_menu_header', '...');
	return '<h3>'.$menu_header_text.'</h3>'.PHP_EOL.$primary_menu;
}

//function: wp_getShortText
//description: returns a shortened version of post title
//required parameters: text
//optional parameters: length, ellipsis(...)  
if(!function_exists('wp_getShortText')) { function wp_getShortText($text, $length = 50, $ellipsis = '...') {	
	$short_text = substr($text,0,$length);
	$output = $short_text;
	if($short_text!=$text)
		$output .= $ellipsis;
	return $output;
}}

//function: sm_social_media_referal
//description: returns boolean
//required parameters: none 
if(!function_exists('sm_social_media_referral')) { function sm_social_media_referral($args = array()) {
	// if no url is passed grab referrer from server vars	
	if(!isset($args['url'])) $args['url'] = $_SERVER['HTTP_REFERER'];
	
	// check to see if SM domain names exist in url
	if( strripos($args['url'],'facebook.com') || 
		strripos($args['url'],'linkedin.com') || 
		strripos($args['url'],'twitter.com') || 
		strripos($args['url'],'youtube.com') ) {
			@header("Location: ".$args['url_base'].$_SERVER['REQUEST_URI'].$args['url_query']);
			return true;
	}
	else
		return false;
}}

//function: sm_redirect_to_fullsite
//description: sends user to page they requested on the fullsite if it exists
//optional parameters: domain of the live site 
if(!function_exists('sm_redirect_to_fullsite')) { function sm_redirect_to_fullsite($args = array()) {
	if(get_option(SM_SITEOP_PREFIX.'auto_redirect') != 'true')
		return false;
	
	// get full site url from site option
	$fullsite_domain = SM_MOBILE_FULLSITE_URL;
	// if fullsite_domain is passed as arg override site option
	if(isset($args['fullsite_domain']))
		$fullsite_domain = $args['fullsite_domain'];
	// if fullsite url not set in site option and not passed as arg, derive it from mobile url	
	else if(!$fullsite_domain && !isset($args['fullsite_domain']))
		$fullsite_domain = ltrim($_SERVER['HTTP_HOST'], 'm.');
		
	$fullsite_domain = 'http://'.ltrim($fullsite_domain,'http://');	
	$redirect_url = rtrim($fullsite_domain,'/').$_SERVER['REQUEST_URI'];
	
	// attempt to cURL the page on full site
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $redirect_url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1); // times out after 4s 
	$result = curl_exec($ch);
	$resultHeader = curl_getinfo($ch);
	curl_close($ch);
	
	// if page exists on full site redirect to it
	if ($resultHeader['http_code'] == '200') {
		if($_SERVER['QUERY_STRING'])
			@header("Location: ".$redirect_url.'&view=mob404');
		else
			@header("Location: ".$redirect_url.'?view=mob404');
		return true;
	}
	else
		return false;
}}

//process the contact form template file submissions
function processMobileForm($args) {
	$errors = array();
	if(empty($args['contact_name'])) $errors[] = __('Name is required', 'sm');
	if(empty($args['contact_email'])) $errors[] = __('Email is not valid', 'sm');
	elseif(!is_email($args['contact_email'])) $errors[] = __('Email address is not valid', 'sm');
	if(empty($args['contact_message'])) $errors[] = __('Message is required', 'sm');
	elseif(strlen($args['contact_message']) < 10) $errors[] = __('Please give us more details in your message', 'sm');
	if($errors == array()) {
		$headers = 'From: '.esc_attr($args['contact_name']).' <'.$args['contact_email'].'>' . "\r\n";
		$message = 'You have a message from '.esc_attr($args['contact_name']). ' <'.$args['contact_email'].'>.'.PHP_EOL.PHP_EOL;
		$message .= 'Message: '.PHP_EOL.esc_textarea($args['contact_message']).PHP_EOL.PHP_EOL;
		$message .= 'Form submitted at http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$mail1 = wp_mail(get_option('admin_email'), 'Mobile Form Submission from '.esc_attr($args['contact_name']), $message, $headers);
		$headers = 'From: '.get_option('blogname').' <'.get_option('admin_email').">\r\n";
		$message = 'Thank you for sending us a message, we will get back to you shortly.'.PHP_EOL.PHP_EOL;
		$message .= 'Message: '.PHP_EOL.esc_textarea($args['contact_message']).PHP_EOL.PHP_EOL;
		$message .= 'Form submitted at http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$mail2 = wp_mail($args['contact_email'], 'We received your submission', $message, $headers);
		dbug('$mail1');
		dbug($mail1);
		dbug('$mail2');
		dbug($mail2);
	}
	return $errors;
}

// adds top button to footer
function wlfw_mobile_top_bttn() {
	echo '<a class="ui-btn-right" id="top">top</a>';
}