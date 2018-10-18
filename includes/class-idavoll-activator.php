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
class Idavoll_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( __FILE__ ) . 'class-idavoll-db-func.php';
		$db_func = new Idavoll_DB_Func();
		$db_func->db_install();
	}
}