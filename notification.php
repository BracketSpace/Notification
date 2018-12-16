<?php
/**
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 5.3.2
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 *
 * @package notification
 */

define( 'NOTIFICATION_VERSION', '5.3.2' );

/**
 * Plugin's autoload function
 *
 * @param  string $class class name.
 * @return mixed         false if not plugin's class or void
 */
function notification_autoload( $class ) {

	$parts      = explode( '\\', $class );
	$namespaces = array( 'BracketSpace', 'Notification' );

	foreach ( $namespaces as $namespace ) {
		if ( array_shift( $parts ) !== $namespace ) {
			return false;
		}
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
		'php'                => '5.6',
		'wp'                 => '4.9',
		'function_collision' => array( 'register_trigger', 'register_notification' ),
		'dochooks'           => true,
	)
);

/**
 * Check if ReflectionObject returns proper docblock comments for methods.
 */
if ( method_exists( $requirements, 'add_check' ) ) {
	$requirements->add_check(
		'dochooks',
		function( $comparsion, $r ) {
			if ( true !== $comparsion ) {
				return;
			}

			/**
			 * NotificationDocHookTest class
			 */
			class NotificationDocHookTest {
				/**
				 * Test method
				 *
				 * @action test 10
				 * @return void
				 */
				public function test_method() {}
			}

			$reflector = new \ReflectionObject( new NotificationDocHookTest() );
			foreach ( $reflector->getMethods() as $method ) {
				$doc = $method->getDocComment();
				if ( false === strpos( $doc, '@action' ) ) {
					$r->add_error( __( 'PHP OP Cache to be disabled', 'notification' ) );
				}
			}

		}
	);
}

if ( ! $requirements->satisfied() ) {
	add_action( 'admin_notices', array( $requirements, 'notice' ) );
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

$runtime = notification_runtime();
$runtime->boot();

/**
 * Create a helper function for easy SDK access.
 *
 * @since  5.2.3
 * @return object
 */
function notification_freemius() {
	global $notification_freemius;

	if ( ! isset( $notification_freemius ) ) {
		// Include Freemius SDK.
		require_once dirname( __FILE__ ) . '/freemius/start.php';

		$notification_freemius = fs_dynamic_init( array(
			'id'             => '1823',
			'slug'           => 'notification',
			'type'           => 'plugin',
			'public_key'     => 'pk_bf7bb6cbc0cd51e14cd186e9620de',
			'is_premium'     => false,
			'has_addons'     => false,
			'has_paid_plans' => false,
			'menu'           => array(
				'slug'    => 'edit.php?post_type=notification',
				'account' => false,
				'contact' => false,
				'support' => false,
			),
		) );
	}

	return $notification_freemius;
}

// Init Freemius.
notification_freemius();
// Signal that SDK was initiated.
do_action( 'notification_freemius_loaded' );

// Uninstallation.
notification_freemius()->add_action( 'after_uninstall', function() {

	global $wpdb;

	$general_settings = get_option( 'notification_general' );

	$un = $general_settings['uninstallation'];

	// Remove notifications.
	if ( isset( $un['notifications'] ) && 'true' === $un['notifications'] ) {
		$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" ); // phpcs:ignore
	}

	// Remove settings.
	if ( isset( $un['settings'] ) && 'true' === $un['settings'] ) {

		$settings_config = get_option( '_notification_settings_config' );

		foreach ( $settings_config as $section_slug => $section ) {
			delete_option( 'notification_' . $section_slug );
			delete_site_option( 'notification_' . $section_slug );
		}

		delete_option( '_notification_settings_config' );
		delete_option( '_notification_settings_hash' );

	}

	// Remove licenses.
	if ( isset( $un['licenses'] ) && 'true' === $un['licenses'] ) {

		$files            = new BracketSpace\Notification\Utils\Files( '', '', '' );
		$view             = new BracketSpace\Notification\Utils\View( $files );
		$extensions_class = new BracketSpace\Notification\Admin\Extensions( $view );

		$extensions_class->load_extensions();

		$premium_extensions = $extensions_class->premium_extensions;

		foreach ( $premium_extensions as $extension ) {
			$license = $extension['license'];
			if ( $license->is_valid() ) {
				$license->deactivate();
			}
		}

		delete_option( 'notification_licenses' );

	}

	// Remove other things.
	delete_option( 'notification_story_dismissed' );
	delete_option( 'notification_debug_log' );

} );
