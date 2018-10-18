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
	<h2 class="nav-tab-wrapper"><?php _e('Booking Options', $this->plugin_name); ?></h2>
	<?php settings_errors(); ?>
	<!-- Tab links -->
	<?php
	function page_tabs($current = 'options') {
	    $tabs = array(
	        'options'   => __('Options', 'idavoll'), 
	        'capacity'  => __('Capacity', 'idavoll'), 
	        'priceplan'  => __('Price Plans', 'idavoll')
	    );
	    $html = '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
	        $html .= '<a class="nav-tab ' . $class . '" href="?page=idavoll&tab=' . $tab . '">' . $name . '</a>';
	    }
	    $html .= '</h2>';
	    echo $html;
	}
            
    $tab = ( ! empty( $_GET['tab'] ) ) ? esc_attr( $_GET['tab'] ) : 'options';
	page_tabs( $tab );
    
    if ($tab == 'options') { 
    	// Options
	    require_once plugin_dir_path( __FILE__ ) . 'idavoll-admin-options.php';   
	} else if ($tab == 'capacity') {
		// Capacity
	    require_once plugin_dir_path( __FILE__ ) . 'idavoll-admin-capacity.php';
	} else if ($tab == 'priceplan') {
		// Capacity
	    require_once plugin_dir_path( __FILE__ ) . 'idavoll-admin-price-plan.php';
	}
    ?>
    

</div>
