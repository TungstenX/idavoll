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

	private static $ihs_db_version = "1.0.7";

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
  			id_admin_user bigint(20),
  			id_changed_by bigint(20),
  			created_at timestamp,
  			changed_at timestamp,
  			start_date date DEFAULT '0000-00-00' NOT NULL,
  			end_date date,
  			base_amount double,
  			contact_name varchar(255),
  			contact_telephone varchar(15),
  			contact_email varchar(60),
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
	*	- Done: Insert, Select
	*	- TODO: Update
	* ihs_price_plan_item
	*	- Done: Insert, Select
	*	- TODO: Update
	* 
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
   			id_booking mediumint(9),
   			room_capacity_type varchar(25),
   			price_factor double,
   			number_of_ppl int(3),
   			room_name varchar(255),
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
   			item_name varchar(20),
   			factor double,
   			day_of_week int(1) DEFAULT -1,
   			start_date date,
  			end_date date,
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		
		//Not sure about this?
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
	* ihs_room
	*	- Done: Insert, Select
	*	- TODO: Update
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
   			weight int(2),
   			number_of_bookings mediumint(9) DEFAULT 0,
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		

		// $table_name_room_capacity = $wpdb->prefix . "ihs_room_capacity"; 
		// $sql = "CREATE TABLE " . $table_name_room_capacity . " (
  //  			id mediumint(9) NOT NULL AUTO_INCREMENT,
  //  			id_capacity_item_main mediumint(9),
  //  			PRIMARY KEY  (id)
		// ) " . $charset_collate . ";";
		// $this->doTheSql($sql);		

		$table_name_room_capacity_additional = $wpdb->prefix . "ihs_room_capacity_additional"; 
		$sql = "CREATE TABLE " . $table_name_room_capacity_additional . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			cap_order int(1),
   			id_room_type mediumint(9),
   			id_capacity_item mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		

		$table_name_room_type = $wpdb->prefix . "ihs_room_type"; 
		$sql = "CREATE TABLE " . $table_name_room_type . " (
   			id mediumint(9) NOT NULL AUTO_INCREMENT,
   			type_name varchar(255),
   			id_room_capacity_item mediumint(9),
   			weight int(2),
   			number_of_bookings mediumint(9),
   			PRIMARY KEY  (id)
		) " . $charset_collate . ";";
		$this->doTheSql($sql);		
	}

	public function getAllCapacityItems() {
		global $wpdb;
		$table_name_capacity = $wpdb->prefix . "ihs_capacity_item"; 
		$rows = $wpdb->get_results( "SELECT id, main_capacity, capacity_type, max, price_factor FROM " . $table_name_capacity . " ORDER BY main_capacity DESC");
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
		$lastid = $wpdb->insert_id;
		return $lastid;
	}

	public function getAllPricePlanItems() {
		global $wpdb;
		$table_name_price_plan_item = $wpdb->prefix . "ihs_price_plan_item";
		$rows = $wpdb->get_results( "SELECT id, item_name, factor, day_of_week, start_date, end_date FROM " . $table_name_price_plan_item);
		return $rows;
	}

	public function storePricePlanItem($item_name, $factor, $day_of_week, $start_date, $end_date) {
		global $wpdb;
		$table_name_price_plan_item = $wpdb->prefix . "ihs_price_plan_item";
		$rows = $wpdb->insert( $table_name_price_plan_item, 
			array(
				'item_name' => $item_name, 
				'factor' => $factor, 
				'day_of_week' => $day_of_week,
				'start_date' => date('Y-m-d', strtotime($start_date)),
				'end_date' => date('Y-m-d', strtotime($end_date)))
		);
		return $rows;
	}

	public function storePricePlanPlanItem($id_price_plan, $id_price_plan_items) {
		global $wpdb;
		$table_name_price_plan_plan_item = $wpdb->prefix . "ihs_price_plan_plan_item"; 
		foreach ($id_price_plan_items as $key => $value) {
			$rows = $wpdb->insert($table_name_price_plan_plan_item, 
				array(
					'id_price_plan' => $id_price_plan, 
					'id_price_plan_item' => $value),
				array(
					'%d',
					'%d'
				));	
		}
	}

	/******************************************************************************************************************************************/
	/* Room */
	/******************************************************************************************************************************************/
	public function getRoom($id) {
		global $wpdb;
		$table_name_room = $wpdb->prefix . "ihs_room";
		$row = $wpdb->get_row( "SELECT id, room_name, room_description, id_room_type, id_room_composite, id_price_plan, weight, number_of_bookings FROM " . $table_name_room . " WHERE id=$id");
		return $row;
	}

	public function getAllRooms() {
		global $wpdb;
		$table_name_room = $wpdb->prefix . "ihs_room";
		$rows = $wpdb->get_results( "SELECT id, room_name, room_description, id_room_type, id_room_composite, id_price_plan, weight, number_of_bookings FROM " . $table_name_room . " ORDER BY weight DESC, number_of_bookings DESC, room_name");
		return $rows;
	}

	/**
	* TODO How to handle , $main_capacity, $additional_capacity
	*/
	public function getRoomsAvailable($id_room_type, $start_date, $end_date) {
		global $wpdb;
		$table_name_room = $wpdb->prefix . "ihs_room";
		$sql = "SELECT id, room_name, room_description, id_room_type, id_room_composite, id_price_plan, weight, number_of_bookings FROM " . $table_name_room;
		if($id_room_type > 0) {
		 $sql .= " WHERE id_room_type=$id_room_type";
		}
		$sql .= " ORDER BY weight DESC, number_of_bookings DESC, room_name";
		$rows = $wpdb->get_results($sql);
		return $rows;
	}

	public function storeRoom($room_name, $room_description, $id_room_type, $id_room_composite, $id_price_plan, $weight, $number_of_bookings = 0) {
		global $wpdb;
		$table_name_room = $wpdb->prefix . "ihs_room";
		$rows = $wpdb->insert( $table_name_room, 
			array(
				'room_name' => $room_name, 
				'room_description' => $room_description,
				'id_room_type' => $id_room_type,
				'id_room_composite' => $id_room_composite,
				'id_price_plan' => $id_price_plan,
				'weight' => $weight,
				'number_of_bookings' => $number_of_bookings
			),
			array(
				'%s',
				'%s', 
				'%d',
				'%d',
				'%d',
				'%d',
				'%d')
		);
		
		$lastid = $wpdb->insert_id;
		return $lastid;
	}

	public function getPricePlanItems($price_plan_id) {		
		global $wpdb;
		$table_name_price_plan_plan_item = $wpdb->prefix . "ihs_price_plan_plan_item"; 
		$table_name_price_plan_item = $wpdb->prefix . "ihs_price_plan_item"; 
		$sql = "SELECT ppi.id as id, ppi.item_name as item_name, ppi.factor as factor, ppi.day_of_week as day_of_week, ppi.start_date as start_date, ppi.end_date as end_date FROM " . $table_name_price_plan_item . " ppi INNER JOIN  " . $table_name_price_plan_plan_item . " pi ON ppi.id = pi.id_price_plan_item WHERE ppi.id = " . $price_plan_id . " ORDER BY ppi.start_date, ppi.day_of_week";	

		//error_log("[DEBUG] getPricePlanItems: SQL: {$sql}", 0);
		$rows = $wpdb->get_results($sql);
		return $rows;	
	}

	/******************************************************************************************************************************************/
	/* Room type */
	/******************************************************************************************************************************************/
	public function getRoomType($id) {
		global $wpdb;
		$table_name_room_type = $wpdb->prefix . "ihs_room_type";
		$row = $wpdb->get_row( "SELECT id, type_name, id_room_capacity_item, weight, number_of_bookings FROM " . $table_name_room_type . " WHERE id=$id");
		return $row;
	}

	public function getAllRoomTypes() {
		global $wpdb;
		$table_name_room_type = $wpdb->prefix . "ihs_room_type";
		$rows = $wpdb->get_results( "SELECT id, type_name, id_room_capacity_item, weight, number_of_bookings FROM " . $table_name_room_type . " ORDER BY weight DESC, number_of_bookings DESC, type_name");
		return $rows;
	}

	public function storeRoomType($type_name, $id_room_capacity_item, $weight, $number_of_bookings = 0) {
		global $wpdb;
		$table_name_room_type = $wpdb->prefix . "ihs_room_type";
		$rows = $wpdb->insert( $table_name_room_type, 
			array(
				'type_name' => $type_name, 
				'id_room_capacity_item' => $id_room_capacity_item,
				'weight' => $weight,
				'number_of_bookings' => $number_of_bookings
			),
			array(
				'%s', 
				'%d',
				'%d',
				'%d')
		);
		$lastid = $wpdb->insert_id;
		return $lastid;
	}

	public function storeCapacityAdditional($id_room_type, $add_caps) {
		global $wpdb;
		$table_name_room_capacity_additional = $wpdb->prefix . "ihs_room_capacity_additional"; 
		$counter = 0;
		foreach ($add_caps as $key => $value) {
			$rows = $wpdb->insert($table_name_room_capacity_additional, 
				array(
					'cap_order' => $counter,
					'id_room_type' => $id_room_type, 
					'id_capacity_item' => $value),
				array(
					'%d',
					'%d',
					'%d'
				));	
			$counter++;
		}
	}

	public function getCapacityItem($id) {
		global $wpdb;
		$table_name_capacity = $wpdb->prefix . "ihs_capacity_item"; 
		$row = $wpdb->get_row( "SELECT id, main_capacity, capacity_type, max, price_factor FROM " . $table_name_capacity . " WHERE id = $id");
		return $row;
	}

	public function getCapacityItemByRoomTypes($id_room_type) {
		global $wpdb;
		$table_name_room_capacity_additional = $wpdb->prefix . "ihs_room_capacity_additional";
		$table_name_capacity = $wpdb->prefix . "ihs_capacity_item"; 
		$rows = $wpdb->get_results("SELECT c.id AS id, c.main_capacity AS main_capacity, c.capacity_type AS capacity_type, c.max AS max, c.price_factor AS price_factor FROM " .$table_name_room_capacity_additional . " ca INNER JOIN " . $table_name_capacity . " c ON c.id = ca.id_capacity_item WHERE ca.id_room_type = " . $id_room_type . " ORDER BY ca.cap_order");
		return $rows;	
	}


	public function getPricePlan($id) {
		global $wpdb;
		$table_name_price_plan = $wpdb->prefix . "ihs_price_plan";
		$row = $wpdb->get_row( "SELECT id, base_amount, single_factor, price_type FROM " . $table_name_price_plan . " WHERE id=$id");
		return $row;
	}

	public function storeBook($id_room, $id_main_capacity, $main_capacity_number, $add_cap_ids, $add_cap_number, $from, $to, $contact_name, $contact_telephone, $contact_email, $deposit) {
		global $wpdb;
		$user_id = get_current_user_id();		
		$room = $this->getRoom($id_room);
		//error_log("[DEBUG] storeBook[1]: room:  " . print_r($room, 1), 0);
		$price_plan = $this->getPricePlan($room->id_price_plan);
		//error_log("[DEBUG] storeBook[2]: price_plan:  " . print_r($price_plan, 1), 0);
		$table_name_booking = $wpdb->prefix . "ihs_booking";
		$rows = $wpdb->insert( $table_name_booking, 
			array(
				'id_admin_user' => $user_id, 
  				'created_at' => date('Y-m-d H:i:s'),
				'start_date' => date('Y-m-d', strtotime($from)), 
				'end_date' => date('Y-m-d', strtotime($to)),
				'base_amount' => $price_plan->base_amount,
				'contact_name' => $contact_name,
				'contact_telephone' => $contact_telephone,
				'contact_email' => $contact_email)
		);
		if($wpdb->last_error !== '') {
    		$wpdb->print_error();
    		exit();
    	}
		$lastid = $wpdb->insert_id;		
   		$main_cap = $this->getCapacityItem($id_main_capacity);	
		//error_log("[DEBUG] storeBook[3]: main_cap:  " . print_r($main_cap, 1), 0);
   		$caps = array(
			array(
	   			'room_capacity_type' => $main_cap->capacity_type,
				'price_factor' => $main_cap->price_factor,
				'max' => $main_capacity_number
			)
		);
		$room_caps = array(
			'main_capacity' => array(
				'room_capacity_type' => $main_cap->capacity_type,
				'price_factor' => $main_cap->price_factor,
				'max' => $main_cap->max
			),
			'additional_capacity' => array()
		);

		foreach ($add_cap_ids as $key => $value) {
			$add_cap = $this->getCapacityItem($value);	
			$cap_add = array(
	   			'room_capacity_type' => $add_cap->capacity_type,
				'price_factor' => $add_cap->price_factor,
				'max' => $add_cap_number[$key]
			);
			$room_cap_add = array(
	   			'room_capacity_type' => $add_cap->capacity_type,
				'price_factor' => $add_cap->price_factor,
				'max' => $add_cap->max
			);
			//error_log("[DEBUG] storeBook[4.1]: cap_add:  " . print_r($cap_add, 1), 0);
			array_push($caps, $cap_add);
			array_push($room_caps['additional_capacity'], $room_cap_add);
		}
		//error_log("[DEBUG] storeBook[4]: caps:  " . print_r($caps, 1), 0);
		//error_log("[DEBUG] storeBook[5]: room_caps:  " . print_r($room_caps, 1), 0);

		$price_plan_items = $this->getPricePlanItems($price_plan->id);
		//error_log("[DEBUG] storeBook[6]: price_plan_items:  " . print_r($price_plan_items, 1), 0);
		$price_plan_item_array = array();
		foreach ($price_plan_items as $key => $value) {
			$item = array(
				'day_of_week' => $value->day_of_week,
				'start_date' => $value->start_date . ' 00:00:00',
				'end_date' => $value->end_date . ' 23:59:59',
				'factor' => $value->factor
			);
			array_push($price_plan_item_array, $item);
		}

   		$booking = array(
   			'start_date' => $from . ' 00:00:00', 
   			'end_date' => $to . ' 23:59:59', 
   			'number_of_ppl_per_room' => array(
   				$room->room_name => $caps
   			),
   			'rooms' => array(
   				array(
   					'room_name' => $room->room_name, 
   					'price_plan' => array(
   						'base_amount' => $price_plan->base_amount,
   						'price_plan_items' => $price_plan_item_array,
   						'price_type' => $price_plan->price_type,
   						'single_factor' => $price_plan->single_factor
   					),
   					'room_type' => array(
   						'room_capacity' => $room_caps
   					)
   				)
   			)
   		);

   		require_once plugin_dir_path( __FILE__ ) . 'class-idavoll-price-model.php';
   		$price_model = new Idavoll_Price_Model();
   		$price_items = $price_model->makePriceItems($booking);   		
   		
		error_log("[DEBUG] storeBook[7]: price_items:  " . print_r($price_items, 1), 0);	
		$room_name = "";
		$table_name_price_item = $wpdb->prefix . "ihs_price_item"; 
   		foreach ($price_items as $key => $value) {
   			//error_log("[DEBUG] storeBook[8]: value:  " . print_r($value, 1), 0);
   			$end_date = isset($value['end_date']) ? $value['end_date'] : strtotime($to);
   			$rows = $wpdb->insert( $table_name_price_item, 
   				array(
   					'amount' => floatval($value['amount']), 
   					'start_date' => date('Y-m-d', $value['start_date']), 
   					'end_date' => date('Y-m-d', $value['end_date']),
   					'times' => $value['times'],
   					'id_booking' => $lastid,
   					'room_capacity_type' => $value['capacity_item']['room_capacity_type'],
   					'price_factor' => $value['capacity_item']['price_factor'],
   					'number_of_ppl' => $value['capacity_item']['max'],
   					'room_name' => $value['room']['room_name']
   				)
   			);

   			//Increment booking number for room
   			if($room_name != $value['room']['room_name']) {
   				$room_name = $value['room']['room_name'];
	   			$table_name_room = $wpdb->prefix . "ihs_room";
	   			$wpdb->query("UPDATE " . $table_name_room . " SET number_of_bookings=number_of_bookings+1 WHERE room_name='" . $value['room']['room_name'] . "'");
	   		}
   		}

		return $lastid;
	}

	public function getBookingList() {
		global $wpdb;
		$table_name_booking = $wpdb->prefix . "ihs_booking"; 
		$table_name_price_item = $wpdb->prefix . "ihs_price_item"; 
		$sql = "SELECT b.id AS id, b.id_admin_user AS id_admin_user, b.start_date AS booking_start_date, b.end_date AS booking_end_date, b.base_amount AS base_amount, b.contact_name AS contact_name, b.contact_telephone AS contact_telephone, b.contact_email AS contact_email, p.id AS item_id, p.amount AS amount, p.start_date AS start_date, p.end_date AS end_date, p.times AS times, p.room_capacity_type AS room_capacity_type, p.price_factor AS price_factor, p.number_of_ppl AS number_of_ppl, p.room_name AS room_name FROM " . $table_name_booking . " b INNER JOIN " . $table_name_price_item . " p ON b.id = p.id_booking ORDER BY b.id DESC, p.start_date";
		$rows = $wpdb->get_results($sql);
		return $rows;	
	}
}
/* Input

$booking = array(
		'start_date' => '2018-10-30 00:00:00', 
		'end_date' => '2018-11-01 23:59:59', 
		'number_of_ppl_per_room' => array(
			'102' => array(
				array(
					'room_capacity_type' => 'Adults',
					'price_factor' => 1.0,
					'max' => 2
				),
				array(
					'room_capacity_type' => 'Kids under 13',
					'price_factor' => 0.75,
					'max' => 2
				) 
			)
		),
		'rooms' => array(
			array(
				'room_name' => '102', 
				'price_plan' => array(
					'base_amount' => 999.99,
					'price_plan_items' => array(
						array(
							'day_of_week' => -1,
							'start_date' => '2018-12-13 00:00:00',
							'end_date' => '2019-01-13 23:59:59',
							'factor' => 1.25
						)
					),
					'price_type' => 1,
					'single_factor' => 1.25
				),
				'room_type' => array(
					'room_capacity' => array(
						'main_capacity' => array(
							'room_capacity_type' => 'Adults',
							'price_factor' => 1.0),
						'additional_capacity' => array(
							array(
								'room_capacity_type' => 'Kids under 13',
								'price_factor' => 0.75
							)
						)
					)
				)
			)
		)
	);

*/
/* Return
Array
(
    [0] => Array
        (
            [amount] => 1999.98 - TAKE - DONE
            [room] => Array
                (
                    [room_name] => 102 - TAKE
                    [price_plan] => Array
                        (
                            [base_amount] => 999.99
                            [price_plan_items] => Array
                                (
                                    [0] => Array
                                        (
                                            [day_of_week] => -1
                                            [start_date] => 2018-12-13 00:00:00
                                            [end_date] => 2019-01-13 23:59:59
                                            [factor] => 1.25
                                        )

                                )

                            [price_type] => 1
                        )

                    [room_type] => Array
                        (
                            [room_capacity] => Array
                                (
                                    [main_capacity] => Array
                                        (
                                            [room_capacity_type] => Adults
                                            [price_factor] => 1
                                        )

                                    [additional_capacity] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [room_capacity_type] => Kids under 13
                                                    [price_factor] => 0.75
                                                )

                                        )

                                )

                        )

                )

            [capacity_item] => Array
                (
                    [room_capacity_type] => Adults - TAKE
                    [price_factor] => 1 - TAKE
                    [max] => 2 - TAKE
                )

            [start_date] => 1540850400 - TAKE - DONE
            [end_date] => 1540936800 - TAKE - DONE 
            [times] => 2 - TAKE - DONE
        )

    [1] => Array
        (
            [amount] => 1499.985
            [room] => Array
                (
                    [room_name] => 102
                    [price_plan] => Array
                        (
                            [base_amount] => 999.99
                            [price_plan_items] => Array
                                (
                                    [0] => Array
                                        (
                                            [day_of_week] => -1
                                            [start_date] => 2018-12-13 00:00:00
                                            [end_date] => 2019-01-13 23:59:59
                                            [factor] => 1.25
                                        )

                                )

                            [price_type] => 1
                        )

                    [room_type] => Array
                        (
                            [room_capacity] => Array
                                (
                                    [main_capacity] => Array
                                        (
                                            [room_capacity_type] => Adults
                                            [price_factor] => 1
                                        )

                                    [additional_capacity] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [room_capacity_type] => Kids under 13
                                                    [price_factor] => 0.75
                                                )

                                        )

                                )

                        )

                )

            [capacity_item] => Array
                (
                    [room_capacity_type] => Kids under 13
                    [price_factor] => 0.75
                    [max] => 2
                )

            [start_date] => 1540850400
            [end_date] => 1540936800
            [times] => 2
        )

)
*/