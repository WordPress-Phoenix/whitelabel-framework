<?php
/**
 * Adds WLFW_Subpages widget.
 */
class WLFW_Subpages extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'wlfw_subpages', // Base ID
			'WLFW Subpages', // Name
			array( 'description' => __( 'Displays a menu that contains subpages and a link back to their parent page', 'wlfw' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo get_wlfw_subpages_html();
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Learn More', 'wlfw' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wlfw' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 	
	}

} // class WLFW_Subpages

// register wlfw subpages widget
add_action( 'widgets_init', create_function( '', 'register_widget( "wlfw_subpages" );' ) );

	
function get_wlfw_subpages_html() {
	global $post;
	$output = '<ul>';
	
	// if no post id for this page use front page id
	if(!$post)
		$post_id = get_option('page_on_front');
	else
		$post_id = $post->ID;
	
	$parent = get_post_ancestors( $post_id );
	
	if(isset($parent[0]))
		$output .= '<li class="parent-link"><a href="'.get_permalink( $parent[0] ).'">'.get_the_title($parent[0] ).'<span>&uarr;</span></a></li>';
	elseif(!is_front_page())
		$output .= '<li class="parent-link"><a href="'.get_home_url().'">Home<span>&uarr;</span></a></li>';
	
	//if (is_page( )) {
	  $page = $post_id;
	  if ($post && $post->post_parent) {
		$page = $post->post_parent;
	  }
	  $children=wp_list_pages( 'echo=0&child_of=' . $page . '&title_li=' );
	  if ($children && $page!=0) {
		$output .= wp_list_pages ('echo=0&child_of=' . $page . '&title_li=');
	  }
	  else
	  	// list top level pages when not on a page
    	$output .= wp_list_pages('echo=0&depth=1&title_li=' );
	//}
	
	$output .= '</ul>';
	return $output;
}