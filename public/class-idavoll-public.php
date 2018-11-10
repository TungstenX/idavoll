<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ParanoidAndroid.co.za
 * @since      1.0.0
 *
 * @package    Idavoll
 * @subpackage Idavoll/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Idavoll
 * @subpackage Idavoll/public
 * @author     André Labuschagné <andre@paranoidandroid.co.za>
 */
class Idavoll_Public {

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

	private $idavoll_options;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->idavoll_options = get_option($this->plugin_name);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Idavoll_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Idavoll_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/idavoll-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Idavoll_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Idavoll_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/idavoll-public.js', array('jquery'), $this->version, false);

	}

	/**
	* Change the plugin's background colour. And other
	*/
    public function idavoll_styles() {
    	$bg_colour = $this->idavoll_options['background_colour'];
        if(!empty($bg_colour) 
        	&& preg_match('/^#[a-f0-9]{6}$/i', $bg_colour)) {
            global $wp_widget_factory;
            $background_colour_css  = ".booking_container{ background:" . $bg_colour . "!important;}";
         	echo "<style>$background_colour_css</style>";
        }
    }

    public function idavoll_booking_load_widget() {
		register_widget( 'idavoll_booking_widget' );
	}

	public function book_room($atts) {
		return "<pre>" . print_r($atts, 1) . "</pre>";
	}

}
