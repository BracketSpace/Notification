<?php
/**
 * Queue
 *
 * Holds the Notifications and triggers.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Core\Notification as CoreNotification;
use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Queue class
 */
class Queue {

	/**
	 * Items
	 *
	 * @var array<int, array{notification: CoreNotification, trigger: Triggerable}>
	 */
	protected static $items = [];

	/**
	 * Adds the item to the queue
	 *
	 * @since 8.0.0
	 * @param CoreNotification $notification Notification.
	 * @param Triggerable      $trigger      Trigger.
	 * @param int|null         $index        Index at which to put the item.
	 * @return void
	 */
	public static function add( CoreNotification $notification, Triggerable $trigger, int $index = null ) {
		$item = [
			'notification' => $notification,
			'trigger'      => $trigger,
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
	 * @since 8.0.0
	 * @param CoreNotification $notification Notification.
	 * @param Triggerable      $trigger      Trigger.
	 * @return void
	 */
	public static function add_replace( CoreNotification $notification, Triggerable $trigger ) {
		// Check if item already exists.
		foreach ( self::$items as $index => $item ) {
			if ( $item['notification'] === $notification && $item['trigger'] === $trigger ) {
				self::add( $notification, $trigger, $index );
				return;
			}
		}

		self::add( $notification, $trigger );
	}

	/**
	 * Checks if the items are already in the queue
	 *
	 * @since 8.0.0
	 * @param CoreNotification $notification Notification.
	 * @param Triggerable      $trigger      Trigger.
	 * @return bool
	 */
	public static function has( CoreNotification $notification, Triggerable $trigger ) : bool {
		foreach ( self::$items as $item ) {
			if ( $item['notification'] === $notification && $item['trigger'] === $trigger ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets items added to the queue
	 *
	 * @since 8.0.0
	 * @return array<int, array{notification: CoreNotification, trigger: Triggerable}>
	 */
	public static function get() : array {
		return self::$items;
	}

	/**
	 * Iterates over the queue items
	 *
	 * @since 8.0.0
	 * @param callable $callback Callback for each item.
	 * @return void
	 */
	public static function iterate( callable $callback ) {
		foreach ( self::get() as $index => $item ) {
			call_user_func( $callback, $index, $item['notification'], $item['trigger'] );
		}
	}

}
