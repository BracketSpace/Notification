<?php
/**
 * Queue
 *
 * Holds the Carriers to dispatch them.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Queue class
 */
class Queue {

	/**
	 * Items
	 *
	 * @var array<int, array{carrier: Sendable, trigger: Triggerable}>
	 */
	protected static array $items = [];

	/**
	 * Adds the item to the queue
	 *
	 * @since [Next]
	 * @param Sendable    $carrier Carrier.
	 * @param Triggerable $trigger Trigger.
	 * @param int|null    $index   Index at which to put the item.
	 * @return void
	 */
	public static function add( Sendable $carrier, Triggerable $trigger, int $index = null ) : void {
		$item = [
			'carrier' => $carrier,
			'trigger' => $trigger,
		];

		if ( null !== $index ) {
			self::$items[ $index ] = $item;
		}

		self::$items[] = $item;
	}

	/**
	 * Replaces the items if they are already in the queue
	 * or adds new queue item
	 *
	 * @since [Next]
	 * @param Sendable    $carrier Carrier.
	 * @param Triggerable $trigger Trigger.
	 * @return void
	 */
	public static function add_replace( Sendable $carrier, Triggerable $trigger ) : void {
		// Check if item already exists.
		foreach ( self::$items as $index => $item ) {
			// phpcs:ignore.
			if ( $item['carrier'] == $carrier && $item['trigger'] == $trigger ) {
				self::add( $carrier, $trigger, $index );
				return;
			}
		}

		self::add( $carrier, $trigger );
	}

	/**
	 * Checks if the items are already in the queue
	 *
	 * @since [Next]
	 * @param Sendable    $carrier Carrier.
	 * @param Triggerable $trigger Trigger.
	 * @return bool
	 */
	public static function has( Sendable $carrier, Triggerable $trigger ) : bool {
		foreach ( self::$items as $item ) {
			// phpcs:ignore.
			if ( $item['carrier'] == $carrier && $item['trigger'] == $trigger ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets items added to the queue
	 *
	 * @since [Next]
	 * @return array<int, array{carrier: Sendable, trigger: Triggerable}>
	 */
	public static function get() : array {
		return self::$items;
	}

}
