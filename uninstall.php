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

// Ensure settings are valid before proceeding
if (
	!is_array($generalSettings) ||
	!isset($generalSettings['uninstallation']) ||
	!is_array($generalSettings['uninstallation'])
) {
	return;
}

$un = $generalSettings['uninstallation'];

// Remove notifications.
if (isset($un['notifications']) && $un['notifications'] === 'true') {
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" ); // phpcs:ignore
}

// Remove settings.
if (isset($un['settings']) && $un['settings'] === 'true') {
	$settingsConfig = get_option('_transient_notification_settings_config');

	if (is_array($settingsConfig)) {
		foreach ($settingsConfig as $sectionSlug => $section) {
			delete_option('notification_' . $sectionSlug);
			delete_site_option('notification_' . $sectionSlug);
		}
	}

	delete_option('_notification_settings_config');
	delete_option('_notification_settings_hash');
}

// Remove licenses.
if (isset($un['licenses']) && $un['licenses'] === 'true') {
	$extensionsClass = new BracketSpace\Notification\Admin\Extensions();

	$extensionsClass->loadExtensions();
	$premiumExtensions = $extensionsClass->premiumExtensions;

	if (!is_array($premiumExtensions)) {
		$premiumExtensions = [];
	}

	foreach ($premiumExtensions as $extension) {
		if (!is_array($extension) || !isset($extension['license'])) {
			continue;
		}

		$license = $extension['license'];
		if (!is_object($license) || !method_exists($license, 'isValid') || !$license->isValid()) {
			continue;
		}

		if (!method_exists($license, 'deactivate')) {
			continue;
		}

		$license->deactivate();
	}

	delete_option('notification_licenses');
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'notification_license_failed_http_%'"); // phpcs:ignore
	$timestamp = wp_next_scheduled('notification_check_licenses');
	if ($timestamp) {
		wp_unschedule_event($timestamp, 'notification_check_licenses');
	}
}

// Remove tables.
$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $wpdb->prefix . 'notification_logs')); // phpcs:ignore
$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $wpdb->prefix . 'notifications')); // phpcs:ignore
$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $wpdb->prefix . 'notification_carriers')); // phpcs:ignore
$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $wpdb->prefix . 'notification_extras')); // phpcs:ignore

// Remove other things.
delete_option('notification_story_dismissed');
delete_option('notification_wizard_dismissed');
delete_option('notification_debug_log');
delete_option('notification_data_version');
delete_option('notification_db_version');
