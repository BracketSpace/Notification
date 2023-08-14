<?php

/**
 * Storage trait
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

use BracketSpace\Notification\ErrorHandler;

/**
 * Storage trait
 */
trait Storage
{
	/**
	 * Stored items
	 *
	 * @var array<mixed>
	 */
	private static $items = [];

	/**
	 * Adds an item to the Store
	 *
	 * @param mixed $item Item to add.
	 * @return void
	 * @since  8.0.0
	 */
	public static function add($item)
	{
		static::$items[] = $item;
	}

	/**
	 * Inserts an item at a specific index.
	 *
	 * @param int|string $index Item index.
	 * @param mixed $item Item to add.
	 * @return void
	 * @since  8.0.0
	 */
	public static function insert($index, $item)
	{
		if (static::has($index)) {
			ErrorHandler::error(
				sprintf(
					'Item at index %s in %s Store already exists.',
					$index,
					self::class
				)
			);

			return;
		}

		static::$items[$index] = $item;
	}

	/**
	 * Gets all items
	 *
	 * @return array<mixed>
	 * @since  8.0.0
	 */
	public static function all(): array
	{
		return static::$items;
	}

	/**
	 * Removes all items from the store
	 *
	 * @return void
	 * @since  8.0.0
	 */
	public static function clear()
	{
		static::$items = [];
	}

	/**
	 * Get item by index
	 *
	 * @param mixed $index Intex of an item.
	 * @return mixed
	 * @since  8.0.0
	 */
	public static function get($index)
	{
		if (!static::has($index)) {
			ErrorHandler::error(
				sprintf(
					'Item %s in %s Store doesn\'t exists.',
					$index,
					self::class
				)
			);

			return;
		}

		return static::$items[$index];
	}

	/**
	 * Checks if the Storage has item
	 *
	 * @param mixed $index Intex of an item.
	 * @return bool
	 * @since  8.0.0
	 */
	public static function has($index): bool
	{
		return array_key_exists($index, static::$items);
	}
}
