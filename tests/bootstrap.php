<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Sample_Plugin
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/notification.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

/**
 * Plugin's autoload function for test files
 *
 * @param  string $class class name.
 * @return mixed         false if not plugin's class or void
 */
function notification_tests_autoload( $class ) {

	$parts      = explode( '\\', $class );
	$namespaces = array( 'BracketSpace', 'Notification', 'Tests' );

	foreach ( $namespaces as $namespace ) {
		if ( array_shift( $parts ) !== $namespace ) {
			return false;
		}
	}

	$file = trailingslashit( dirname( __FILE__ ) ) . implode( '/', $parts ) . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}

}
spl_autoload_register( 'notification_tests_autoload' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
