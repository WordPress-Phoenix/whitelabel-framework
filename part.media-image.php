<?php
/**
* The template for displaying image attachments.
*/

?>

<?php while ( have_posts() ) : the_post(); $metadata = wp_get_attachment_metadata(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<div class="entry-meta">
                    <nav id="nav-single">
                        <span class="nav-previous"><?php previous_image_link( false, __( '&larr;' , 'wlfw' ) ); ?></span>
                        <?php edit_post_link( __( 'Edit', 'wlfw' ), '<span class="edit-link">', '</span>' ); ?>
                        <span class="nav-next"><?php next_image_link( false, __( '&rarr;' , 'wlfw' ) ); ?></span>
                    </nav><!-- #nav-single -->
                    <span class="meta-prep meta-prep-entry-date">Published <span class="entry-date">
                    	<abbr class="published" title="<?php echo esc_attr( get_the_time() ); ?>"><?php echo get_the_date(); ?></abbr>
                    </span>
					
				</div><!-- .entry-meta -->
				<h1 class="entry-title"><a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" rel="gallery">&uarr;<?php echo esc_attr(strip_tags(get_the_title($post->post_parent))); ?></a><br /><?php the_title(); ?></h1>

			</header><!-- .entry-header -->

			<div class="entry-content">

				<div class="entry-attachment">
					<div class="attachment-mime">
	<?php
    /**
    * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
    * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
    */
    $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
    foreach ( $attachments as $k => $attachment ) {
    if ( $attachment->ID == $post->ID )
    break;
    }
    $k++;
    // If there is more than 1 attachment in a gallery
    if ( count( $attachments ) > 1 ) {
    if ( isset( $attachments[ $k ] ) )
    // get the URL of the next image attachment
    $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
    else
    // or get the URL of the first image attachment
    $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
    } else {
    // or, if there's only 1 image, get the URL of the image
    $next_attachment_url = wp_get_attachment_url();
    }
    ?>
						<a href="<?php echo esc_url(  wp_get_attachment_url() ); ?>" rel="attachment"><?php
						echo wp_get_attachment_image( $post->ID, array( 848, 1024 ) ); // filterable image width with 1024px limit for image height.
						$span_smaller_style = '';
						if($metadata['width'] < 640) {
							$span_smaller_style_font_size = intval($metadata['width']) / 8;
							$span_smaller_style = 'style="width:'.$metadata['width'].'px; font-size: '.$span_smaller_style_font_size.'px;"';
						}
						echo '<span class="size" '.$span_smaller_style.'>'.$metadata['width'].' &times; ' . $metadata['height'].'</span>';
						?></a>

						<?php if ( ! empty( $post->post_excerpt ) ) : ?>
						<div class="entry-caption">
							<?php the_excerpt(); ?>
						</div>
						<?php endif; ?>
					</div><!-- .attachment -->

				</div><!-- .entry-attachment -->

				<div class="entry-description">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'wlfw' ) . '</span>', 'after' => '</div>' ) ); ?>
				</div><!-- .entry-description -->

			</div><!-- .entry-content -->

		</article><!-- #post-<?php the_ID(); ?> -->

		<?php comments_template(); ?>

	<?php endwhile; // end of the loop. ?>