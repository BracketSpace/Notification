<?php
/**
 * Recipient Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Interfaces;

/**
 * Recipient Store
 *
 * @todo Refactor the class so it uses Storage trait.
 */
class Recipient implements Interfaces\Storable {

	/**
	 * Stored items
	 *
	 * @var array<mixed>
	 */
	private static $items = [];

	/**
	 * Inserts a Recipient for specific Carrier.
	 *
	 * @since  8.0.0
	 * @param  string                $carrier_slug Carrier slug.
	 * @param  string                $slug         Recipient slug.
	 * @param  Interfaces\Receivable $recipient    Recipient to add.
	 * @return void
	 */
	public static function insert( string $carrier_slug, string $slug, Interfaces\Receivable $recipient ) {
		if ( ! isset( static::$items[ $carrier_slug ] ) ) {
			static::$items[ $carrier_slug ] = [];
		}

		if ( array_key_exists( $slug, static::$items[ $carrier_slug ] ) ) {
			ErrorHandler::error(
				sprintf(
					'Recipient with %s slug for %s Carrier in %s Store already exists.',
					$slug,
					$carrier_slug,
					__CLASS__
				)
			);

			return;
		}

		static::$items[ $carrier_slug ][ $slug ] = $recipient;
	}

	/**
	 * Gets all items
	 *
	 * @since  8.0.0
	 * @return array<string,array<string,Interfaces\Receivable>>
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
	 * Gets all Recipients for specific Carrier
	 *
	 * @since  8.0.0
	 * @param  string $carrier_slug Carrier slug.
	 * @return array<string,Interfaces\Receivable>
	 */
	public static function all_for_carrier( string $carrier_slug ) : array {
		if ( ! array_key_exists( $carrier_slug, static::$items ) ) {
			ErrorHandler::error(
				sprintf(
					'Carrier %s in %s Store doesn\'t have any Recipients.',
					$carrier_slug,
					__CLASS__
				)
			);

			return [];
		}

		return static::$items[ $carrier_slug ];
	}

	/**
	 * Gets Recipient for Carrier by a slug
	 *
	 * @since  8.0.0
	 * @param  string $carrier_slug Carrier slug.
	 * @param  string $slug         Recipient slug.
	 * @return mixed
	 */
	public static function get( string $carrier_slug, $slug ) {
		$carrier_recipients = static::all_for_carrier( $carrier_slug );

		if ( ! array_key_exists( $slug, $carrier_recipients ) ) {
			ErrorHandler::error(
				sprintf(
					'Carrier %s in %s Store doesn\'t have %s Recipient.',
					$carrier_slug,
					__CLASS__,
					$slug
				)
			);

			return;
		}

		return $carrier_recipients[ $slug ];
	}

}
