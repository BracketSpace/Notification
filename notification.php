<?php
/**
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 6.2.0
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 *
 * @package notification
 */

define( 'NOTIFICATION_VERSION', '6.1.6' );

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * Requirements check
 */
$requirements = new BracketSpace\Notification\Utils\Requirements( __( 'Notification', 'notification' ), [
	'php'                => '7.0',
	'wp'                 => '5.2',
	'function_collision' => [ 'notification' ],
] );

if ( ! $requirements->satisfied() ) {
	add_action( 'admin_notices', [ $requirements, 'notice' ] );
	return;
}

global $notification_runtime;

/**
 * Gets the plugin runtime.
 *
 * @param string $property Optional property to get.
 * @return object Runtime class instance
 */
function notification_runtime( $property = null ) {

	global $notification_runtime;

	if ( empty( $notification_runtime ) ) {
		$notification_runtime = new BracketSpace\Notification\Runtime( __FILE__ );
	}

	if ( null !== $property && isset( $notification_runtime->{ $property } ) ) {
		return $notification_runtime->{ $property };
	}

	return $notification_runtime;

}

add_action( 'plugins_loaded', function() {
	$runtime = notification_runtime();
	$runtime->boot();
}, 5 );
