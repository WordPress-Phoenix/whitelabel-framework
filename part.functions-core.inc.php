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
?>