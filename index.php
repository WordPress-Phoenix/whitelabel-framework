<?php
/**
 * The default template file.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

//LOADING header.php + actions
get_header(); 
?>
<div id="middleWrapper" data-role="content">
    <div id="middleContainer" class="<?php wlfw_grid_row_class(16); ?>">
        
        <div id="contentColWide" class="<?php wlfw_grid_col_class(11); ?> <?php echo get_post_format(); ?> <?php echo apply_filters('wlfw_content_class', ''); ?> content-primary">
            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<?php 
					if($wp_query->found_posts < 1){ get_template_part( 'content', get_post_format() ); } 
					else { get_template_part( 'loop', get_post_type() ); }
				?>
            <?php endwhile; ?>
        <?php if(posts_nav_link()){} else wp_link_pages(); ?>
        </div><!-- ./contentColWide -->
        
        <div id="middleSidebar" class="<?php wlfw_grid_col_class(5); ?>">
			<?php get_sidebar('middle'); ?>
        </div>
        
        <div class="clear"></div>
    </div><!-- #middleContainer -->
</div><!-- #middleWrapper -->
<?php get_footer(); ?>