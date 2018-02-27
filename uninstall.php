<?php
/**
 * Plugin uninstallation file
 *
 * @package notification
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

$general_settings = get_option( 'notification_general' );

$un = $general_settings['uninstallation'];

// remove notifications.
if ( isset( $un['notifications'] ) && $un['notifications'] == 'true' ) {
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" );
}

// remove settings.
if ( isset( $un['settings'] ) && $un['settings'] == 'true' ) {

	$settings_config = get_option( '_notification_settings_config' );

	foreach ( $settings_config as $section_slug => $section ) {
		delete_option( 'notification_' . $section_slug );
		delete_site_option( 'notification_' . $section_slug );
	}

	delete_option( '_notification_settings_config' );
	delete_option( '_notification_settings_hash' );

}
