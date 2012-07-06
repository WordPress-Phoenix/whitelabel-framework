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

// theme version # constant for appending to  script and style enqueue
// theme options created with the GPL "siteoptions_builder" class
if( function_exists('wp_get_theme') )
	$theme_data = wp_get_theme();
else
	$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );

define('THEME_VERSION', $theme_data['Version']);

//setup feaux head and foot calls since the script is not detecting
//that we are using actions to load these functions
if(FALSE) { wp_head(); wp_footer(); }

if(defined('WHITELABEL_SITEOPTIONS')) $whitelabelConfigFlag = WHITELABEL_SITEOPTIONS;
if(!defined('WHITELABEL_SITEOPTIONS') || $whitelabelConfigFlag != false) {
	if(!class_exists('sm_options_container'))  
	get_template_part('part', 'siteoptions_builder.class');
	get_template_part('part.theme', 'siteoptions.inc');
}

//mobile redirection tool
//you can use this utility to redirect mobile visitors to your mobile domain
//name by providing the redirection URL in the Appearance Customization menu
if(defined('WHITELABEL_MOBILE')) $whitelabelMobile = WHITELABEL_CONFIG;
if(!defined('WHITELABEL_MOBILE') || $whitelabelMobile != false)
	get_template_part('part.mobile', 'redirect.devices.inc');

//build out the website based on the config options
//this section controls which styles and scripts are enqueued
//as well as all of the theme options as defined by WordPress core
if(defined('WHITELABEL_CONFIG')) $whitelabelConfigFlag = WHITELABEL_CONFIG;
if(!defined('WHITELABEL_CONFIG') || $whitelabelConfigFlag != false)
	get_template_part('part.theme', 'config.inc');

//allow core functions to be completely removed, or overwritten by child them template part
if(defined('WHITELABEL_CORE')) $whitelabelCoreFlag = WHITELABEL_CORE;
if(!defined('WHITELABEL_CORE') || $whitelabelCoreFlag != false)
	get_template_part('part.theme', 'functions.inc');

//allow core fucntions to be completely removed, or overwritten by child them template part
if(defined('WHITELABEL_WPBUILTINS')) $whitelabelBuiltinsFlag = WHITELABEL_WPBUILTINS;
if(!defined('WHITELABEL_WPBUILTINS') || $whitelabelBuiltinsFlag != false) 
	get_template_part('part.theme', 'wpbuiltins.inc');	
	
//allow core fucntions to be completely removed, or overwritten by child them template part
if(defined('WHITELABEL_SHORTCODES')) $whitelabelBuiltinsFlag = WHITELABEL_SHORTCODES;
if(!defined('WHITELABEL_SHORTCODES') || $whitelabelBuiltinsFlag != false) 
	get_template_part('part.theme', 'shortcodes.inc');	
	
//allow widgets to be completely removed, or overwritten by child them template part
if(defined('WHITELABEL_WIDGETS')) $whitelabelWidgetsFlag = WHITELABEL_WIDGETS;
if(!defined('WHITELABEL_WIDGETS') || $whitelabelWidgetsFlag != false)
	get_template_part('part.widgets.inc');		

//allow floating social to be completely removed, or overwritten by child them template part
if(defined('WHITELABEL_FLOATING_SOCIAL')) $floatingSocialFlag = WHITELABEL_FLOATING_SOCIAL;
if(!defined('WHITELABEL_FLOATING_SOCIAL') || $floatingSocialFlag != false)
	get_template_part('part.floating', 'social.inc');

//enable use of sm-debug-bar without requiring the function to be activated.
if(!function_exists('dbug')) { function dbug(){} }

// create Sample Page Elements page when theme is activated
if (isset($_GET['activated']) && is_admin()){
	//we no longer create a sample page as its additional work to remove it on every install
}



function wl_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'wlfw' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'wlfw' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'wlfw' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}