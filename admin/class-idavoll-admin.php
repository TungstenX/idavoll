<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ParanoidAndroid.co.za
 * @since      1.0.0
 *
 * @package    Idavoll
 * @subpackage Idavoll/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Idavoll
 * @subpackage Idavoll/admin
 * @author     André Labuschagné <andre@paranoidandroid.co.za>
 */
class Idavoll_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( 'settings_page_idavoll' == get_current_screen() -> id ) {
             // CSS stylesheet for colour Picker
             wp_enqueue_style( 'wp-color-picker' );            
             wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/idavoll-admin.css', array( 'wp-color-picker' ), $this->version, 'all' );
         }
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( 'settings_page_idavoll' == get_current_screen() -> id ) {
            wp_enqueue_media();   
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/idavoll-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );         
        }
	}

	/**
 	* Register the administration menu for this plugin into the WordPress Dashboard menu.
 	*
 	* @since    1.0.0
 	*/ 
	public function add_plugin_admin_menu() {

	    /*
	     * Add a settings page for this plugin to the Settings menu.
	     *
	     * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
	     *
	     *        Administration Menus: http://codex.wordpress.org/Administration_Menus
	     *
	     */
	    add_options_page( 'Idavoll Booking Setup', 'Idavoll Booking Setup', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
	    );
	}

	 /**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
	    /*
	    *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	    */
	   $settings_link = array(
	    '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
	    include_once( 'partials/idavoll-admin-display.php' );
	}

	public function options_update() {
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
	}

	public function validate($input) {
		// All inputs        
		$valid = array();

		//Cleanup the inputs
		$valid['deposit'] = (!isset($input['deposit']) || empty($input['deposit'])) ? 50.0 : floatval($input['deposit']);
		$valid['cancellationfee'] = (!isset($input['cancellationfee']) || empty($input['cancellationfee'])) ? 50.0 : floatval($input['cancellationfee']);
		$valid['cancellationdays'] = (!isset($input['cancellationdays']) || empty($input['cancellationdays'])) ? 14 : intval($input['cancellationdays']);

		//First colour Picker
        $valid['background_colour'] = (isset($input['background_colour']) && !empty($input['background_colour'])) ? sanitize_text_field($input['background_colour']) : '';

        if ( !empty($valid['background_colour']) && !preg_match( '/^#[a-f0-9]{6}$/i', $valid['background_colour']  ) ) { 
        	// if user insert a HEX colour with #
            add_settings_error(
                'background_colour',                     // Setting title
                'background_colour_texterror',            // Error ID
                'Please enter a valid hex value colour',     // Error message
                'error'                         // Type of message
            );
        }

		return $valid;
	}

	public function cap_in_admin_action() {
    	// Do your stuff here
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavol_input  = $_POST['idavoll-capacity'];
    	$is_main_capacity = $idavol_input['main_capacity'] == 'on' ? true : false;
		$capacity_type = $idavol_input['capacity_type'];
		$max = $idavol_input['capacity_max'];
		$price_factor = $idavol_input['capacity_factor'];
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$db_func->storeCapacityItem($is_main_capacity, $capacity_type, $max, $price_factor);
    	// error_log("wpse10501_admin_action: " . print_r($_POST, 1) , 0);
    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}

	public function price_plan_in_admin_action() {
    	// Do your stuff here
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavol_input  = $_POST['idavoll-price-plan'];
    	$price_type = $idavol_input['price_type'];
		$base_amount = $idavol_input['base_amount'];
		$single_factor = $idavol_input['single_factor'];
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$db_func->storePricePlan($base_amount, $single_factor, $price_type);
    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}
}
