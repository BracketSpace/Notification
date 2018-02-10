<?php
/**
 * Plugin Name: Notification
 * Description: Send notifications about various events in WordPress. You can also create your custom triggers for any action.
 * Plugin URI: https://notification.underdev.it
 * Author: underDEV
 * Author URI: https://underdev.it
 * Version: 5.0.0
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

	if ( array_shift( $parts ) != 'underDEV' ) {
		return false;
	}

	if ( array_shift( $parts ) != 'Notification' ) {
		return false;
	}

	$file = trailingslashit( 'class' ) . implode( '/', $parts ) . '.php';

	require_once $file ;

}
spl_autoload_register( 'notification_autoload' );

/**
 * Requirements check
 */
$requirements = new underDEV\Notification\Utils\Requirements( __( 'Notification', 'notification' ), array(
	'php'                => '5.3',
	'wp'                 => '4.6',
	'function_collision' => array( 'register_trigger', 'register_notification' ),
) );

if ( ! $requirements->satisfied() ) {
	add_action( 'admin_notices', array( $requirements, 'notice' ) );
	return;
}

/**
 * Boots up the plugin
 *
 * @return object Runtime class instance
 */
function notification_runtime() {

	global $notification_runtime;

	if ( empty( $notification_runtime ) ) {
		$notification_runtime = new underDEV\Notification\Runtime( __FILE__ );
	}

	return $notification_runtime;

}

notification_runtime();
