<?php
//function: sm_topmost_parent
//description: returns the top most parent of a page
//optional parameters: post_id  
if(!function_exists('sm_topmost_parent')) { function sm_topmost_parent( $args = array() ) {
	global $post;
	if(!isset($args['post_id'])) 
		$args['post_id'] = $post->ID;		
	$parent_id = get_post($args['post_id'])->post_parent;
	if($parent_id == 0)
		return basename(get_permalink( $args['post_id'] ));
	else
		return sm_topmost_parent( array('post_id'=>$parent_id) );
}}

//function: sm_disable_admin_bar (call before wp_head)
//description: disables admin bar on a per page basis
//optional parameters: none 
if(!function_exists('sm_disable_admin_bar')) { function sm_disable_admin_bar( $args = array() ) {
	add_filter( 'show_admin_bar', '__return_false' );
	remove_action('wp_head', '_admin_bar_bump_cb'); 
	wp_deregister_script('admin-bar');
	wp_deregister_style('admin-bar');
}}

//function: sm_remove_all_styles
//description: remove all enqueued styles on a per page basis
//optional parameters: none 
//how to use: add_action('wp_print_scripts', 'sm_remove_all_styles', 100);
if(!function_exists('sm_remove_all_styles')) { function sm_remove_all_styles( $args = array() ) {
	global $wp_styles;
    $wp_styles->queue = array();
}}

//function: sm_remove_all_scripts
//description: remove all enqueued sscripts on a per page basis
//optional parameters: none 
//how to use: add_action('wp_print_styles', 'sm_remove_all_scripts', 100);
if(!function_exists('sm_remove_all_scripts')) { function sm_remove_all_scripts( $args = array() ) {
	global $wp_scripts;
    $wp_scripts->queue = array();
}}


// Add Subapage link to New menu in admin bar
// TODO - Move link below add new page link
add_filter( 'wp_insert_post_data', 'add_subpage_set_page_parent', 10, 2 );
function add_subpage_set_page_parent( $data, $postarr ) {
	if ( $data['post_status'] == 'auto-draft' && isset( $_GET['post_parent'] ) && !$data['post_parent'] )
		$data['post_parent'] = (int) $_GET['post_parent'];
	return $data;
}

//uses the same arguments as WordPress "checked()" function
//but adds the argument submitted and "default"to allow you 
//to set the default checked value of the checkbox
function wlfw_checked($checkboxPostedValue, $checkboxDefaultValue = 'on', $echo = false, $requiredField = NULL, $default = false) {
	if(empty($requiredField) || (isset($_REQUEST[$requiredField]) && !empty($_REQUEST[$requiredField])) ) {
		return checked($checkboxPostedValue, $checkboxDefaultValue, $echo);
	}
	//if a required field is set, and the required field has not been submitted
	//then page is loading for the first time and needs to load default value (whole point of the function)
	elseif($default) { 
		if($echo) echo 'checked="checked"';
		return 'checked="checked"'; 
	}
	else { global $errors; $errors['wlfw_checked'] = 'wlfw_checked() function failed'; }
}

//disables 404 permalink guessing
function wlfw_disable_404_permalink_guessing( $args=array() ) {
	if( stristr($_SERVER['HTTP_HOST'], 'www.') || max( $_GET['p'], $_GET['page_id'], $_GET['attachment_id'] ) ){  }
	else remove_filter('template_redirect', 'redirect_canonical'); 
}
if(get_option(SM_SITEOP_PREFIX.'disable_404_permalink_guessing')=='true') { wlfw_disable_404_permalink_guessing(); }

// check the current post for the existence of a short code
function wlfw_post_has_shortcode($shortcode = '') {

	$post_to_check = get_post(get_the_ID());
	// false because we have to search through the post content first
	$found = false;
	// if no short code was provided, return false
	if (!$shortcode) {
		return $found;
	}
	// check the post content for the short code
	if ( stripos($post_to_check->post_content, '[' . $shortcode) !== false ) {
		// we have found the short code
		$found = true;
	}
	// return our final results
	return $found;
}

function wlfw_remove_widget_by_base_id() {
	global $wp_registered_widgets; 
	//print_r($wp_registered_widgets);exit;
	$keys = preg_grep("/wlfw_floating_social/", array_keys($wp_registered_widgets) );
	foreach ( $keys as $key ) {
		wp_unregister_sidebar_widget($key);
	}
}