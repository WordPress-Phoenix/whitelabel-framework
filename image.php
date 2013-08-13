<?php
/**
 * The default template file.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

//LOADING header.php + actions
get_header(); 
?>
<div id="middleWrapper" class="<?php echo apply_filters( 'middle_wrapper_section_class', ''); ?>" data-role="content">
    <div id="middleContainer" class="<?php echo apply_filters('middle_container_row_class', wlfw_grid_row_class(16)); ?>">
        <div id="middleSidebar" class="<?php wlfw_grid_col_class(5); ?>">
			<?php get_sidebar('middle'); ?>
        </div>
        <div id="contentColWide" class="<?php echo apply_filters('wlfw_content_col_class', wlfw_grid_col_class(16)); ?> content-primary">
			<?php get_template_part( 'part.media', 'image' ); ?>
            <?php if(posts_nav_link()){} else wp_link_pages(); ?>
        </div><!-- ./contentColWide -->
        <div class="clear"></div>
    </div><!-- #middleContainer -->
</div><!-- #middleWrapper -->
<?php get_footer(); ?>