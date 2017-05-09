<?php
/*
Plugin Name: Notification
Description: Send email notifications about various events in WordPress. You can also create your custom triggers for any action.
Plugin URI: https://notification.underdev.it
Author: underDEV
Author URI: https://underdev.it
Version: 3.0
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
require_once( 'vendor/autoload.php' );

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

/**
 * Do some check on plugin activation
 * @return void
 */
function notification_activation() {

	if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		wp_die( __( 'This plugin requires PHP in version at least 5.3. WordPress itself <a href="https://wordpress.org/about/requirements/" target="_blank">requires at least PHP 5.6</a>. Please upgrade your PHP version or contact your Server administrator.', 'notification' ) );

	}

}
register_activation_hook( __FILE__, 'notification_activation' );
