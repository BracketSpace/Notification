<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

require_once( 'inc/singleton.php' );
require_once( 'inc/settings/section.php' );
require_once( 'inc/settings/group.php' );
require_once( 'inc/settings/field.php' );
require_once( 'inc/settings/corefields.php' );
require_once( 'inc/settings.php' );

Notification\Settings::get()->register_settings();
$settings = Notification\Settings::get()->get_settings();

file_put_contents( dirname( __FILE__ ) . '/log', print_r( $settings, true ) . "\r\n\r\n", FILE_APPEND );

if ( isset( $settings['general']['uninstallation'] ) ) {

	$un = $settings['general']['uninstallation'];

	// remove notifications

	if ( isset( $un['notifications'] ) && $un['notifications'] == 'true' ) {
		$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" );
	}

	// remove settings

	if ( isset( $un['settings'] ) && $un['settings'] == 'true' ) {

		$sections = Notification\Settings::get()->get_sections();

		foreach ( $sections as $section_slug => $section ) {
			delete_option( 'notification_' . $section_slug );
			delete_site_option( 'notification_' . $section_slug );
		}

	}

}
