<?php

/**
 * Processor class
 * Responsible for running Triggers and executing Carriers.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver\Transient;
use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Processor class
 */
class Processor
{
	use Casegnostic;

	/**
	 * Processes the Queue
	 *
	 * @action shutdown
	 *
	 * @return void
	 * @since  8.0.0
	 */
	public function processQueue()
	{
		Queue::iterate(
			static function ($index, $notification, $trigger) {

				$bpEnabled = apply_filters(
					'notification/trigger/process_in_background',
					\Notification::component('settings')->getSetting('general/advanced/background_processing'),
					$trigger
				);

				// If Background Processing is enabled we load the execution to Cron and stop processing.
				if ($bpEnabled) {
					self::schedule($notification, $trigger);
					return;
				}

				self::processNotification($notification, $trigger);
			}
		);
	}

	/**
	 * Scheduled the Notification submission in Cron.
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return void
	 * @since  8.0.0
	 */
	public static function schedule(Notification $notification, Triggerable $trigger)
	{
		// Optimize Trigger.
		$trigger->clearMergeTags();

		/**
		 * Identifies the Trigger by its values. This serves two purposes:
		 * 1. We create the param cache key out of the id
		 * 2. Based on that ID we can detect duplicates
		 *
		 * By default all Trigger props are taken and whole trigger is unique.
		 * This can be overridden by specific extensions.
		 */
		$triggerKey = sprintf(
			'%s_%s_%s',
			$notification->getHash(),
			$trigger->getSlug(),
			apply_filters(
				'notification/background_processing/trigger_key',
				md5((string)wp_json_encode($trigger)),
				$trigger
			)
		);

		// Cache trigger params.
		self::getCache($triggerKey)->set($trigger);

		$result = wp_schedule_single_event(
			time() + apply_filters('notification/background_processing/delay', 30),
			'notification_background_processing',
			[
				$notification->to('json', ['jsonOptions' => JSON_UNESCAPED_UNICODE]),
				$triggerKey,
			]
		);
	}

	/**
	 * Dispatches all the Carriers attached for Notification.
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return void
	 * @since  8.0.0
	 */
	public static function processNotification(Notification $notification, Triggerable $trigger)
	{
		$trigger->setupMergeTags();

		if (!apply_filters('notification/should_send', true, $notification, $trigger)) {
			return;
		}

		foreach ($notification->getEnabledCarriers() as $carrier) {
			/** @var \BracketSpace\Notification\Abstracts\Carrier $carrier */
			$carrier->resolveFields($trigger);
			$carrier->prepareData();

			do_action('notification/carrier/pre-send', $carrier, $trigger, $notification);

			if ($carrier->isSuppressed()) {
				continue;
			}

			self::send($carrier, $trigger);
		}

		do_action('notification/sent', $notification, $trigger);
	}

	/**
	 * Handles cron request
	 *
	 * @action notification_background_processing
	 *
	 * @param string $notificationJson Notification JSON.
	 * @param string $triggerKey Trigger key.
	 * @return void
	 * @since  8.0.0
	 */
	public static function handleCron($notificationJson, $triggerKey)
	{
		$notification = Notification::from('json', $notificationJson);
		$trigger = self::getCache($triggerKey)->get();

		if (!$trigger instanceof Triggerable) {
			ErrorHandler::error(
				sprintf('Trigger key %s doesn\'t seem to exist in cache anymore', $triggerKey)
			);
			return;
		}

		self::getCache($triggerKey)->delete();

		self::processNotification($notification, $trigger);
	}

	/**
	 * Sends the Carrier in context of Trigger
	 *
	 * @param \BracketSpace\Notification\Interfaces\Sendable $carrier Carrier object.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return void
	 * @since  8.0.0
	 */
	public static function send(Sendable $carrier, Triggerable $trigger)
	{
		$carrier->send($trigger);
		do_action('notification/carrier/sent', $carrier, $trigger);
	}

	/**
	 * Gets cache instance
	 *
	 * @param string $triggerKey Trigger key.
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache
	 * @since  8.0.11
	 */
	public static function getCache($triggerKey)
	{
		return new Cache(new Transient(3 * DAY_IN_SECONDS), $triggerKey);
	}
}
