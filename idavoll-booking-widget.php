<?php
// Creating the widget 
class idavoll_booking_widget extends WP_Widget {

	function idavoll_booking_widget() {
    	$widget_ops = array('classname' => 'idavoll_booking_widget', 'description' => 'Displays a random post with thumbnail' );
    	$this->WP_Widget('idavoll_booking_widget', 'Random Post and Thumbnail', $widget_ops);
  	}
  	
	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'idavoll_booking_widget', 

			// Widget name will appear in UI
			__('Idavoll Booking Widget', 'idavoll_booking_widget_domain'), 

			// Widget description
			array( 'description' => __( 'Idavoll Booking system', 'wpb_widget_domain' ), ) 
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		echo __( 'Hello, World!', 'wpb_widget_domain' );
		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'idavoll_booking_widget_domain' );
		}
		// Widget admin form
?>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} 

add_action( 'widgets_init', create_function('', 'return register_widget("idavoll_booking_widget");') );?>