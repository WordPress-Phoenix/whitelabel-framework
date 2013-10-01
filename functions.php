<?php
/**
 * Theme: WhiteLabel Framework (For Developers)
 *
 * @Package: Wordpress 3+
 * @Author: Seth Carstens
 * @Licensed under GPL standards.
 *
 * NOTE ON CHILD THEMES:
 * We highly suggest not modifying the framework, and instead building a child theme.
 * If you decide to use a child theme, do not place any custom functions in this area,
 * and instead use the child theme's functions.php file. Otherwise, add any of custom 
 * functions below this section
 */
 
if(!defined('SM_SITEOP_PREFIX')) define('SM_SITEOP_PREFIX', 'sm_option_');

//setup global errors for use with admin notices
//do not load on media page because core uses an array for errors for some reason...
global $errors, $pagenow;
if($pagenow != 'media.php' && $pagenow != 'uploader.php' && !is_wp_error($errors)) $errors = new WP_Error();

// theme version # constant for appending to  script and style enqueue
// theme options created with the GPL "siteoptions_builder" class
if( function_exists('wp_get_theme') )
	$theme_data = wp_get_theme();
else
	$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );

// load the theme updater core script
// this checks for theme updates using the public GitHub repository
if(is_admin() && file_exists(dirname(__FILE__).'/inc/updater-plugin.php')) 
	include_once(dirname(__FILE__).'/inc/updater-plugin.php');
	
// load and activate the child theme generator. only if the URL is loaded with this GET action
// the file thats included does a check to make sure user has proper permissions to create themes
// ex: http://whitelabelframework.com/wp-admin/themes.php?action=wlfw-create-child-theme
if(is_admin() && file_exists(dirname(__FILE__).'/inc/child-theme-oneclick.php') && !empty($_GET['action']) && $_GET['action'] == 'wlfw-create-child-theme') 
	include_once(dirname(__FILE__).'/inc/child-theme-oneclick.php');

define('THEME_VERSION', $theme_data['Version']);

//setup feaux head and foot calls since the script is not detecting
//that we are using actions to load these functions
if(FALSE) { wp_head(); wp_footer(); }


function wlfw_include_core_files() { 

	$wlfw_parts = array(
		'WHITELABEL_SITEOPTIONS_BUILDER' => array( 'slug' => 'part', 'name' => 'siteoptions_builder.class'),
		'WHITELABEL_SITEOPTIONS' => array( 'slug' => 'part.theme', 'name' => 'siteoptions.inc' ),
		//mobile redirection tool
		//you can use this utility to redirect mobile visitors to your mobile domain
		//name by providing the redirection URL in the Appearance Customization menu
		'WHITELABEL_MOBILE' => array( 'slug' => 'part.mobile', 'name' => 'redirect.devices.inc' ),
		//build out the website based on the config options
		//this section controls which styles and scripts are enqueued
		//as well as all of the theme options as defined by WordPress core
		
		//allow core functions to be completely removed, or overwritten by child them template part
		'WHITELABEL_CORE' => array( 'slug' => 'part.theme', 'name' => 'functions.inc' ),
		'WHITELABEL_WPBUILTINS' => array( 'slug' => 'part.theme', 'name' => 'wpbuiltins.inc' ),
		'WHITELABEL_SHORTCODES' => array( 'slug' => 'part.theme', 'name' => 'shortcodes.inc' ),
		'WHITELABEL_WIDGETS' => array( 'slug' => 'part.widgets.inc', 'name' => '' ),
		'WHITELABEL_FLOATING_SOCIAL' => array( 'slug' => 'part.floating', 'name' => 'social.inc' ),
	);
	
	$wlfw_parts = apply_filters('wlfw_include_core_files', $wlfw_parts);
	
	// remove site options builder if its already being added elsewhere
	if(class_exists('sm_options_container') ) 
		unset($wlfw_parts['WHITELABEL_SITEOPTIONS_BUILDER']);
		
	if( get_option(SM_SITEOP_PREFIX.'grid_system') =='mobile' ) { 

		$wlfw_mobile_parts = array(
		'WHITELABEL_MOBILE_FUNCTIONS' => array( 'slug' => 'part.theme', 'name' => 'functions-mobile.inc' ),
		'WHITELABEL_MOBILE_SITEOPTIONS' => array( 'slug' => 'part.theme', 'name' => 'mobile-siteoptions.inc' ),
		'WHITELABEL_MOBILE_LINK_FULLSITE_WIDGET' => array( 'slug' => 'part.widgets', 'name' => 'mobile-link-to-full-site.inc' ),
		);
		
		$wlfw_parts = array_merge($wlfw_parts, $wlfw_mobile_parts);
	}
			
	$wlfw_parts = array_merge(array('WHITELABEL_CONFIG' => array( 'slug' => 'part.theme.config', 'name' => get_option(SM_SITEOP_PREFIX.'grid_system') )), $wlfw_parts );
		
	foreach($wlfw_parts as $constant => $template_part) {
		if(defined($constant)) $value = constant($constant);
		if(!defined($constant) || $value != false)
		get_template_part($template_part['slug'], $template_part['name']);	
	}
}
add_action('after_setup_theme', 'wlfw_include_core_files');

//enable use of sm-debug-bar without requiring the function to be activated.
if(!function_exists('dbug')) { function dbug(){} }

// create Sample Page Elements page when theme is activated
if (isset($_GET['activated']) && is_admin()){
	//we no longer create a sample page as its additional work to remove it on every install
}