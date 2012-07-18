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

// Add Theme Options link to New menu in admin bar
add_action( 'wp_before_admin_bar_render', 'add_theme_options_menu_item' );
function add_theme_options_menu_item() {
	global $wp_admin_bar, $wp_version;	
	$args = array(
		'parent' => 'site-name',
		'id' => 'theme-options',
		'title' => __('Theme Options', 'wlfw'),
		'href' => admin_url( "themes.php?page=whitelabel-appearance-options"),
	);
	
	if ( version_compare( $wp_version, '3.3', '<' ) ) {
		$wp_admin_bar->add_node('new-content', &$wp_admin_bar->menu, $args);
	} else {
		$wp_admin_bar->add_node($args);
	}
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

// wlfw comments used in comments.php
function wlfw_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'wlfw' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'wlfw' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'wlfw' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'wlfw' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'wlfw' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'wlfw' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'wlfw' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}

// do stuff when theme is activated for the first time
function wlfw_first_activation() {	

	// if on themes page and theme has been activated
	if ( stristr($_SERVER['REQUEST_URI'], 'themes.php') && isset($_GET['activated']) )  {
		
		// get theme data
		if( function_exists('wp_get_theme') ) $theme_data = wp_get_theme();
		else $theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
		// create option name for check name based on theme name
		$option_name = sanitize_key($theme_data['Name'].'_first_activation_check');
	
		if ( get_option($option_name) != "set" ) {
			// do stuff if its the first time being activated
			do_action('wlfw_first_activation');
			// set option so it doesn't run in future
			add_option($option_name, "set");
		}
	}
}
add_action( 'after_setup_theme','wlfw_first_activation', 1 );

// prevent wordpress from auto populating sidebars with default widgets
function remove_wp_widget_autopopulate() {
	do_action( 'widgets_init' );
	remove_action( 'init', 'wp_widgets_init', 1 );
}
add_action('wlfw_first_activation', 'remove_wp_widget_autopopulate' );