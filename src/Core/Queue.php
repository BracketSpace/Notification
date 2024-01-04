<?php

/**
 * Queue
 *
 * Holds the Notifications and triggers.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Core\Notification as CoreNotification;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Queue class
 */
class Queue
{
	/**
	 * Items
	 *
	 * @var array<int, array{
	 * notification: \BracketSpace\Notification\Core\Notification,
	 * trigger: \BracketSpace\Notification\Interfaces\Triggerable}
	 * >
	 */
	protected static $items = [];

	/**
	 * Adds the item to the queue
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger.
	 * @param int|null $index Index at which to put the item.
	 * @return void
	 * @since 8.0.0
	 */
	//phpcs:ignore SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilitySymbolRequired
	public static function add(CoreNotification $notification, Triggerable $trigger, int $index = null)
	{
		$item = [
			'notification' => $notification,
			'trigger' => clone $trigger,
		];

		if ($index !== null) {
			self::$items[$index] = $item;
		}

		self::$items[] = $item;
	}

	/**
	 * Replaces the items if they are already in the queue
	 * or adds new queue item
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger.
	 * @return void
	 * @since 8.0.0
	 */
	public static function addReplace(CoreNotification $notification, Triggerable $trigger)
	{
		// Check if item already exists.
		foreach (self::$items as $index => $item) {
			if ($item['notification'] === $notification && $item['trigger'] === $trigger) {
				self::add($notification, $trigger, $index);
				return;
			}
		}

		self::add($notification, $trigger);
	}

	/**
	 * Checks if the items are already in the queue
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger.
	 * @return bool
	 * @since 8.0.0
	 */
	public static function has(CoreNotification $notification, Triggerable $trigger): bool
	{
		foreach (self::$items as $item) {
			if ($item['notification'] === $notification && $item['trigger'] === $trigger) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets items added to the queue
	 *
	 * @return array<int,array{
	 * notification: \BracketSpace\Notification\Core\Notification,
	 * trigger: \BracketSpace\Notification\Interfaces\Triggerable}
	 * >
	 * @since 8.0.0
	 */
	public static function get(): array
	{
		return self::$items;
	}

	/**
	 * Iterates over the queue items
	 *
	 * @param callable $callback Callback for each item.
	 * @return void
	 * @since 8.0.0
	 */
	public static function iterate(callable $callback)
	{
		foreach (self::get() as $index => $item) {
			call_user_func(
				$callback,
				$index,
				$item['notification'],
				$item['trigger']
			);
		}
	}

	/**
	 * Clears the queue entirely
	 *
	 * @return void
	 * @since 8.0.9
	 */
	public static function clear()
	{
		self::$items = [];
	}

	/**
	 * Removes an item from the queue
	 *
	 * @param int $index Index of an item to remove.
	 * @return void
	 * @since 8.0.9
	 */
	public static function remove(int $index)
	{
		unset(self::$items[$index]);
	}
}
