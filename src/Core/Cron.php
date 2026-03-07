<?php

/**
 * Cron class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * Cron class
 */
class Cron
{
	/**
	 * Registers custom intervals for Cron
	 *
	 * @filter cron_schedules
	 *
	 * @param array<mixed> $intervals intervals.
	 * @return array<mixed>
	 * @since  5.1.5
	 */
	public function registerIntervals($intervals)
	{
		$intervals['ntfn_2days'] = [
			'interval' => 2 * DAY_IN_SECONDS,
			'display' => __('Every two days', 'notification'),
		];

		$intervals['ntfn_3days'] = [
			'interval' => 3 * DAY_IN_SECONDS,
			'display' => __('Every three days', 'notification'),
		];

		$intervals['ntfn_week'] = [
			'interval' => WEEK_IN_SECONDS,
			'display' => __('Every week', 'notification'),
		];

		$intervals['ntfn_2weeks'] = [
			'interval' => 2 * WEEK_IN_SECONDS,
			'display' => __('Every two weeks', 'notification'),
		];

		$intervals['ntfn_month'] = [
			'interval' => MONTH_IN_SECONDS,
			'display' => __('Every month', 'notification'),
		];

		return $intervals;
	}

	/**
	 * Registers and reschedules the check updates event
	 *
	 * @action admin_init
	 *
	 * @return void
	 * @since  5.1.5
	 */
	public function registerCheckUpdatesEvent()
	{
		$event = wp_get_schedule('notification_check_wordpress_updates');
		$schedule = \Notification::settings()->getSetting('triggers/wordpress/updates_cron_period');

		if (! is_string($schedule)) {
			$schedule = 'ntfn_week';
		}

		if ($event === false) {
			$this->schedule($schedule, 'notification_check_wordpress_updates');
		}

		// Reschedule to match new settings.
		if ($event === $schedule) {
			return;
		}

		$this->unschedule('notification_check_wordpress_updates');
		$this->schedule($schedule, 'notification_check_wordpress_updates');
	}

	/**
	 * Schedules the event
	 *
	 * @param string $schedule schedule name.
	 * @param string $eventName event name.
	 * @param bool $once if schedule only one.
	 * @return void
	 * @since  5.1.5
	 */
	public function schedule($schedule, $eventName, $once = false)
	{
		if ($once && wp_get_schedule($eventName) !== false) {
			return;
		}

		wp_schedule_event(
			time() + DAY_IN_SECONDS,
			$schedule,
			$eventName
		);
	}

	/**
	 * Unschedules the event
	 *
	 * @param string $eventName event name.
	 * @return void
	 * @since  5.1.5
	 */
	public function unschedule($eventName)
	{
		$timestamp = wp_next_scheduled($eventName);
		wp_unschedule_event($timestamp, $eventName);
	}

	/**
	 * Registers and schedules the license check event
	 *
	 * @action admin_init
	 *
	 * @return void
	 */
	public function registerLicenseCheckEvent()
	{
		if (wp_next_scheduled('notification_check_licenses') !== false) {
			return;
		}

		wp_schedule_event(time() + DAY_IN_SECONDS, 'daily', 'notification_check_licenses');
	}

	/**
	 * Handles the background license check
	 *
	 * @action notification_check_licenses
	 *
	 * @return void
	 */
	public function handleLicenseCheck()
	{
		if (!function_exists('is_plugin_active')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		/** @var \BracketSpace\Notification\Admin\Extensions $extensions */
		$extensions = \Notification::component('BracketSpace\Notification\Admin\Extensions');
		$rawExtensions = $extensions->getRawExtensions();

		foreach ($rawExtensions as $extension) {
			if (
				!is_array($extension) || !isset($extension['edd'], $extension['slug']) ||
				!is_array($extension['edd']) || !is_string($extension['slug']) ||
				!is_plugin_active($extension['slug'])
			) {
				continue;
			}

			$license = new License($extension);

			if (empty($license->getKey())) {
				continue;
			}

			// Clear transient cache for fresh check
			$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
			$cache = new Cache($driver, sprintf('notification_license_check_%s', $extension['slug']));
			$cache->delete();

			// The cooldown in check() ensures: if first extension fails,
			// all others sharing the same store URL skip instantly.
			$license->isValid();
		}
	}
}
