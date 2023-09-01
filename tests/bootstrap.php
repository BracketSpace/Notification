<?php
/**
 * PHPUnit bootstrap file
 *
 * @package notification
 */

define('NOTIFICATION_DOING_TESTS', true);
define('NOTIFICATION_DEBUG', true);
define('DOING_TESTS', true);

$_tests_dir = getenv('WP_TESTS_DIR');

if (! $_tests_dir) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv('WP_TESTS_PHPUNIT_POLYFILLS_PATH');
if (false !== $_phpunit_polyfills_path) {
    define('WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path);
}

if (! file_exists("{$_tests_dir}/includes/functions.php")) {
    echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
    exit(1);
}

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
tests_add_filter( 'muplugins_loaded', function() {
	require dirname( __DIR__ ) . '/notification.php';
} );

/**
 * Disable plugin defaults.
 */
tests_add_filter( 'notification/load/default/recipients', '__return_false' );
tests_add_filter( 'notification/load/default/carriers', '__return_false' );
tests_add_filter( 'notification/load/default/resolvers', '__return_false' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";
