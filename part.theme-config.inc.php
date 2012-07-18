<?php
//adding support for custom header image
//by default it controls the image area to the right of the logo area on the left.
if(!defined('NO_HEADER_TEXT')) define('NO_HEADER_TEXT', TRUE );
if(!defined('HEADER_TEXTCOLOR')) define('HEADER_TEXTCOLOR', '');
if(!defined('HEADER_IMAGE')) define('HEADER_IMAGE', trailingslashit( get_stylesheet_directory_uri() ).'appearance/images/banner.jpg');
if(!defined('HEADER_IMAGE_ALT')) define('HEADER_IMAGE_ALT', 'header banner image');
if(!defined('HEADER_IMAGE_WIDTH')) define('HEADER_IMAGE_WIDTH', apply_filters( 'wlfw_header_image_width', 580 ));
if(!defined('HEADER_IMAGE_HEIGHT')) define('HEADER_IMAGE_HEIGHT', apply_filters( 'wlfw_header_image_height', 80 ));

//turning on all the theme options by default
//simply set any of these to false to turn them off, 
//keep in mind there may be dependecies between these modules to consider
if(!defined('WHITELABEL_SITEOPTIONS')) define('WHITELABEL_SITEOPTIONS', TRUE );
if(!defined('WHITELABEL_CONFIG')) define('WHITELABEL_CONFIG', TRUE );
if(!defined('WHITELABEL_CORE')) define('WHITELABEL_CORE', TRUE );
if(!defined('WHITELABEL_WPBUILTINS')) define('WHITELABEL_WPBUILTINS', TRUE );
if(!defined('WHITELABEL_FLOATING_SOCIAL')) define('WHITELABEL_FLOATING_SOCIAL', TRUE );

//TODO: Create an activation function so it only runs 1 time
update_option('wpm_o_user_id', 1); //turn off whitelabel plugin advertisement
add_theme_support('post-thumbnails');
add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );
add_theme_support('automatic-feed-links');
add_theme_support( 'custom-background', array(
	'default-color'          => '#FFF',
	'default-image'          => '',
	'wp-head-callback'       => '_custom_background_cb',
	'admin-head-callback'    => '',
	'admin-preview-callback' => '')
);
add_editor_style('editor-style.css');
add_filter('use_default_gallery_style', '__return_false');

//as required by wordpress.org
if ( ! isset( $content_width ) ) $content_width = 960;

//load scripts only if not in the wordpress admin dashbaord area
//organized by top of page to bottom of page in loading order
if (!is_admin()) {
	//fix ubillboard from overwriting scripts
	remove_action('wp_print_scripts', 'uds_billboard_scripts');
	if(function_exists('uds_billboard_scripts')) add_action('wp_enqueue_scripts', 'uds_billboard_scripts', 10);
	//begin queueing scripts
	add_action('wp_enqueue_scripts', 'setup_scripts_and_styles_enqueue', 20);
	add_action('wp_print_styles', 'load_theme_stylesheet_last', 99);
	add_action('build_theme_head', 'get_template_part', 10, 2);
	add_action('build_theme_head', 'wp_head', 20);
	add_action('build_theme_head', 'load_head_closing', 90);
	add_action('body_enqueue', 'get_template_part', 10, 2);
	add_action('build_theme_header', 'get_template_part', 10, 2);
	add_action('build_theme_footer', 'get_template_part', 10, 2);
	add_action('footer_enqueue', 'get_template_part', 10, 2);
	add_action('footer_enqueue', 'wp_footer', 20);
}
else {
	global $errors;
	//enable error notice in admin is theme framework is activated instead of using a child theme
	if(get_template_directory() == get_stylesheet_directory()) {
		$all_themes = get_themes();
		foreach($all_themes as $theme_title => $theme_ob) {
			if($theme_ob->get_template() == 'whitelabel-framework' && $theme_ob->get_stylesheet() != 'whitelabel-framework') {
				$child_exists = true;
				break;
			}
		}
		if(!empty($child_exists)) 
			$errors->add('Theme Error', __(sprintf('You are currently using a theme framework as your primary theme. A child theme was detected in your themes list, please activate it now.'), SM_SITEOP_PREFIX));
		else
			$errors->add('Theme Error', __(sprintf('You are currently using a theme framework as your primary theme. Please use our <a href="%1$s">One Click Child Theme Builder</a> to create and activate your child theme right now!', get_admin_url().'themes.php?action=wlfw-create-child-theme'), SM_SITEOP_PREFIX));	
		//turn on admin notices which properly prints the global $errors objects detected errors
		add_action('admin_notices', 'wlfw_admin_display_global_errors');
	}
}
function load_head_closing() { get_template_part('part.head', 'analytics.inc'); }
function load_theme_stylesheet_last() { wp_enqueue_style('style', get_stylesheet_directory_uri().'/style.css', '', THEME_VERSION); }

//default javascript library to load for the framework
function setup_scripts_and_styles_enqueue() {
	wp_register_script( 'jquery-corner' ,get_template_directory_uri(). '/js/jquery.corner.js', array('jquery'), THEME_VERSION );
	wp_register_script( 'google-maps' ,get_template_directory_uri(). '/js/googleMaps.js', '', THEME_VERSION);
	wp_register_script( 'jquery-validate' ,get_template_directory_uri(). '/js/jquery.validate.min.js', array('jquery'), THEME_VERSION);
	wp_register_script( 'jquery-validate-additional-methods' ,get_template_directory_uri(). '/js/additional-methods.js', array('jquery','jquery-validate'), THEME_VERSION);
	wp_register_script( 'jquery-pajinate' ,get_template_directory_uri(). '/js/jquery.pajinate.js', array('jquery'), THEME_VERSION);
	wp_register_script( 'whitelabel-uix' ,get_template_directory_uri(). '/js/whitelabel-uix.js', array('jquery'), THEME_VERSION);
	
	//older version of UI scripts (core, resizeable, dragable, dialog) fixes IE Jump issue
	wp_deregister_script( 'jquery-ui' );
	wp_register_script( 'jquery-ui' ,get_template_directory_uri().'/js/jquery-ui-dialog.custom.min.js', array('jquery'), THEME_VERSION);
	
	
	// load jQuery from external source 
	if( $my_jquery = get_option(SM_SITEOP_PREFIX.'jquery_source')&& get_option(SM_SITEOP_PREFIX.'jquery_source') != 'default' && get_option(SM_SITEOP_PREFIX.'jquery_source') != 'local' )	{ 
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery' ,get_option(SM_SITEOP_PREFIX.'jquery_source'), '',THEME_VERSION  );
	}
	// or load local version (prevents overriding by plugins
	elseif( $my_jquery = get_option(SM_SITEOP_PREFIX.'jquery_source') && get_option(SM_SITEOP_PREFIX.'jquery_source') == 'local' ) {
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery' , network_site_url( '/' ).WPINC. '/js/jquery/jquery.js', '',THEME_VERSION  );
	}
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-corner', array('jquery') );
	wp_enqueue_script( 'jquery-ui', array('jquery') );
	wp_enqueue_script( 'whitelabel-uix', array('jquery') );
	
	//register the grid system
	wp_register_style('reset', get_template_directory_uri().'/appearance/960.gs/css/reset.css', '', THEME_VERSION);
	wp_register_style('960gs', get_template_directory_uri().'/appearance/960.gs/css/960.css', '', THEME_VERSION);
	
	//register nivo slider and jQuery-UI
	wp_register_style('nivo', get_template_directory_uri().'/appearance/nivo-slider.css', '', THEME_VERSION);
	wp_register_style('jquery-ui', get_template_directory_uri().'/appearance/jquery-ui.css', '', THEME_VERSION);
	
	wp_enqueue_style('nivo');
	wp_enqueue_style('jquery-ui');
	wp_enqueue_style('reset');
	wp_enqueue_style('960gs');
}
