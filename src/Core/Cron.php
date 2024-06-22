<?php

/**
 * Cron class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\ErrorHandler;

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
			ErrorHandler::error('Update cron period is not a string');
			return;
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
}
