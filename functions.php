<?php
/**
 * Theme: WhiteLabel Framework (For Developers) Installer
 *
 * @Package: Wordpress 3+
 * @Author: Seth Carstens
 * @Licensed under GPL standards.
 *
 */

// theme version # constant for appending to  script and style enqueue
// theme options created with the GPL "siteoptions_builder" class
if( function_exists('wp_get_theme') )
	$theme_data = wp_get_theme();
else
	$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
	
define('THEME_VERSION', $theme_data['Version']);

// load the theme updater core script
// this checks for theme updates using the public GitHub repository
if(is_admin() && file_exists(dirname(__FILE__).'/inc/updater-plugin.php')) 
	include_once(dirname(__FILE__).'/inc/updater-plugin.php');
	

if ( is_multisite() && !current_user_can('manage_network_themes') ) {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );	
}
elseif(basename($_SERVER['REQUEST_URI'], '?'.$_SERVER['QUERY_STRING']) != 'themes.php' || isset($_GET['activated'])) {
	$theme = wp_get_theme();
	$stylesheet = $theme->get_stylesheet();
	$redirect_url = wp_nonce_url('update.php?action=upgrade-theme&theme=' . urlencode($stylesheet), 'upgrade-theme_' . $stylesheet);
	$redirect_url = urldecode ( $redirect_url );
	$redirect_url = str_replace('&amp;', '&',$redirect_url);
	//die($update_url);
	
	if( !stristr( $_SERVER['REQUEST_URI'], 'update.php') )
	wp_redirect( admin_url( $redirect_url ) );
}