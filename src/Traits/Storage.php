<?php
/**
 * Storage trait
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

use BracketSpace\Notification\ErrorHandler;

/**
 * Storage trait
 */
trait Storage {

	/**
	 * Stored items
	 *
	 * @var array<mixed>
	 */
	private static $items = [];

	/**
	 * Adds an item to the Store
	 *
	 * @since  [Next]
	 * @param  mixed $item Item to add.
	 * @return void
	 */
	public static function add( $item ) : void {
		static::$items[] = $item;
	}

	/**
	 * Inserts an item at a specifc index.
	 *
	 * @since  [Next]
	 * @param  int|string $index Item index.
	 * @param  mixed      $item  Item to add.
	 * @return void
	 */
	public static function insert( $index, $item ) : void {
		if ( array_key_exists( $index, static::$items ) ) {
			ErrorHandler::error(
				sprintf(
					'Item at index %s in %s Store already exists.',
					$index,
					__CLASS__
				)
			);

			return;
		}

		static::$items[ $index ] = $item;
	}

	/**
	 * Gets all items
	 *
	 * @since  [Next]
	 * @return array<mixed>
	 */
	public static function all() : array {
		return static::$items;
	}

	/**
	 * Removes all items from the store
	 *
	 * @since  [Next]
	 * @return void
	 */
	public static function clear() {
		static::$items = [];
	}

	/**
	 * Get item by index
	 *
	 * @since  [Next]
	 * @param  mixed $index Intex of an item.
	 * @return mixed
	 */
	public static function get( $index ) {
		if ( ! array_key_exists( $index, static::$items ) ) {
			ErrorHandler::error(
				sprintf(
					'Item %s in %s Store doesn\'t exists.',
					$index,
					__CLASS__
				)
			);

			return;
		}

		return static::$items[ $index ];
	}

}
