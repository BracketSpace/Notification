<?php
/**
 * Recipient Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Store;

use BracketSpace\Notification\Abstracts;

/**
 * Recipient Store
 */
class Recipient extends Abstracts\Store {

	/**
	 * Storage key
	 */
	const STORAGE_KEY = 'recipient';

	/**
	 * {@inheritdoc}
	 *
	 * Compares the version of Recipient object, and replace it if adding the newer.
	 *
	 * @since  6.3.0
	 * @throws \Exception If no offset has been provided and item doesn't implements Nameable.
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

			if ( ! isset( $items[ $offset ] ) ) {
				$items[ $offset ] = [];
			}

			if ( isset( $items[ $offset ][ $value->get_slug() ] ) ) {
				throw new \Exception( 'Recipient with that slug is already registered for ' . $offset . ' Carrier' );
			} else {
				$items[ $offset ][ $value->get_slug() ] = $value;
			}

			return $items;

		} );
	}

}
