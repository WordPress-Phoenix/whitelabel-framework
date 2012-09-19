<?php if(get_post_meta($post->ID, '_wlfw_exclude_title', true) =='true' ) add_filter('wlfw_before_title_output', 'wlfw_remove_page_title_h1s'); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ( has_post_thumbnail() && get_option(SM_SITEOP_PREFIX.'featured_image_on_content') == 'true' ) { the_post_thumbnail('', array('class' => 'bm20')); } ?>
    <?php echo apply_filters( 'wlfw_before_title_output', '<h1>'. get_the_title(). '</h1>'); ?>
    <?php the_content(); ?>
    <div class="clear"></div>
</div><!-- #post-## -->
<div class="entry-meta">
    <?php wl_posted_in(); ?>
</div><!-- .entry-meta -->
<?php comments_template( '', true ); ?>