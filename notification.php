<?php
/**
 * Notification
 *
 * @package notification
 *
 * @wordpress-plugin
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 7.2.4
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 */

use BracketSpace\Notification\Plugin;

if ( ! defined( 'NOTIFICATION_VERSION' ) ) {
	define( 'NOTIFICATION_VERSION', '7.2.4' );
}

// Load autoloader.
if ( ! class_exists( Plugin::class ) && is_file( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Start the plugin.
add_action( 'init', 'BracketSpace\\Notification\\boot', 5, 0 );
