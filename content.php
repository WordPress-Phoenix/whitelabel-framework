<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ( has_post_thumbnail() && get_option('wlfw_featured_image_on_content') == 'true' ) { the_post_thumbnail('', array('class' => 'bm20')); } ?>
    <h1><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wlfw' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
    <?php the_content(); ?>
    <div class="clear"></div>
</div><!-- #post-## -->
<div class="entry-meta">
    <?php wl_posted_in(); ?>
</div><!-- .entry-meta -->
<?php comments_template( '', true ); ?>
