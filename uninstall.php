<?php

/**
 * Uninstall plugin file
 *
 * @package notification
 */

declare(strict_types=1);

/**
 * Load the vendor autoload.
 */
require_once __DIR__ . '/vendor/autoload.php';

global $wpdb;

$generalSettings = get_option('notification_general');

$un = $generalSettings['uninstallation'];

// Remove notifications.
if (isset($un['notifications']) && $un['notifications'] === 'true') {
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" ); // phpcs:ignore
}

// Remove settings.
if (isset($un['settings']) && $un['settings'] === 'true') {
	$settingsConfig = get_option('_transient_notification_settings_config');

	foreach ($settingsConfig as $sectionSlug => $section) {
		delete_option('notification_' . $sectionSlug);
		delete_site_option('notification_' . $sectionSlug);
	}

	delete_option('_notification_settings_config');
	delete_option('_notification_settings_hash');
}

// Remove licenses.
if (isset($un['licenses']) && $un['licenses'] === 'true') {
	$extensionsClass = new BracketSpace\Notification\Admin\Extensions();

	$extensionsClass->loadExtensions();
	$premiumExtensions = $extensionsClass->premiumExtensions;

	foreach ($premiumExtensions as $extension) {
		$license = $extension['license'];
		if (!$license->isValid()) {
			continue;
		}

		$license->deactivate();
	}

	delete_option('notification_licenses');
}

// Remove logs table.
$logsTable = $wpdb->prefix . 'notification_logs';
$wpdb->query( "DROP TABLE IF EXISTS ${logsTable}"  ); // phpcs:ignore

// Remove other things.
delete_option('notification_story_dismissed');
delete_option('notification_wizard_dismissed');
delete_option('notification_debug_log');
delete_option('notification_data_version');
delete_option('notification_db_version');
