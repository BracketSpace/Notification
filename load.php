<?php
/**
 * This file should be used only for loading Notification plugin in other plugin or theme
 * Just include this file to get it work, it will figure out if it's loaded from theme or from plugin
 *
 * @package notification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Directory
 */

if ( ! defined( 'NOTIFICATION_DIR' ) ) {
	define( 'NOTIFICATION_DIR', trailingslashit( dirname( __FILE__ ) ) );
}

/**
 * URL
 */

$theme_url = wp_parse_url( get_stylesheet_directory_uri() );
$theme_pos = strpos( NOTIFICATION_DIR, $theme_url['path'] );

// Notification loaded from theme.
if ( false !== $theme_pos ) {

	$plugin_relative_dir = str_replace( $theme_url['path'], '', substr( NOTIFICATION_DIR, $theme_pos ) );
	$url                 = $theme_url['scheme'] . '://' . $theme_url['host'] . $theme_url['path'] . $plugin_relative_dir;

	if ( ! defined( 'NOTIFICATION_URL' ) ) {
		define( 'NOTIFICATION_URL', $url );
	}
} else { // Notification loaded from plugin.

	$plugin_url = trailingslashit( plugins_url( '', __FILE__ ) );

	if ( ! defined( 'NOTIFICATION_URL' ) ) {
		define( 'NOTIFICATION_URL', $plugin_url );
	}
}

/**
 * Load
 */

if ( defined( 'NOTIFICATION_URL' ) && ! function_exists( 'notification_runtime' ) ) {

	if ( ! defined( 'NOTIFICATION_AS_BUNDLE' ) ) {
		define( 'NOTIFICATION_AS_BUNDLE', true );
	}

	require_once NOTIFICATION_DIR . 'notification.php';

} else {
	trigger_error( 'Notification plugin tried to load itself as a bundled package but it couldn\'t. Make sure the Notification plugin is not active.', E_USER_WARNING );
}
