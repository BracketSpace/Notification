<?php

/**
 * Public API.
 *
 * @package notification
 *
 * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName
 */

declare(strict_types=1);

namespace BracketSpace\Notification;

use BracketSpace\Notification\Core\Notification;

/**
 * Logs the message in database
 *
 * @param string $component Component nice name, like `Core` or `Any Plugin Name`.
 * @param string $type Log type, values: notification|error|warning.
 * @param string $message Log formatted message.
 * @return bool|\WP_Error
 * @since  6.0.0
 * @since [Next] Function lives under BracketSpace\Notifiation namespace.
 */
function log($component, $type, $message)
{
	if ($type !== 'notification' && !getSetting('debugging/settings/error_log')) {
		return false;
	}

	$debugger = \Notification::component('core_debugging');

	$logData = [
		'component' => $component,
		'type' => $type,
		'message' => $message,
	];

	try {
		return $debugger->addLog($logData);
	} catch (\Throwable $e) {
		return new \WP_Error('wrong_log_data', $e->getMessage());
	}
}

/**
 * Creates new Notification from array
 *
 * Accepts both array with Trigger and Carriers objects or static values.
 *
 * @since  6.0.0
 * @since [Next] Function lives under BracketSpace\Notifiation namespace.
 * @param NotificationUnconvertedData $data Notification data.
 * @return \WP_Error | true
 */
function notification($data = [])
{
	try {
		Register::notification(Notification::from('array', $data));
	} catch (\Throwable $e) {
		return new \WP_Error('notification_error', $e->getMessage());
	}

	return true;
}

/**
 * Registers settings
 *
 * @param mixed $callback Callback for settings registration, array of string.
 * @param int $priority Action priority.
 * @return void
 * @since  6.0.0
 * @since [Next] Function lives under BracketSpace\Notifiation namespace.
 */
function registerSettings($callback, $priority = 10)
{
	if (!is_callable($callback)) {
		trigger_error('You have to pass callable while registering the settings', E_USER_ERROR);
	}

	add_action('notification/settings/register', $callback, $priority);
}

/**
 * Gets setting values
 *
 * @return mixed
 * @since  6.0.0
 * @since [Next] Function lives under BracketSpace\Notifiation namespace.
 */
function getSettings()
{
	return \Notification::component('core_settings')->getSettings();
}

/**
 * Gets single setting value
 *
 * @param string $setting setting name in `a/b/c` format.
 * @return mixed
 * @since  6.0.0
 * @since [Next] Function lives under BracketSpace\Notifiation namespace.
 */
function getSetting($setting)
{
	return \Notification::component('core_settings')->getSetting($setting);
}

/**
 * Updates single setting value.
 *
 * @param string $setting setting name in `a/b/c` format.
 * @param mixed $value setting value.
 * @return  mixed
 * @since  6.0.0
 * @since [Next] Function lives under BracketSpace\Notifiation namespace.
 */
function updateSetting($setting, $value)
{
	return \Notification::component('core_settings')->updateSetting(
		$setting,
		$value
	);
}
