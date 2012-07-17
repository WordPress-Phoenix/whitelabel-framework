<?php

add_theme_support( 'custom-header', array(
	'default-image'          => '',
	'random-default'         => false,
	'width'                  => 0,
	'height'                 => 0,
	'flex-height'            => false,
	'flex-width'             => false,
	'default-text-color'     => '',
	'header-text'            => true,
	'uploads'                => true,
	'wp-head-callback'       => 'wlfw_header_style',
	'admin-head-callback'    => 'wlfw_admin_header_style',
	'admin-preview-callback' => 'wlfw_admin_header_image',
));

function wlfw_header_style() {}
function wlfw_admin_header_style() {}
function wlfw_admin_header_image() {
	if(get_header_image()) {
		echo '
		<div class="grid_10 logo-sibling right">
		  <div class="white"><img class="right clear" src="'.get_header_image().'" alt="'.HEADER_IMAGE_ALT.'">
			<div class="clear"></div>
		  </div>
		</div>
		<div style="clear:both;"></div>
		';
	}
}

//setup custom-menu feature
if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(
		array(
		  'primary_nav' => 'Primary Navigation Below Header',
		  'footer_nav' => 'Footer Navigation'
		)
	);
}
//wp-admin bar extension ->
// new ability to create "subpages" in 1 click
add_action( 'wp_before_admin_bar_render', 'add_subpage_menu_item' );
function add_subpage_menu_item() {
	global $wp_admin_bar, $post, $wp_version;
	
	if (!is_singular() || !is_page() || is_home())
		return;
	
	$args = array(
		'parent' => 'new-content',
		'id' => 'new-subpage',
		'title' => __('Subpage', 'wlfw'),
		'href' => admin_url( "post-new.php?post_type=page&post_parent={$post->ID}"),
	);
	
	if ( version_compare( $wp_version, '3.3', '<' ) ) {
		$wp_admin_bar->add_node('new-content', &$wp_admin_bar->menu, $args);
	} else {
		$wp_admin_bar->add_node($args);
	}
}
//Build Sidebar Area
function wlfw_widgets_init() {
	
	/* TODO: add asides sidebar styling and enable 2011 style sidebar
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'wlfw' ),
		'id' => 'primary-page-widget-area',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h5 class="widget-title">',
		'after_title' => '</h5>',
	) );
	*/
	// Area 1, located at the top of the page to the right of logo.
	register_sidebar( array(
		'name' => __( 'Logo Aside', 'wlfw' ),
		'id' => 'logo-aside',
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'after_widget' => '<div class="clear"></div></div>',
	) );
	// Area 2, located at the top of the page sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Page Sidebar', 'wlfw' ),
		'id' => 'primary-page-widget-area',
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'after_widget' => '<div class="clear"></div></div>',
	) );
}
add_action( 'widgets_init', 'wlfw_widgets_init' );