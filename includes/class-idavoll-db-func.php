<?php

/**
 * Fired during plugin activation
 *
 * @link       https://ParanoidAndroid.co.za
 * @since      1.0.0
 *
 * @package    Idavoll
 * @subpackage Idavoll/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Idavoll
 * @subpackage Idavoll/includes
 * @author     André Labuschagné <andre@paranoidandroid.co.za>
 */
class Idavoll_DB_Func {

	private static $ihs_db_version = "1.0";

	public function db_install () {
		global $wpdb;
		$installed_version = get_option('ihs_db_option');
		if ( $installed_version !== $ihs_db_version ) {
			$charset_collate = $wpdb->get_charset_collate();
			//Booking
			$this->makeBookingTable($charset_collate);
			$this->makeCapacityTable($charset_collate);
			$this->makePriceTables($charset_collate);
			$this->makeRoomTables($charset_collate);
			update_option( 'ihs_db_version', self::$ihs_db_version );
		}
	}

	private function doTheSql($sql) {
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
		// error_log("Last error for dbDelta: {$wpdb->last_error}", 0);
	}

	/**
	* Version 1.0
	*/
	private function makeBookingTable($charset_collate) {
		global $wpdb;
		$table_name_booking = $wpdb->prefix . "ihs_booking"; 
		$sql = "CREATE TABLE " . $table_name_booking . " (
  			id mediumint(9) NOT NULL AUTO_INCREMENT,
  			start_date date DEFAULT '0000-00-00' NOT NULL,
  			end_date date,
  			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);

		//Booking room / capacity max pivot table		
		$table_name_booking_rooms_ppr = $wpdb->prefix . "ihs_booking_rooms_ppr"; 
		$sql = "CREATE TABLE " . $table_name_booking_rooms_ppr . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			id_booking mediumint(9),
   			id_room mediumint(9),
   			id_capacity mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);
	}

	/**
	* Version 1.0
	* Done: Insert, Select
	* TODO: Update
	*/
	private function makeCapacityTable($charset_collate) {
		global $wpdb;
		$table_name_capacity = $wpdb->prefix . "ihs_capacity_item"; 
		$sql = "CREATE TABLE " . $table_name_capacity . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			main_capacity tinyint(1) DEFAULT 0 NOT NULL,
   			capacity_type varchar(25),
   			max int(3),
   			price_factor double,
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		
	}

	/**
	* Version 1.0
	* ihs_price_plan
	*	- 
	*/
	private function makePriceTables($charset_collate) {
		global $wpdb;
		$table_name_price_item = $wpdb->prefix . "ihs_price_item"; 
		$sql = "CREATE TABLE " . $table_name_price_item . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			amount double NOT NULL,
   			start_date date,
  			end_date date,
   			times int(3),
   			id_capacity_item mediumint(9),
   			id_room mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);

		$table_name_price_plan = $wpdb->prefix . "ihs_price_plan"; 
		$sql = "CREATE TABLE " . $table_name_price_plan . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			base_amount double NOT NULL,
   			single_factor double,
   			price_type int(1),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		

		$table_name_price_plan_item = $wpdb->prefix . "ihs_price_plan_item"; 
		$sql = "CREATE TABLE " . $table_name_price_plan_item . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			factor double,
   			day_of_week int(1) DEFAULT -1,
   			start_date date,
  			end_date date,
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		

		$table_name_price_plan_plan_item = $wpdb->prefix . "ihs_price_plan_plan_item"; 
		$sql = "CREATE TABLE " . $table_name_price_plan_plan_item . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			id_price_plan mediumint(9),
   			id_price_plan_item mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		
	}

	/**
	* Version 1.0
	*/
	private function makeRoomTables($charset_collate) {
		global $wpdb;
		$table_name_room = $wpdb->prefix . "ihs_room"; 
		$sql = "CREATE TABLE " . $table_name_room . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			room_name varchar(255) NOT NULL,
   			room_description text,
   			id_room_type mediumint(9),
   			id_room_composite mediumint(9),
   			id_price_plan mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		

		$table_name_room_capacity = $wpdb->prefix . "ihs_room_capacity"; 
		$sql = "CREATE TABLE " . $table_name_room_capacity . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			id_capacity_item_main mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		

		$table_name_room_capacity_additional = $wpdb->prefix . "ihs_room_capacity_additional"; 
		$sql = "CREATE TABLE " . $table_name_room_capacity_additional . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			cap_order int(1),
   			id_room_capacity mediumint(9),
   			id_capacity_item mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		

		$table_name_room_type = $wpdb->prefix . "ihs_room_type"; 
		$sql = "CREATE TABLE " . $table_name_room_capacity_additional . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			type_name varchar(255),
   			id_room_capacity mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		
	}

	public function getAllCapacityItems() {
		global $wpdb;
		$table_name_capacity = $wpdb->prefix . "ihs_capacity_item"; 
		$rows = $wpdb->get_results( "SELECT id, main_capacity, capacity_type, max, price_factor FROM " . $table_name_capacity);
		return $rows;
	}

	public function storeCapacityItem($main_capacity, $capacity_type, $max, $price_factor) {
		global $wpdb;
		$table_name_capacity = $wpdb->prefix . "ihs_capacity_item"; 
		$rows = $wpdb->insert( $table_name_capacity, 
			array(
				'main_capacity' => $main_capacity, 
				'capacity_type' => $capacity_type,
				'max' => $max,
				'price_factor' => $price_factor),
			array(
				'%d', 
				'%s',
				'%d',
				'%f')
		);
		return $rows;
	}

	public function getAllPricePlans() {
		global $wpdb;
		$table_name_price_plan = $wpdb->prefix . "ihs_price_plan";
		$rows = $wpdb->get_results( "SELECT id, base_amount, single_factor, price_type FROM " . $table_name_price_plan);
		return $rows;
	}

	public function storePricePlan($base_amount, $single_factor, $price_type) {
		global $wpdb;
		$table_name_price_plan = $wpdb->prefix . "ihs_price_plan";
		$rows = $wpdb->insert( $table_name_price_plan, 
			array(
				'base_amount' => $base_amount, 
				'single_factor' => $single_factor,
				'price_type' => $price_type),
			array(
				'%f', 
				'%f',
				'%d')
		);
		return $rows;
	}
}