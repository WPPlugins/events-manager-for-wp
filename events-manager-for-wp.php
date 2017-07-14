<?php
/*
	Plugin Name: Events Manager for WordPress
	Plugin URI: https://github.com/forsitemedia/events-manager-for-wp/
	Description: Events Manager for WordPress
	Version: 1.2.6
	Author: Forsite Media
	Author URI: https://forsite.media/
	License: GPLv2 or later
	Text Domain: events-manager-for-wp
	Domain Path: /languages
*/

/*

Plugins code is loosly based on BE Events Calendar (c) 2014, Bill Erickson <bill@billerickson.net>.
https://github.com/billerickson/BE-Events-Calendar

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

// Bail out if file called directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Whatcha doin?' );
}

// Define plugin constants
define( 'EM4WP_EVENTS_CALENDAR_VERSION', '1.2.6' );
define( 'EM4WP_EVENTS_CALENDAR_DIR', plugin_dir_path( __FILE__ ) );
define( 'EM4WP_EVENTS_CALENDAR_URL', plugin_dir_url( __FILE__ ) );
define( 'EM4WP_EVENTS_CALENDAR_PLUGIN_FILE', __FILE__ );

/**
 * 
 * Load the text domain for translation of the plugin
 *
 * @since 1.2.2
 * 
 */
load_plugin_textdomain( 'events-manager-for-wp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param  string  $class  The class being instantiated
 */
function autoload_em4wp( $class ) {

	// Bail out if not loading a Media Manager class
	if ( 'EM4WP_' != substr( $class, 0, 6 ) || class_exists( $class ) ) {
		return;
	}

	// Convert from the class name, to the classes file name
	$file_data = strtolower( $class );
	$file_data = str_replace( '_', '-', $file_data );
	$file_name = 'class-' . $file_data . '.php';

	// Get the classes file path
	$dir = dirname( __FILE__ );
	$path = $dir . '/inc/' . $file_name;

	// Include the class (spl_autoload_register will automatically instantiate it for us)
	require( $path );
}
spl_autoload_register( 'autoload_em4wp' );

new EM4WP_Events_Calendar;
new EM4WP_Recurring_Events;
new EM4WP_Genesis_Schema;
new EM4WP_Events_Calendar_View;
new EM4WP_Upcoming_Events;
new EM4WP_Locations;
new EM4WP_Events_Archive;
new EM4WP_Frontend;
new EM4WP_Event_Type;
new EM4WP_Read_More;
new EM4WP_Settings;
