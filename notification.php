<?php
/*
Plugin Name: Notification
Description: Create custom triggers and send email notifications
Author: underDEV
Author URI: https://www.wpart.co
Version: 1.2
License: GPL3
Text Domain: notification
Domain Path: /languages
*/

use \Notification\Notifications;

if ( ! defined( 'NOTIFICATION_URL' ) ) {
	define( 'NOTIFICATION_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'NOTIFICATION_DIR' ) ) {
	define( 'NOTIFICATION_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Plugin's autoload function
 * @param  string $class class name
 * @return mixed         false if not plugin's class or void
 */
function notification_autoload( $class ) {

	$parts = explode( '\\', $class );

	if ( array_shift( $parts ) != 'Notification' ) {
		return false;
	}

	// Recipients
	if ( $parts[0] == 'Recipients' ) {
		$file = NOTIFICATION_DIR . strtolower( implode( '/', $parts ) ) . '.php';
	} else { // Other classes
		$file = NOTIFICATION_DIR . trailingslashit( 'inc' ) . strtolower( implode( '/', $parts ) ) . '.php';
	}

	if ( file_exists( $file ) ) {
		require_once( $file );
	}

}
spl_autoload_register( 'notification_autoload' );

/**
 * Setup plugin
 * @return void
 */
function notification_plugin_setup() {

	load_plugin_textdomain( 'notification', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}
add_action( 'plugins_loaded', 'notification_plugin_setup' );

/**
 * Initialize plugin
 * @return void
 */
function notification_initialize() {

	/**
	 * Global functions
	 */
	require_once( NOTIFICATION_DIR . trailingslashit( 'inc' ) . 'global.php' );

	/**
	 * Notifications instance
	 */
	new Notifications();

	/**
	 * Load some default triggers
	 */
	require_once( NOTIFICATION_DIR . trailingslashit( 'triggers' ) . 'triggers.php' );

	/**
	 * Load default recipients
	 */
	require_once( NOTIFICATION_DIR . trailingslashit( 'recipients' ) . 'recipients.php' );

}
add_action( 'init', 'notification_initialize', 5 );
