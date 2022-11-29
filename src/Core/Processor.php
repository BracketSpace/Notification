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
use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Processor class
 */
class Processor
{

	/**
	 * Processes the Queue
	 *
	 * @action shutdown
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public function processQueue()
	{

		Queue::iterate(
			static function ( $index, $notification, $trigger ) {

				$bpEnabled = apply_filters(
					'notification/trigger/process_in_background',
					notification_get_setting('general/advanced/background_processing'),
					$trigger
				);

				// If Background Processing is enabled we load the execution to Cron and stop processing.
				if ($bpEnabled) {
					self::schedule($notification, $trigger);
					return;
				}

				self::process_notification($notification, $trigger);
			}
		);
	}

	/**
	 * Scheduled the Notification submission in Cron.
	 *
	 * @since  8.0.0
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return void
	 */
	public static function schedule( Notification $notification, Triggerable $trigger )
	{
		// Optimize Trigger.
		$trigger->clearMergeTags();

		/**
		 * Identifies the Trigger by its values. This serves two purposes:
		 * 1. We create the param cache key out of the id
		 * 2. Based on that ID we can detect duplicates
		 *
		 * By default all Trigger props are taken and whole trigger is unique.
		 * This can be overriden by specific extensions.
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
		self::get_cache($triggerKey)->set($trigger);

		$result = wp_schedule_single_event(
			time() + apply_filters('notification/background_processing/delay', 30),
			'notification_background_processing',
			[
				notification_adapt('JSON', $notification)->save(JSON_UNESCAPED_UNICODE, true),
				$triggerKey,
			]
		);
	}

	/**
	 * Dispatches all the Carriers attached for Notification.
	 *
	 * @since  8.0.0
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return void
	 */
	public static function processNotification( Notification $notification, Triggerable $trigger )
	{
		$trigger->setupMergeTags();

		if (! apply_filters('notification/should_send', true, $notification, $trigger)) {
			return;
		}

		foreach ($notification->getEnabledCarriers() as $carrier) {
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
	 * @since  8.0.0
	 * @param  string $notificationJson Notification JSON.
	 * @param  string $triggerKey       Trigger key.
	 * @return void
	 */
	public static function handleCron( $notificationJson, $triggerKey )
	{
		$notification = notification_adapt_from('JSON', $notificationJson)->getNotification();
		$trigger = self::get_cache($triggerKey)->get();

		if (! $trigger instanceof Triggerable) {
			ErrorHandler::error(
				sprintf(
					'Trigger key %s doesn\'t seem to exist in cache anymore',
					$triggerKey
				)
			);
			return;
		}

		self::get_cache($triggerKey)->delete();

		self::process_notification($notification, $trigger);
	}

	/**
	 * Sends the Carrier in context of Trigger
	 *
	 * @since  8.0.0
	 * @param \BracketSpace\Notification\Interfaces\Sendable $carrier Carrier object.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return void
	 */
	public static function send( Sendable $carrier, Triggerable $trigger )
	{
		$carrier->send($trigger);
		do_action('notification/carrier/sent', $carrier, $trigger);
	}

	/**
	 * Gets cache instance
	 *
	 * @since  8.0.11
	 * @param  string $triggerKey Trigger key.
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache
	 */
	public static function getCache( $triggerKey )
	{
		return new Cache(
			new Transient(3 * DAY_IN_SECONDS),
			$triggerKey
		);
	}
}
