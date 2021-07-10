<?php
/**
 * PHPUnit bootstrap file
 *
 * @package notification
 */

define( 'NOTIFICATION_DOING_TESTS', true );
define( 'NOTIFICATION_DEBUG', true );

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Give access to tests_add_filter() function.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

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
tests_add_filter( 'notification/load/default/resolvers', '__return_false' );

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
