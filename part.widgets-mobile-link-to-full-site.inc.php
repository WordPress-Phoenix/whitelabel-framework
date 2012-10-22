<?php
/**
 * Adds WLFW_Full_Site_Link widget.
 */

class WLFW_Full_Site_Link extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'wlfw_full_site_link', // Base ID
			'WLFW Link to full site', // Name
			array( 'description' => __( 'Displays a link to full version of your website', 'wlfw' ), ) // Args
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
		echo get_wlfw_link_to_full_site_html( $instance['link_text']);
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
		$instance['link_text'] = strip_tags( $new_instance['link_text'] );

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
		if ( isset( $instance[ 'link_text' ] ) ) {
			$link_text = $instance[ 'link_text' ];
		}
		else {
			$link_text = __( 'View Full Site', 'wlfw' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link Text:', 'wlfw' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>" />
		</p>
		<?php 	
	}

} // class WLFW_Full_Site_Link

// register wlfw_full_site_link
add_action( 'widgets_init', create_function( '', 'register_widget( "wlfw_full_site_link" );' ) );

	
function get_wlfw_link_to_full_site_html($link_text) {
	$output = '<p class="footer-left">';
	if(defined('SM_MOBILE_FULLSITE_URL') && SM_MOBILE_FULLSITE_URL != '') $fullsitelink = '<a href="'.SM_MOBILE_FULLSITE_URL.SM_MOBILE_FULLSITE_QUERY.'">'.$link_text.'</a>';
	else $fullsitelink = '<a href="/wp-admin/themes.php?page=mobile-appearance-options">Please set full site URL in Utilities tab of Mobile Options panel</a>';
    $output .= $fullsitelink;
	$output .= '</p>';
	return $output;
}