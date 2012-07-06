<?php

/**

 * The template for displaying Comments.

 */

 

/* Copy of Twenty Eleven Comment function

 * @since Twenty Eleven 1.0

 */
if ( is_singular() ) wp_enqueue_script( "comment-reply" ); 

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



?>

	<div id="comments">

	<?php if ( post_password_required() ) : ?>

		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'wlfw' ); ?></p>

	</div><!-- #comments -->

	<?php

			/* Stop the rest of comments.php from being processed,

			 * but don't kill the script entirely -- we still have

			 * to fully load the template.

			 */

			return;

		endif;

	?>



	<?php // You can start editing here -- including this comment! ?>



	<?php if ( have_comments() ) : ?>

		<h2 id="comments-title">

			<?php

				printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'wlfw' ),

					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );

			?>

		</h2>



		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>

		<nav id="comment-nav-above">

			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'wlfw' ); ?></h1>

			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'wlfw' ) ); ?></div>

			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'wlfw' ) ); ?></div>

		</nav>

		<?php endif; // check for comment navigation ?>



		<ol class="commentlist">

			<?php

				/* Loop through and list the comments. Tell wp_list_comments()

				 * to use wlfw_comment() to format the comments.

				 * If you want to overload this in a child theme then you can

				 * define wlfw_comment() and that will be used instead.

				 * See wlfw_comment() in wlfw/functions.php for more.

				 */

				wp_list_comments( array( 'callback' => 'wlfw_comment' ) );

			?>

		</ol>



		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>

		<nav id="comment-nav-below">

			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'wlfw' ); ?></h1>

			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'wlfw' ) ); ?></div>

			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'wlfw' ) ); ?></div>

		</nav>

		<?php endif; // check for comment navigation ?>



	<?php

		/* If there are no comments and comments are closed, let's leave a little note, shall we?

		 * But we don't want the note on pages or post types that do not support comments.

		 */

		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :

	?>

		<p class="nocomments"><?php _e( 'Comments are closed.', 'wlfw' ); ?></p>

	<?php endif; ?>



	<?php comment_form(); ?>



</div><!-- #comments -->

