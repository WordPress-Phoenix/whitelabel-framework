<?php if(get_post_meta($post->ID, '_wlfw_exclude_title', true) =='true' ) add_filter('wlfw_before_title_output', 'wlfw_remove_page_title_h1s'); ?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ( has_post_thumbnail() && get_option(SM_SITEOP_PREFIX.'featured_image_on_content') == 'true' && !defined ( 'WLFW_CONTENT_POST_THUMBNAIL_DISABLED' ) ) { the_post_thumbnail('', array('class' => 'bm20')); } ?>
    <?php echo apply_filters( 'wlfw_before_title_output', '<h1>'. get_the_title(). '</h1>'); ?>
    <?php if( is_single() ) echo apply_filters('wlfw_post_info', wlfw_post_info()); ?>
    <?php the_content(); ?>
    <div class="clear after-content"></div>
    <?php do_action('before_post_close_div'); ?>
</div><!-- #post-## -->
<?php echo apply_filters( 'wlfw_entry-meta', '<div class="entry-meta">'. wlfw_posted_in(). '</div><!-- .entry-meta -->'); ?>
<?php do_action('wlfw_comments_template', '', true); ?>