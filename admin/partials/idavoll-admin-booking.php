<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ParanoidAndroid.co.za
 * @since      1.0.0
 *
 * @package    Idavoll
 * @subpackage Idavoll/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<!-- <?php echo $this->plugin_name; ?> -->
	<h2 class="nav-tab-wrapper"><?php _e('Booking', $this->plugin_name); ?></h2>
	<?php settings_errors(); ?>
	<!-- Tab links -->
	<?php
	function page_tabs($current = 'book') {
	    $tabs = array(
	        'book'   => __('Book', 'idavoll'), 
	        'bookings'  => __('Bookings', 'idavoll')
	    );
	    $html = '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
	        $html .= '<a class="nav-tab ' . $class . '" href="?page=idavoll%2Fbooking&tab=' . $tab . '">' . $name . '</a>';
	    }
	    $html .= '</h2>';
	    echo $html;
	}
            
    $tab = ( ! empty( $_GET['tab'] ) ) ? esc_attr( $_GET['tab'] ) : 'book';
	page_tabs( $tab );
    
    if ($tab == 'book') { 
    	// Options
	    require_once plugin_dir_path( __FILE__ ) . 'idavoll-admin-book.php';   
	} else if ($tab == 'bookings') {
		// Capacity
	    require_once plugin_dir_path( __FILE__ ) . 'idavoll-admin-booking-list.php';
	}
    ?>
</div>