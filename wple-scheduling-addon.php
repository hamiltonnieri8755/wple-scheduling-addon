<?php
/*
Plugin Name:       WPLE Advanced Scheduling Add-On
Plugin URI:        https://github.com/wp-lab/wple-scheduling-addon
Description:       Extend WP-Lister for eBay with custom schedule times per product and incremental bulk scheduling.
Version:           0.1
Author:            Matthias Krok
License:           GNU General Public License v2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
Domain Path:       /languages
Text Domain:       wple-scheduling-addon
GitHub Plugin URI: https://github.com/wp-lab/wple-scheduling-addon
GitHub Branch:     master
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// include main class
require_once dirname( __FILE__ ) . '/classes/wple-custom-schedules.php';
require_once dirname( __FILE__ ) . '/classes/wple-scheduling-settings.php';

/**
 * Init main class
 */
function wple_scheduling_init() {

	// $class = new WPLE_Custom_Schedules();
	// add_filter( 'some_hook', array( $class, 'register_something' ) );

	WPLE_Custom_Schedules::init();
	WPLE_Scheduling_Settings::init();

}
add_action( 'plugins_loaded', 'wple_scheduling_init', 9 ); // saving settings require registering actions early
// add_action( 'init', 'wple_scheduling_init' );
