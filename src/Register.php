<?php
/**
 * Register class
 *
 * @package notification
 */

namespace BracketSpace\Notification;

/**
 * Register class
 */
class Register {

	/**
	 * Registers Carrier
	 *
	 * @since  8.0.0
	 * @param  Interfaces\Sendable $carrier Carrier object.
	 * @return Interfaces\Sendable
	 */
	public static function carrier( Interfaces\Sendable $carrier ) {
		Store\Carrier::insert( $carrier->get_slug(), $carrier );
		do_action( 'notification/carrier/registered', $carrier );

		return $carrier;
	}

	/**
	 * Registers Recipient
	 *
	 * @since  8.0.0
	 * @param  string                $carrier_slug Carrier slug.
	 * @param  Interfaces\Receivable $recipient    Recipient object.
	 * @return Interfaces\Receivable
	 */
	public static function recipient( string $carrier_slug, Interfaces\Receivable $recipient ) {
		Store\Recipient::insert( $carrier_slug, $recipient->get_slug(), $recipient );
		do_action( 'notification/recipient/registered', $recipient, $carrier_slug );

		return $recipient;
	}

	/**
	 * Registers Recipient
	 *
	 * @since  8.0.0
	 * @param  Interfaces\Resolvable $resolver Resolver object.
	 * @return Interfaces\Resolvable
	 */
	public static function resolver( Interfaces\Resolvable $resolver ) {
		Store\Resolver::insert( $resolver->get_slug(), $resolver );
		do_action( 'notification/resolver/registered', $resolver );

		return $resolver;
	}

	/**
	 * Registers Trigger
	 *
	 * @since  8.0.0
	 * @param  Interfaces\Triggerable $trigger Trigger object.
	 * @return Interfaces\Triggerable
	 */
	public static function trigger( Interfaces\Triggerable $trigger ) {
		Store\Trigger::insert( $trigger->get_slug(), $trigger );
		do_action( 'notification/trigger/registered', $trigger );

		return $trigger;
	}

	/**
	 * Registers Global Merge Tag
	 *
	 * @since  8.0.0
	 * @param  Interfaces\Taggable $merge_tag MergeTag object.
	 * @return Interfaces\Taggable
	 */
	public static function global_merge_tag( Interfaces\Taggable $merge_tag ) {
		Store\GlobalMergeTag::insert( $merge_tag->get_slug(), $merge_tag );

		do_action( 'notification/global_merge_tag/registered', $merge_tag );

		// Register the Merge Tag.
		add_action( 'notification/trigger/merge_tags', function ( $trigger ) use ( $merge_tag ) {
			$trigger->add_merge_tag( clone $merge_tag );
		} );

		return $merge_tag;
	}

}
