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
	 * Registers Carrier
	 *
	 * @since  8.0.0
	 * @param \BracketSpace\Notification\Interfaces\Sendable $carrier Carrier object.
	 * @return \BracketSpace\Notification\Interfaces\Sendable
	 */
	public static function carrier( Interfaces\Sendable $carrier )
	{
		Store\Carrier::insert($carrier->getSlug(), $carrier);
		do_action('notification/carrier/registered', $carrier);

		return $carrier;
	}

	/**
	 * Registers Recipient
	 *
	 * @since  8.0.0
	 * @param  string                $carrierSlug Carrier slug.
	 * @param \BracketSpace\Notification\Interfaces\Receivable $recipient Recipient object.
	 * @return \BracketSpace\Notification\Interfaces\Receivable
	 */
	public static function recipient( string $carrierSlug, Interfaces\Receivable $recipient )
	{
		Store\Recipient::insert($carrierSlug, $recipient->getSlug(), $recipient);
		do_action('notification/recipient/registered', $recipient, $carrierSlug);

		return $recipient;
	}

	/**
	 * Registers Recipient
	 *
	 * @since  8.0.0
	 * @param \BracketSpace\Notification\Interfaces\Resolvable $resolver Resolver object.
	 * @return \BracketSpace\Notification\Interfaces\Resolvable
	 */
	public static function resolver( Interfaces\Resolvable $resolver )
	{
		Store\Resolver::insert($resolver->getSlug(), $resolver);
		do_action('notification/resolver/registered', $resolver);

		return $resolver;
	}

	/**
	 * Registers Trigger
	 *
	 * @since  8.0.0
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return \BracketSpace\Notification\Interfaces\Triggerable
	 */
	public static function trigger( Interfaces\Triggerable $trigger )
	{
		Store\Trigger::insert($trigger->getSlug(), $trigger);
		do_action('notification/trigger/registered', $trigger);

		return $trigger;
	}

	/**
	 * Registers Global Merge Tag
	 *
	 * @since  8.0.0
	 * @param \BracketSpace\Notification\Interfaces\Taggable $mergeTag MergeTag object.
	 * @return \BracketSpace\Notification\Interfaces\Taggable
	 */
	public static function globalMergeTag( Interfaces\Taggable $mergeTag )
	{
		Store\GlobalMergeTag::insert($mergeTag->getSlug(), $mergeTag);

		do_action('notification/global_merge_tag/registered', $mergeTag);

		// Register the Merge Tag.
		add_action(
			'notification/trigger/merge_tags',
			static function ( $trigger ) use ( $mergeTag ) {
				$trigger->addMergeTag(clone $mergeTag);
			}
		);

		return $mergeTag;
	}
}
