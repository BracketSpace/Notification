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
if ( isset( $un['notifications'] ) && 'true' === $un['notifications'] ) {
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" ); // WPCS: db call ok.
}

// remove settings.
if ( isset( $un['settings'] ) && 'true' === $un['settings'] ) {

	$settings_config = get_option( '_notification_settings_config' );

	foreach ( $settings_config as $section_slug => $section ) {
		delete_option( 'notification_' . $section_slug );
		delete_site_option( 'notification_' . $section_slug );
	}

	delete_option( '_notification_settings_config' );
	delete_option( '_notification_settings_hash' );

}

// remove licenses.
if ( isset( $un['licenses'] ) && 'true' === $un['licenses'] ) {

	$files            = new BracketSpace\Notification\Utils\Files( '', '', '' );
	$view             = new BracketSpace\Notification\Utils\View( $files );
	$extensions_class = new BracketSpace\Notification\Admin\Extensions( $view );

	$extensions_class->load_extensions();

	$premium_extensions = $extensions_class->premium_extensions;

	foreach ( $premium_extensions as $extension ) {
		$license = $extension['license'];
		if ( $license->is_valid() ) {
			$license->deactivate();
		}
	}

	delete_option( 'notification_licenses' );

}

// remove other things.
delete_option( 'notification_story_dismissed' );
