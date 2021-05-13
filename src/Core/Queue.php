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
	 * @return void
	 */
	public static function add( Sendable $carrier, Triggerable $trigger ) : void {
		self::$items[] = [
			'carrier' => $carrier,
			'trigger' => $trigger,
		];
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
