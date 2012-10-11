<?php



//disable all other stylesheets and scripts

remove_all_actions('wp_enqueue_scripts');

remove_all_actions('wp_print_styles');



//load mobile stylesheets and scripts

if (!is_admin()) {

	add_action('wp_enqueue_scripts', 'setup_scripts_and_styles_enqueue',1);

	add_action('build_theme_head', 'get_template_part', 10, 2);

	add_action('build_theme_head', 'wp_head', 20);

	add_action('build_theme_head', 'load_head_closing', 90);

	add_action('mobile_body_start', 'mobile_body_start_scripts');

	add_action('body_enqueue', 'get_template_part', 10, 2);

	add_action('build_theme_header', 'get_template_part', 10, 2);

	add_action('build_theme_footer', 'get_template_part', 10, 2);

	add_action('footer_enqueue', 'get_template_part', 10, 2);

	add_action('footer_enqueue', 'wp_footer', 20);

}



function load_head_closing() { get_template_part('part.head', 'analytics.inc'); }



function load_head_entry() {

	//echo '<revision id="1" />'.PHP_EOL; //used to debug page updates

	if ( get_post_meta(get_the_ID(), 'optimizer', true) ) {

		echo get_post_meta(get_the_ID(), 'optimizer', true).PHP_EOL;

	}

}



//javascript to enqueue into the page with proper dependencies for good load order

function setup_scripts_and_styles_enqueue() {

   	if(!is_admin()) {	

		wp_deregister_script( 'jquery' );

		wp_enqueue_script( 'jquery', get_template_directory_uri().'/js/jq.min.js', '', '1.6.4');

		wp_enqueue_script( 'jqm', 'http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js', array('jquery'), '1.0.1');

		wp_enqueue_script( 'jqm2', get_template_directory_uri().'/js/jqm-init.js', array('jquery'), THEME_VERSION);

		wp_register_style('styles', get_template_directory_uri().'/style.css', '', THEME_VERSION);
		wp_enqueue_style( 'styles');
		
		if(get_template_directory_uri() != get_stylesheet_directory_uri() ) {
			wp_register_style('child-styles', get_stylesheet_directory_uri().'/style.css', array('styles'), THEME_VERSION);
			wp_enqueue_style( 'child-styles');
		}

	}

}



?>