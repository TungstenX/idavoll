<?php
/*
Plugin Name: Random Post Widget
Plugin URI: http://jamesbruce.me/
Description: Random Post Widget grabs a random post and the associated thumbnail to display on your sidebar
Author: James Bruce
Version: 1
Author URI: http://jamesbruce.me/
*/

class IdavollBookingWidget extends WP_Widget {

  function IdavollBookingWidget() {
    $widget_ops = array('classname' => 'IdavollBookingWidget', 'description' => 'Idavoll Booking system' );
    $this->WP_Widget('IdavollBookingWidget', 'Idavoll Booking', $widget_ops);
    // $idavoll_options = get_option('idavoll');
    // $bg_colour = $idavoll_options['background_colour'];
    // if(!empty($bg_colour) 
    //   && preg_match('/^#[a-f0-9]{6}$/i', $bg_colour)) {
    //     global $wp_widget_factory;
    //     $background_colour_css  = ".booking_container{ background:" . $bg_colour . "!important;}";
    //   echo "<style>$background_colour_css</style>";
    // }
  }
 
  function form($instance) {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance) {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;
 
    // WIDGET CODE GOES HERE
    $partials = plugin_dir_path( __FILE__ ) . 'public/partials/idavoll-public-display.php';
    require $partials;
    // echo "<h1>This is my new widget!</h1>";
    // echo "<pre>" . print_r($idavoll_options, 1) . "</pre>";
    // echo "<p>" . $slug . "</p>";
 
    echo $after_widget;
  }
}

add_action( 'widgets_init', create_function('', 'return register_widget("IdavollBookingWidget");') );?>
