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
	 * @since  8.0.0
	 * @param  mixed $item Item to add.
	 * @return void
	 */
	public static function add( $item ) {
		static::$items[] = $item;
	}

	/**
	 * Inserts an item at a specifc index.
	 *
	 * @since  8.0.0
	 * @param  int|string $index Item index.
	 * @param  mixed      $item  Item to add.
	 * @return void
	 */
	public static function insert( $index, $item ) {
		if ( static::has( $index ) ) {
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
	 * @since  8.0.0
	 * @return array<mixed>
	 */
	public static function all() : array {
		return static::$items;
	}

	/**
	 * Removes all items from the store
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public static function clear() {
		static::$items = [];
	}

	/**
	 * Get item by index
	 *
	 * @since  8.0.0
	 * @param  mixed $index Intex of an item.
	 * @return mixed
	 */
	public static function get( $index ) {
		if ( ! static::has( $index ) ) {
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

	/**
	 * Checks if the Storage has item
	 *
	 * @since  8.0.0
	 * @param  mixed $index Intex of an item.
	 * @return bool
	 */
	public static function has( $index ) : bool {
		return array_key_exists( $index, static::$items );
	}

}
