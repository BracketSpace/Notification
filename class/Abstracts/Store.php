<?php
/**
 * Store abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;

/**
 * Store class
 */
abstract class Store implements Interfaces\Storable {

	/**
	 * Iterator position
	 *
	 * @var integer
	 */
	private $position = 0;

	/**
	 * Store keys
	 *
	 * @var array
	 */
	private $keys = [];

	/**
	 * Gets store key
	 *
	 * @since  6.0.0
	 * @return string
	 */
	public static function get_storage_key() {
		return 'notification/store/' . static::STORAGE_KEY;
	}

	/**
	 * Gets stored items
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public function get_items() {
		$items      = (array) apply_filters( self::get_storage_key(), [] );
		$this->keys = array_keys( $items );
		return $items;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @throws \Exception If no offset has been provided and item doesn't implements Nameable.
	 * @throws \Exception If item with the given key has been already added to Store.
	 * @param  mixed $offset Offset.
	 * @param  mixed $value  Value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {

		// Prepare offset.
		if ( empty( $offset ) ) {
			if ( ! ( $value instanceof Interfaces\Nameable ) ) {
				throw new \Exception( 'You must provide the item key or use "Nameable" object' );
			} else {
				$offset = $value->get_slug();
			}
		}

		// Add to Store.
		add_filter( self::get_storage_key(), function( $items ) use ( $offset, $value ) {

			if ( isset( $items[ $offset ] ) ) {
				throw new \Exception( sprintf( 'Item with key %s already exists in %s store', $offset, __CLASS__ ) );
			} else {
				$items[ $offset ] = $value;
			}

			return $items;

		} );

	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @param  mixed $offset Offset.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->get_items()[ $offset ] );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @param  mixed $offset Offset.
	 * @return void
	 */
	public function offsetUnset( $offset ) {

		// Removed from Store.
		add_filter( self::get_storage_key(), function( $items ) use ( $offset ) {
			unset( $items[ $offset ] );
			return $items;
		} );

	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @param  mixed $offset Offset.
	 * @return mixed         Value or null.
	 */
	public function offsetGet( $offset ) {
		$items = $this->get_items();
		return isset( $items[ $offset ] ) ? $items[ $offset ] : null;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function rewind() {
		$this->position = 0;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @return mixed Value.
	 */
	public function current() {
		return $this[ $this->key() ];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @return string Key.
	 */
	public function key() {
		return $this->keys[ $this->position ];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function next() {
		++$this->position;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  6.0.0
	 * @return bool
	 */
	public function valid() {
		$this->get_items();
		return isset( $this->keys[ $this->position ] );
	}

}
