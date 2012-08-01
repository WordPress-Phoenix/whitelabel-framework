<?php
/**
 * The 404 Page template file.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

//LOADING header.php + actions
header("Status: 404 Not Found");
get_header(); 
?>
<div id="middleWrapper">
    <div id="middleContainer" class="<?php wlfw_grid_row_class(16); ?>">
        <div id="middleSidebar" class="<?php wlfw_grid_col_class(5); ?>">
			<?php get_sidebar('middle'); ?>
        </div>
        <div id="contentColWide" class="<?php wlfw_grid_col_class(11); ?>">
            <div id="post-0" class="post error404 not-found">
                <h1 class="entry-title"><?php _e( 'We are sorry but the page you requested does not exist.', 'wlfw' ); ?></h1>
                <div class="entry-content">
                <?php    
                     if(get_option(SM_SITEOP_PREFIX.'autosearch')) { $auto_search_enabled =  get_option(SM_SITEOP_PREFIX.'autosearch'); }
                    else { $auto_search_enabled =  false; }
                    if($auto_search_enabled)
                        get_template_part( 'loop', 'search' ); 
                    else {
                        $default_content_id = get_option(SM_SITEOP_PREFIX.'autosearch_default_content_id');
                        if($default_content_id !='') {
                            global $post;
                            $args = array( 'numberposts' => 5, 'offset'=> 1, 'category' => 1 );
                            $myposts = get_posts( array('include'=> $default_content_id, 'post_type'=> 'page') );
                            
                            foreach( $myposts as $post ) :	setup_postdata($post);
                                the_content();
                            endforeach;
                        }
                    }
                 ?>   
                </div><!-- .entry-content -->
            </div><!-- #post-0 -->
        </div><!-- ./contentColWide -->
        <div class="clear"></div>
    </div><!-- #middleContainer -->
</div><!-- #middleWrapper -->
<?php get_footer(); ?>