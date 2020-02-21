<?php
/**
 * Load file
 *
 * @package notification
 */

/**
 * Load the main plugin file.
 */
require_once __DIR__ . '/notification.php';

/**
 * Initialize early.
 */
add_action( 'init', function() {
	Notification::init( __FILE__ )->init();
}, 4 );
