<?php

/**
 * Public API.
 *
 * @package notification
 */

declare(strict_types=1);

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Interfaces;

/**
 * Adapts Notification object
 * Default adapters are: WordPress || JSON
 *
 * @since  6.0.0
 * @throws \Exception If adapter wasn't found.
 * @param  string       $adapterName Adapter class name.
 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
 * @return \BracketSpace\Notification\Interfaces\Adaptable
 */
function notification_adapt( $adapterName, Notification $notification )
{

	if (class_exists($adapterName)) {
		$adapter = new $adapterName($notification);
	} elseif (class_exists('BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapterName)) {
		$adapterName = 'BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapterName;
		$adapter = new $adapterName($notification);
	} else {
		throw new \Exception(sprintf('Couldn\'t find %s adapter', $adapterName));
	}

	return $adapter;
}

/**
 * Adapts Notification from input data
 * Default adapters are: WordPress || JSON
 *
 * @since  6.0.0
 * @param  string $adapterName Adapter class name.
 * @param  mixed  $data         Input data needed by adapter.
 * @return \BracketSpace\Notification\Interfaces\Adaptable
 */
function notification_adapt_from( $adapterName, $data )
{
	$adapter = notification_adapt($adapterName, new Notification());
	return $adapter->read($data);
}

/**
 * Changes one adapter to another
 *
 * @since  6.0.0
 * @param  string               $newAdapterName Adapter class name.
 * @param \BracketSpace\Notification\Interfaces\Adaptable $adapter Adapter.
 * @return \BracketSpace\Notification\Interfaces\Adaptable
 */
function notification_swap_adapter( $newAdapterName, Interfaces\Adaptable $adapter )
{
	return notification_adapt($newAdapterName, $adapter->getNotification());
}

/**
 * Logs the message in database
 *
 * @since  6.0.0
 * @param  string $component Component nice name, like `Core` or `Any Plugin Name`.
 * @param  string $type      Log type, values: notification|error|warning.
 * @param  string $message   Log formatted message.
 * @return bool|\WP_Error
 */
function notification_log( $component, $type, $message )
{

	if ($type !== 'notification' && ! notification_get_setting('debugging/settings/error_log')) {
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
 * @param  array $data Notification data.
 * @return \WP_Error | true
 */
function notification( $data = [] )
{

	try {
		notification_add(new Notification(notification_convert_data($data)));
	} catch (\Throwable $e) {
		return new \WP_Error('notification_error', $e->getMessage());
	}

	return true;
}

/**
 * Adds Notification to Store
 *
 * @since  6.0.0
 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
 * @return void
 */
function notification_add( Notification $notification )
{
	Store\Notification::insert($notification->getHash(), $notification);
	do_action('notification/notification/registered', $notification);
}

/**
 * Converts the static data to Trigger and Carrier objects
 *
 * If no `trigger` nor `carriers` keys are available it does nothing.
 * If the data is already in form of objects it does nothing.
 *
 * @since  6.0.0
 * @param  array $data Notification static data.
 * @return array       Converted data.
 */
function notification_convert_data( $data = [] )
{

	// Trigger conversion.
	if (! empty($data['trigger']) && ! ( $data['trigger'] instanceof Interfaces\Triggerable )) {
		$data['trigger'] = Store\Trigger::get($data['trigger']);
	}

	// Carriers conversion.
	if (isset($data['carriers'])) {
		$carriers = [];

		foreach ($data['carriers'] as $carrierSlug => $carrierData) {
			if ($carrierData instanceof Interfaces\Sendable) {
				$carriers[$carrierSlug] = $carrierData;
				continue;
			}

			$registeredCarrier = Store\Carrier::get($carrierSlug);

			if (empty($registeredCarrier)) {
				continue;
			}

			$carrier = clone $registeredCarrier;
			$carrier->setData($carrierData);
			$carriers[$carrierSlug] = $carrier;
		}

		$data['carriers'] = $carriers;
	}

	return $data;
}

/**
 * Registers settings
 *
 * @since  5.0.0
 * @param  mixed   $callback Callback for settings registration, array of string.
 * @param int $priority Action priority.
 * @return void
 */
function notification_register_settings( $callback, $priority = 10 )
{

	if (! is_callable($callback)) {
		trigger_error('You have to pass callable while registering the settings', E_USER_ERROR);
	}

	add_action('notification/settings/register', $callback, $priority);
}

/**
 * Gets setting values
 *
 * @since 5.0.0
 * @return mixed
 */
function notification_get_settings()
{
	return \Notification::component('core_settings')->getSettings();
}

/**
 * Gets single setting value
 *
 * @since  5.0.0
 * @since  7.0.0 The `notifications` section has been changed to `carriers`.
 * @param  string $setting setting name in `a/b/c` format.
 * @return mixed
 */
function notification_get_setting( $setting )
{

	$parts = explode('/', $setting);

	if ($parts[0] === 'notifications') {
		_deprecated_argument(__FUNCTION__, '7.0.0', 'The `notifications` section has been changed to `carriers`, adjust the first part of the setting.');
		$parts[0] = 'carriers';
		$setting = implode('/', $parts);
	}

	return \Notification::component('core_settings')->getSetting($setting);
}

/**
 * Updates single setting value.
 *
 * @param   string $setting setting name in `a/b/c` format.
 * @param   mixed  $value setting value.
 * @return  mixed
 */
function notification_update_setting( $setting, $value )
{
	return \Notification::component('core_settings')->updateSetting($setting, $value);
}
