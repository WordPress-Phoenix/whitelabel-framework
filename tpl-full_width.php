<?php

/*

Template Name: Full Width

*/



//LOADING header.php + actions

get_header(); 

?>

<div id="middleWrapper">

    <div id="middleContainer" class="<?php wlfw_grid_row_class(16); ?>">

        <div id="contentColWide" class="<?php wlfw_grid_col_class(16); ?>">

            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php if ( has_post_thumbnail() ) { the_post_thumbnail('', array('class' => 'bm20')); } ?>

                    <h1><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wlfw' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

					<?php the_content(); ?>

                    <div class="clear"></div>

                </div><!-- #post-## -->

                <?php comments_template( '', true ); ?>

            <?php endwhile; ?>

        </div><!-- ./contentColWide -->

        <div class="clear"></div>

    </div><!-- #middleContainer -->

</div><!-- #middleWrapper -->

<?php get_footer(); ?>