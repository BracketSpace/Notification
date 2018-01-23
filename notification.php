<?php
/*
Plugin Name: Notification
Description: Send email notifications about various events in WordPress. You can also create your custom triggers for any action.
Plugin URI: https://notification.underdev.it
Author: underDEV
Author URI: https://underdev.it
Version: 3.2.0
License: GPL3
Text Domain: notification
Domain Path: /languages
*/

if ( ! defined( 'NOTIFICATION_URL' ) ) {
	define( 'NOTIFICATION_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'NOTIFICATION_DIR' ) ) {
	define( 'NOTIFICATION_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Composer autoload
 */
require_once( NOTIFICATION_DIR . '/vendor/autoload.php' );

/**
 * Check minimum requirements of the plugin
 * @param string $php_ver The minimum PHP version.
 * @param string $wp_ver  The minimum WP version.
 * @param string $name    The name of the theme/plugin to check.
 * @param array  $plugins Required plugins format plugin_path/plugin_name.
 */
$requirements = new Minimum_Requirements( '5.3', '3.6', __( 'Notification', 'notification' ), array() );

/**
 * Check compatibility on activation
 */
register_activation_hook( __FILE__, array( $requirements, 'check_compatibility_on_install' ) );

/**
 * If it is already installed and activated check if example new version is compatible,
 * if is not don't load plugin code and print admin_notice
 */
if ( ! $requirements->is_compatible_version() ) {
	add_action( 'admin_notices', array( $requirements, 'load_plugin_admin_notices' ) );
	return;
}

/**
 * Setup plugin
 * @return void
 */
function notification_plugin_setup() {

	load_plugin_textdomain( 'notification', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}
add_action( 'plugins_loaded', 'notification_plugin_setup' );

/**
 * Initialize plugin
 * @return void
 */
function notification_initialize() {

	/**
	 * Notifications instance
	 */
	underDEV\Notification\Notifications::get();

	/**
	 * Settings instance
	 */
	underDEV\Notification\Settings::get();

}
add_action( 'init', 'notification_initialize', 5 );

/**
 * Initialize default triggers and recipients
 * @return void
 */
function notification_default_triggers_recipients_initialization() {

	/**
	 * Load some default triggers
	 */
	require_once( NOTIFICATION_DIR . trailingslashit( 'triggers' ) . 'triggers.php' );

	/**
	 * Load default recipients
	 */
	require_once( NOTIFICATION_DIR . trailingslashit( 'recipients' ) . 'recipients.php' );

}
add_action( 'init', 'notification_default_triggers_recipients_initialization', 9 );

/**
 * Initialize plugin on admin side
 * @return void
 */
function notification_admin_initialize() {

	/**
	 * Admin instance
	 */
	underDEV\Notification\Admin::get();

	/**
	 * Extensions
	 */
	underDEV\Notification\Extensions::get();

}
add_action( 'init', 'notification_admin_initialize', 5 );

/**
 * Initialize Upgrader
 * @return void
 */
function notification_upgrade() {

	/**
	 * Upgrade instance
	 */
	underDEV\Notification\Upgrade::get();

}
add_action( 'admin_init', 'notification_upgrade', 5 );
