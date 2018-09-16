<?php
/**
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 5.2.3
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

	if ( array_shift( $parts ) !== 'BracketSpace' ) {
		return false;
	}

	if ( array_shift( $parts ) !== 'Notification' ) {
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
$requirements = new BracketSpace\Notification\Utils\Requirements(
	__( 'Notification', 'notification' ),
	array(
		'php'                => '5.3.9',
		'wp'                 => '4.6',
		'function_collision' => array( 'register_trigger', 'register_notification' ),
	)
);

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

/**
 * Create a helper function for easy SDK access.
 *
 * @since  5.2.3
 * @return object
 */
function not_fs() {
	global $not_fs;

	if ( ! isset( $not_fs ) ) {
		// Include Freemius SDK.
		require_once dirname( __FILE__ ) . '/freemius/start.php';

		$not_fs = fs_dynamic_init(
			array(
				'id'             => '1823',
				'slug'           => 'notification',
				'type'           => 'plugin',
				'public_key'     => 'pk_bf7bb6cbc0cd51e14cd186e9620de',
				'is_premium'     => false,
				'has_addons'     => false,
				'has_paid_plans' => false,
				'menu'           => array(
					'first-path' => 'plugins.php',
					'account'    => false,
					'contact'    => false,
					'support'    => false,
				),
			)
		);
	}

	return $not_fs;
}

// Init Freemius.
not_fs();
// Signal that SDK was initiated.
do_action( 'not_fs_loaded' );
