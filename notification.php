<?php
/**
 * Plugin Name: Notification
 * Description: Send notifications about various events in WordPress. You can also create your custom triggers for any action.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 5.1.0
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 *
 * @package notification
 */

/**
 * Plugin's autoload function
 *
 * @param  string $class class name.
 * @return mixed         false if not plugin's class or void
 */
function notification_autoload( $class ) {

	$parts = explode( '\\', $class );

	if ( array_shift( $parts ) != 'BracketSpace' ) {
		return false;
	}

	if ( array_shift( $parts ) != 'Notification' ) {
		return false;
	}

	$file = trailingslashit( dirname( __FILE__ ) ) . trailingslashit( 'class' ) . implode( '/', $parts ) . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}

}
spl_autoload_register( 'notification_autoload' );

/**
 * Requirements check
 */
$requirements = new BracketSpace\Notification\Utils\Requirements( __( 'Notification', 'notification' ), array(
	'php'                => '5.3',
	'wp'                 => '4.6',
	'function_collision' => array( 'register_trigger', 'register_notification' ),
) );

if ( ! $requirements->satisfied() ) {
	add_action( 'admin_notices', array( $requirements, 'notice' ) );
	return;
}

global $notification_runtime;

/**
 * Boots up the plugin
 *
 * @return object Runtime class instance
 */
function notification_runtime() {

	global $notification_runtime;

	if ( empty( $notification_runtime ) ) {
		$notification_runtime = new BracketSpace\Notification\Runtime( __FILE__ );
	}

	return $notification_runtime;

}

$runtime = notification_runtime();
$runtime->boot();
