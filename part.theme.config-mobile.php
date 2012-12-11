<?php
//setting up the themes global prefix
if(!defined('THEME_PREFIX')) define('THEME_PREFIX', 'WLFW_' );

//adding support for custom header image
//by default it controls the image area to the right of the logo area on the left.
if(!defined('NO_HEADER_TEXT')) define('NO_HEADER_TEXT', TRUE );
if(!defined('HEADER_TEXTCOLOR')) define('HEADER_TEXTCOLOR', '');
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
	'wp-head-callback'       => 'mppro_custom_background',
	'admin-head-callback'    => '',
	'admin-preview-callback' => '')
);
function mppro_custom_background() {
	$custom_bg_color=get_background_color();
	$custom_bg_image=get_background_image();
	
	if(!empty($custom_bg_color) || !empty($custom_bg_image)) {
		echo '<style type="text/css" id="custom-background-css">'.PHP_EOL;
		echo '.ui-mobile .type-home .ui-content, .ui-mobile .ui-content { '.PHP_EOL;
		if(!empty($custom_bg_color)) {
			echo 'background-color:#'.$custom_bg_color.'!important;'.PHP_EOL;
			echo 'background-image:none!important;'.PHP_EOL;
		}
		if(!empty($custom_bg_image)) {
			echo 'background-image:url('.$custom_bg_image.')!important;'.PHP_EOL;
			echo 'background-repeat: no-repeat; background-position: top center; background-attachment: scroll;'.PHP_EOL;
		}
		echo '}'.PHP_EOL;
		echo '</style>'.PHP_EOL;
	}
}


add_editor_style('editor-style.css');
add_filter('use_default_gallery_style', '__return_false');
add_filter('body_class','wlfw_set_body_class');
add_filter('wlfw_content_class', 'wlfw_get_content_class');

//as required by wordpress.org
if ( ! isset( $content_width ) ) $content_width = 960;

//load scripts only if not in the wordpress admin dashbaord area
//organized by top of page to bottom of page in loading order
if (!is_admin()) {
	//this adds a class to all widgets identifying incrementally
	add_filter('dynamic_sidebar_params', 'wlfw_number_widgets_by_class');
	
	//fix ubillboard from overwriting scripts
	remove_action('wp_print_scripts', 'uds_billboard_scripts');
	if(function_exists('uds_billboard_scripts')) add_action('wp_enqueue_scripts', 'uds_billboard_scripts', 10);
	//begin queueing scripts
	add_action('wp_enqueue_scripts', 'setup_scripts_and_styles_enqueue', 20);
	add_action('wp_print_styles', 'load_theme_stylesheet_last', 99);
	add_action('build_theme_head', 'get_template_part', 10, 2);
	add_action('build_theme_head', 'wp_head', 20);
	add_action('build_theme_head', 'load_head_closing', 90);
	add_action('append_meta_tags', 'wlfw_mobile_meta' );
	add_action('body_class', 'wlfw_set_body_class_for_ie', 5);
	add_action('body_enqueue', 'get_template_part', 10, 2);
	add_action('build_theme_header', 'get_template_part', 10, 2);
	add_action('build_theme_footer', 'get_template_part', 10, 2);
	add_action('footer_enqueue', 'get_template_part', 10, 2);
	add_action('footer_enqueue', 'wp_footer', 20);
	add_action('template_redirect', 'wlfw_add_comments_template');
	
}
else {
	global $errors;
	//enable error notice in admin if theme framework is activated instead of using a child theme
	if(get_template_directory() == get_stylesheet_directory()) {
		$all_themes = wp_get_themes();
		foreach($all_themes as $theme_title => $theme_ob) {
			if($theme_ob->get_template() == 'whitelabel-framework' && $theme_ob->get_stylesheet() != 'whitelabel-framework') {
				$child_exists = true;
				break;
			}
		}
		if(is_wp_error($errors) && !empty($child_exists)) 
			$errors->add('Theme Error', __(sprintf('You are currently using a theme framework as your primary theme. A child theme was detected in your themes list, please activate it now.'), THEME_PREFIX));
		elseif(is_wp_error($errors))
			$errors->add('Theme Error', __(sprintf('You are currently using a theme framework as your primary theme. Please use our <a href="%1$s">One Click Child Theme Builder</a> to create and activate your child theme right now!', get_admin_url().'themes.php?action=wlfw-create-child-theme'), THEME_PREFIX));	
	}

	//turn on admin notices which properly prints the global $errors objects detected errors
	add_action('admin_notices', 'wlfw_admin_display_global_errors');
	
	//init theme activation actions
	add_action( 'after_switch_theme','wlfw_first_activation' );
	
	//add a defaults to the theme upon first activation
	add_action( 'wlfw_first_activation','wlfw_set_theme_defaults' );
	
	//add exclude page titles option
	add_action( 'post_submitbox_misc_actions', 'wlfw_add_page_title_exclude' );
	add_action( 'save_post', 'wlfw_page_title_exclude_save' );
}
function load_head_closing() { get_template_part('part.head', 'analytics.inc'); }
function load_theme_stylesheet_last() { wp_enqueue_style('mobile-styles', get_template_directory_uri().'/appearance/mobile-style.css', '', THEME_VERSION); }

//default javascript library to load for the framework
function setup_scripts_and_styles_enqueue() {
	// ** layout specific styles **//
	//disable all other stylesheets and scripts	
	if(!is_admin()) {
		wp_deregister_script( 'jquery' );
		wp_enqueue_script( 'jquery', get_template_directory_uri().'/js/jq.min.js', '', '1.6.4');	
		wp_enqueue_script( 'jqm', 'http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js', array('jquery'), '1.0.1');
		wp_enqueue_script( 'jqm2', get_template_directory_uri().'/js/jqm-init.js', array('jquery'), THEME_VERSION);
	}
}