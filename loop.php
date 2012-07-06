<?php
/**
 * The loop that displays posts.
 *
*/ 
$thumbnail_option = get_option('wlfw_featured_image_on_loop')
?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
   
    <h1><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wlfw' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php if ( $thumbnail_option != 'false' && has_post_thumbnail() ) { the_post_thumbnail('thumbnail', array('class' => 'bm20 alignright')); } ?> <?php the_title(); ?></a></h1>
    <?php if(get_post_format() == 'gallery') the_content(); else the_excerpt(); ?>
    <div class="clear"></div>
</div><!-- #post-## -->
<?php comments_template( '', true );