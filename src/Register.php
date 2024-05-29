<?php

/**Register class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification;

/**
 * Register class
 */
class Register
{
	/**
	 * Registers Notification
	 *
	 * @since [Next]
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
	 * @return \BracketSpace\Notification\Core\Notification
	 */
	public static function notification(Core\Notification $notification)
	{
		Store\Notification::insert(
			$notification->getHash(),
			$notification
		);
		do_action('notification/notification/registered', $notification);

		return $notification;
	}

	/**
	 * Creates new Notification from array
	 *
	 * Accepts both array with Trigger and Carriers objects or static values.
	 *
	 * @since [Next]
	 * @param NotificationUnconvertedData $data Notification data.
	 * @return \WP_Error|true
	 */
	public static function notificationFromArray($data = [])
	{
		try {
			self::notification(Core\Notification::from('array', $data));
		} catch (\Throwable $e) {
			return new \WP_Error('notification_error', $e->getMessage());
		}

		return true;
	}

	/**
	 * Registers Carrier
	 *
	 * @param \BracketSpace\Notification\Interfaces\Sendable $carrier Carrier object.
	 * @return \BracketSpace\Notification\Interfaces\Sendable
	 * @since  8.0.0
	 */
	public static function carrier(Interfaces\Sendable $carrier)
	{
		Store\Carrier::insert(
			$carrier->getSlug(),
			$carrier
		);
		do_action('notification/carrier/registered', $carrier);

		return $carrier;
	}

	/**
	 * Registers Recipient
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @param \BracketSpace\Notification\Interfaces\Receivable $recipient Recipient object.
	 * @return \BracketSpace\Notification\Interfaces\Receivable
	 * @since  8.0.0
	 */
	public static function recipient(string $carrierSlug, Interfaces\Receivable $recipient)
	{
		Store\Recipient::insert(
			$carrierSlug,
			$recipient->getSlug(),
			$recipient
		);
		do_action('notification/recipient/registered', $recipient, $carrierSlug);

		return $recipient;
	}

	/**
	 * Registers Recipient
	 *
	 * @param \BracketSpace\Notification\Interfaces\Resolvable $resolver Resolver object.
	 * @return \BracketSpace\Notification\Interfaces\Resolvable
	 * @since  8.0.0
	 */
	public static function resolver(Interfaces\Resolvable $resolver)
	{
		Store\Resolver::insert(
			$resolver->getSlug(),
			$resolver
		);
		do_action('notification/resolver/registered', $resolver);

		return $resolver;
	}

	/**
	 * Registers Trigger
	 *
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return \BracketSpace\Notification\Interfaces\Triggerable
	 * @since  8.0.0
	 */
	public static function trigger(Interfaces\Triggerable $trigger)
	{
		Store\Trigger::insert(
			$trigger->getSlug(),
			$trigger
		);
		do_action('notification/trigger/registered', $trigger);

		return $trigger;
	}

	/**
	 * Registers Global Merge Tag
	 *
	 * @param \BracketSpace\Notification\Interfaces\Taggable $mergeTag MergeTag object.
	 * @return \BracketSpace\Notification\Interfaces\Taggable
	 * @since  8.0.0
	 */
	public static function globalMergeTag(Interfaces\Taggable $mergeTag)
	{
		Store\GlobalMergeTag::insert(
			$mergeTag->getSlug(),
			$mergeTag
		);

		do_action('notification/global_merge_tag/registered', $mergeTag);

		// Register the Merge Tag.
		add_action(
			'notification/trigger/merge_tags',
			static function ($trigger) use ($mergeTag) {
				$trigger->addMergeTag(clone $mergeTag);
			}
		);

		return $mergeTag;
	}
}
