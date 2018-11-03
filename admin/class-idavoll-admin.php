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
		if ('settings_page_idavoll' == get_current_screen() -> id) {
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
	    add_options_page( 'Idavoll Booking Setup', 'Idavoll Booking Setup', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
	    add_menu_page( 'Idavoll Booking System', 'Booking', 'manage_options', $this->plugin_name . '/booking', array($this, 'booking_page'), 'dashicons-calendar-alt', 3 );

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

	public function booking_page() {
		include_once( 'partials/idavoll-admin-booking.php' );
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


    public function percentageToFactor($percentage) {
    	return (float)$percentage / 100.00;
    }

	public function cap_in_admin_action() {
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavoll_input  = $_POST['idavoll-capacity'];
    	$is_main_capacity = $idavoll_input['main_capacity'] == 'on' ? true : false;
		$capacity_type = $idavoll_input['capacity_type'];
		$max = $idavoll_input['capacity_max'];
		$price_factor = $this->percentageToFactor($idavoll_input['capacity_factor']);
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$db_func->storeCapacityItem($is_main_capacity, $capacity_type, $max, $price_factor);
    	// error_log("wpse10501_admin_action: " . print_r($_POST, 1) , 0);
    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}

	public function price_plan_in_admin_action() {
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavoll_input  = $_POST['idavoll-price-plan'];
    	// error_log("price_plan_in_admin_action: " . print_r($idavoll_input, 1) , 0);
    	$price_type = $idavoll_input['price_type'];
		$base_amount = $idavoll_input['base_amount'];
		$single_factor = $this->percentageToFactor($idavoll_input['single_factor']);
		$price_items = $idavoll_input['price_item'];
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$last_id = $db_func->storePricePlan($base_amount, $single_factor, $price_type);
    	$db_func->storePricePlanPlanItem($last_id, $price_items);
    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}

	public function price_plan_item_in_admin_action() {
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavoll_input  = $_POST['idavoll-price-plan-item'];
    	$item_name = $idavoll_input['item_name'];
		$factor = $this->percentageToFactor($idavoll_input['factor']);
		$day_of_week = $idavoll_input['day_of_week'];
		$start_date = $idavoll_input['start_date'];
		$end_date = $idavoll_input['end_date'];
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$db_func->storePricePlanItem($item_name, $factor, $day_of_week, $start_date, $end_date);
    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}

	public function room_type_in_admin_action() {
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavoll_input  = $_POST['idavoll-room-type'];
		// error_log("room_type_in_admin_action: " . print_r($idavoll_input, 1) , 0);
    	$type_name = $idavoll_input['type_name'];
		$id_room_capacity_item = $idavoll_input['main_capacity_item'];
		$add_caps = $idavoll_input['add_cap'];

		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$last_id = $db_func->storeRoomType($type_name, $id_room_capacity_item);
		$db_func->storeCapacityAdditional($last_id, $add_caps);

    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}

	public function room_in_admin_action() {
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavoll_input  = $_POST['idavoll-room'];
		// error_log("room_type_in_admin_action: " . print_r($idavoll_input, 1) , 0);
    	$room_name = $idavoll_input['room_name'];
    	$room_description = $idavoll_input['room_description'];
		$id_room_type = $idavoll_input['room_type'];
		$id_price_plan = $idavoll_input['price_plan'];

		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$db_func->storeRoom($room_name, $room_description, $id_room_type, null, $id_price_plan);

    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}

	public function book_room_in_admin_action() {
    	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    	$idavoll_input  = $_POST['idavoll-book'];
		// error_log("room_type_in_admin_action: " . print_r($idavoll_input, 1) , 0);
		$room = explode("|", $idavoll_input['room']);
    	$id_room = $room[0];
    	$id_main_capacity = $idavoll_input['main_capacity_id'];
    	$main_capacity_number = $idavoll_input['main_capacity'];
    	$add_cap_ids = $idavoll_input['add_capacity_id'];
    	$add_cap_number = $idavoll_input['add_capacity_id'];
    	$from = $idavoll_input['from'];
    	$to = $idavoll_input['to'];
    	$contact_name = $idavoll_input['contact_name'];
    	$contact_telephone = $idavoll_input['contact_telephone'];
    	$contact_email = $idavoll_input['contact_email'];
		$deposit = (!isset($idavoll_input['deposit']) || empty($idavoll_input['deposit'])) ? 0 : $idavoll_input['deposit'];
		//TODO error checking!
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
    	$db_func->storeBook($id_room, $id_main_capacity, $main_capacity_number, $add_cap_ids, $add_cap_number, $from, $to, $contact_name, $contact_telephone, $contact_email ,$deposit);
    	wp_redirect( $_SERVER['HTTP_REFERER'] );
    	exit();
	}

	public function rooms_available_admin_action() {
		//For demo
		error_log("[DEBUG] rooms_available_admin_action: " . print_r($_POST, 1) , 0);
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
		$db_func = new Idavoll_DB_Func();
		$rooms = $db_func->getAllRooms();
		$ret_str = json_encode($rooms);
		ob_clean();
		echo $ret_str;
		wp_die(); 

		//Need to do this for real
		$input_ids  = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_STRING);
		$room_type_ids = explode("|", $input_ids);
		if(is_null($room_type_ids) || count($room_type_ids) != 2) {
			//Empty list
			$ret = array();
	    	$ret_str = json_encode($ret);
			ob_clean();
			echo $ret_str;
			wp_die(); 	
		}
		//TODO Error handling!
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-idavoll-db-func.php';
		$db_func = new Idavoll_DB_Func();
		$room_type = $db_func->getRoomType($room_type_ids[1]);
    	$main_cap = $db_func->getCapacityItem($room_type->id_room_capacity_item);
    	$add_caps = $db_func->getCapacityItemByRoomTypes($room_type_ids[1]);
    	$ret = array($main_cap, $add_caps);
    	$ret_str = json_encode($ret);
		ob_clean();
		echo $ret_str;
		wp_die(); 
	}
}
