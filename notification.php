<?php
/*
Plugin Name: Notification
Description: Send notifications about various events in WordPress. You can also create your custom triggers for any action.
Plugin URI: https://notification.underdev.it
Author: underDEV
Author URI: https://underdev.it
Version: 4.0.0
License: GPL3
Text Domain: notification
Domain Path: /languages
*/

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

	if ( array_shift( $parts ) != 'underDEV' ) {
		return false;
	}

	if ( array_shift( $parts ) != 'Notification' ) {
		return false;
	}

	$file = NOTIFICATION_DIR . trailingslashit( 'inc' ) . implode( '/', $parts ) . '.php';

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
