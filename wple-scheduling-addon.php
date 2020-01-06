<?php
/*
Plugin Name:       WPLE Advanced Scheduling Add-On
Plugin URI:        https://github.com/wp-lab/wple-scheduling-addon
Description:       Extend WP-Lister for eBay with custom schedule times per product and incremental bulk scheduling.
Version:           1.1
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

	// Enqueue style 
	wp_enqueue_style( 'wple-jqueryui-css', 'http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
	wp_enqueue_style( 'wple-datepicker-css', plugins_url( '/wple-scheduling-addon/css/jquery.datetimepicker.min.css') );
	
	// Enqueue js
	wp_enqueue_script( 'wple-jqueryui-js', 'http://code.jquery.com/ui/1.11.4/jquery-ui.js', null , array(), '1.0.0', true );
	wp_enqueue_script( 'wple-datepicker-js', plugins_url( '/js/jquery.datetimepicker.full.min.js', __FILE__ ), null , array(), '1.0.0', true );
	wp_enqueue_script( 'wple-custombulk-js', plugins_url( '/js/customBulkAction.js', __FILE__ ) );

}

add_action( 'plugins_loaded', 'wple_scheduling_init', 9 ); // saving settings require registering actions early
// add_action( 'init', 'wple_scheduling_init' );

/**
 *	Add custom bulk action to listing's action select box - Bulk Schedule
 */
add_action( 'admin_footer', 'modifiyListingsBulkAction' );

function modifiyListingsBulkAction() {
	include plugin_dir_path( __FILE__ ) . '/views/bulkschedule_dialog.php';
}

/**
 *	Bulk Schedule Handler
 */
add_action( "wp_loaded", 'handle_BulkSchedule' );

function handle_BulkSchedule() {
	WPLE_Custom_Schedules::bulk_schedule( $_REQUEST['auction'] );
}
