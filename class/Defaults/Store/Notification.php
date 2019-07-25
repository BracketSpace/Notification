<?php
/**
 * Notification Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Store;

use BracketSpace\Notification\Abstracts;

/**
 * Notification Store
 */
class Notification extends Abstracts\Store {

	/**
	 * Storage key
	 */
	const STORAGE_KEY = 'notification';

	/**
	 * {@inheritdoc}
	 *
	 * Compares the version of Notification object, and replace it if adding the newer.
	 *
	 * @since  6.0.0
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

			// Check if newer version hasn't been added already.
			if ( isset( $items[ $offset ] ) && version_compare( $items[ $offset ]->get_version(), $value->get_version(), '>=' ) ) {
				return $items;
			}

			$items[ $offset ] = $value;

			return $items;

		} );

	}

	/**
	 * Gets the Notifications with specific Trigger
	 *
	 * @since  6.0.0
	 * @param  string $trigger_slug Trigger slug.
	 * @return array
	 */
	public function with_trigger( $trigger_slug ) {
		return array_filter( $this->get_items(), function( $notification ) use ( $trigger_slug ) {
			return ! empty( $notification->get_trigger() ) && $notification->get_trigger()->get_slug() === $trigger_slug;
		} );
	}

}
