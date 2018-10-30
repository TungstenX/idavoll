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
 * 
 *
 * @since      1.0.0
 * @package    Idavoll
 * @subpackage Idavoll/includes
 * @author     André Labuschagné <andre@paranoidandroid.co.za>
 */
class Idavoll_Price_Model {

	/**
	* Booking is an array
	* 	- start_date str 2018-10-30 00:00:01
	*	- end_date str 2018-10-31 23:59:59
	*	- number_of_ppl_per_room ass array of arrays
	*		- Map <Room name, List of capacity items>
	*	- rooms 
	*		- price_plan
	*			- base_amount - double
	*			- price_plan_items array of ass arrays
	*				- day_of_week int 1-7 (or -1)
	*				- start_date - can be null
	*				- end_date - can be null
	*				- factor
	*			- price_type - 0 or 1
	*/
	public function makePriceItems($booking) {
		$ret = array();
		$walker = strtotime($booking['start_date']);
		$end_date = strtotime($booking['end_date']) - 86400;//not the last day
		while($walker < $end_date) {
			foreach ($booking['rooms'] as $key => $value) {
				$this->doPricing($ret, $booking, $value, $walker);
			}
			$walker += 86400;//One day in seconds
		}
		return $ret;
	}

	/**
	* @param $ret - ass array
	* @param $booking - ass array
	*	- number_of_ppl_per_room ass array of arrays
	*		- Map <Room name, List of capacity items
	* @param $room - ass array
	*	- price_plan
	*		- base_amount - double
	*		- price_plan_items array of ass arrays
	*			- day_of_week int 1-7 (or -1)
	*			- start_date - can be null
	*			- end_date - can be null
	*			- factor
	*		- price_type - 0 or 1
	*	- room_name
	* @param $date - long
	*/
	private function doPricing(&$ret, $booking, $room, $date) {
		$amount = $room['price_plan']['base_amount'];
		$ppe_factor = $this->getPricePlanEntryFactor($room['price_plan']['price_plan_items'], $date);
		if($room['price_plan']['price_type'] == 0) {
			$amount *= $ppe_factor;
			$price_item = &$this->findPriceItem($ret, $room, $amount, $date);
			$price_item['times'] = $price_item['times'] + 1;
			$this->updatePriceItem($ret, $room, $amount,$price_item);
		} else {
			$caps = $booking['number_of_ppl_per_room'][$room['room_name']];
			foreach ($caps as $key => $value) {
				$room_cap_factor = $this->getRoomCapFactor($value, $room);
				$single_factor = $this->isMainCapacity($value, $room) ? ($value['max'] === 1 ? $room['price_plan']['single_factor'] : 1.0) : 1.0;
				$temp_amount = $amount * $ppe_factor * $room_cap_factor * $single_factor * $value['max'];
				$value['price_factor'] = $room_cap_factor;
				$price_item = &$this->findPriceItem($ret, $room, $temp_amount, $date, $value);
				$price_item['times']++;
				$this->updatePriceItem($ret, $room, $temp_amount, $price_item);
			}
		}
	}

	/**
	* @param $price_plan_items array of ass arrays
	*	- day_of_week int 1-7 (or -1)
	*	- start_date - can be null
	*	- end_date - can be null
	*	- factor
	* @param $date long
	*/
	private function getPricePlanEntryFactor($price_plan_items, $date) {
		$factor = 1.0;
		// error_log("getPricePlanEntryFactor: " . print_r($price_plan_items, 1) , 0);
		
		foreach ($price_plan_items as $key => $value) {
			$day_of_week = intVal(date("N", $date));			
			if (($value['day_of_week'] == $day_of_week) 
				|| ((!is_null($value['start_date']) && !is_null($value['end_date']))
					&& (($date >= $value['start_date']) && ($date <= $value['end_date'])))) {
				$factor += $value['factor'];
			}
		}
		return $factor;
	}

	/**
	* @param $ret array of ass arrays
	*	- room - ass array
	*		- room_name
	*	- amount - double
	*	- end_date - long
	*   - times - int
	*	- start_date - long
	* @param $room
	*	- room_name
	*/
	private function &findPriceItem(&$ret, $room, $amount, $date, $capacity_item = NULL) {
		foreach ($ret as $key => $value) {
			if (($value['room']['room_name'] === $room['room_name']) && ($value['amount'] === $amount)) {
				$value['end_date'] = $date;
				return $value;
			}
		}
		$price_item = array(
			'amount' => $amount, 
			'room' => $room,
			'capacity_item' => $capacity_item,
			'start_date' => $date,
			'end_date' => NULL,
			'times' => 0			
		);
		array_push($ret, $price_item);
		return $price_item;
	}

	private function updatePriceItem(&$ret, $room, $amount, $price_item) {
		foreach ($ret as $key => $value) {
			if (($value['room']['room_name'] === $room['room_name']) && ($value['amount'] === $amount)) {
				$ret[$key] = $price_item;
				return;
			}
		}
		array_push($ret, $price_item);
	}

	/**
	* @param $capacity_item ass array
	*	- room_capacity_type
	*	- price_factor - double
	*	- max - int
	* @param $room ass array
	*	- room_name
	*	- room_type ass array
	*		- room_capacity
	*			- main_capacity capacity_item
	*			- additional_capacity ass array of capacity_item
	*/
	private function getRoomCapFactor($capacity_item, $room) {
		foreach ($room['room_type']['room_capacity']['additional_capacity'] as $key => $value) {
			if($value['room_capacity_type'] === $capacity_item['room_capacity_type']) {				
				return $value['price_factor'];
			}
		}
		return 1.0;
	}

	/**
	* @param $capacity_item ass array
	*	- room_capacity_type
	*	- price_factor - double
	*	- max - int
	* @param $room ass array
	*	- room_name
	*	- room_type ass array
	*		- room_capacity
	*			- main_capacity capacity_item
	*			- additional_capacity ass array of capacity_item
	*/
	private function isMainCapacity($capacity_item, $room) {
		return $capacity_item['room_capacity_type'] === $room['room_type']['room_capacity']['main_capacity']['room_capacity_type'];
	}
}
/* For testing */
/*$model = new Idavoll_Price_Model();
$booking = array(
		'start_date' => '2018-10-30 00:00:00', 
		'end_date' => '2018-11-01 23:59:59', 
		'number_of_ppl_per_room' => array(
			'102' => array(
				array(
					'room_capacity_type' => 'Adults',
					'price_factor' => 1.0,
					'max' => 1
				),
				array(
					'room_capacity_type' => 'Kids under 13',
					'price_factor' => 0.75,
					'max' => 1
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
					'price_type' => 1
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
echo "<pre>" . print_r($model->makePriceItems($booking), 1) . "</pre>";*/