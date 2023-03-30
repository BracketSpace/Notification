<?php

/**
 * Recipient Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Interfaces;

/**
 * Recipient Store
 *
 * @todo Refactor the class so it uses Storage trait.
 */
class Recipient implements Interfaces\Storable
{
	/**
	 * Stored items
	 *
	 * @var array<mixed>
	 */
	private static $items = [];

	/**
	 * Inserts a Recipient for specific Carrier.
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @param string $slug Recipient slug.
	 * @param \BracketSpace\Notification\Interfaces\Receivable $recipient Recipient to add.
	 * @return void
	 * @since  8.0.0
	 */
	public static function insert(string $carrierSlug, string $slug, Interfaces\Receivable $recipient)
	{
		if (!isset(static::$items[$carrierSlug])) {
			static::$items[$carrierSlug] = [];
		}

		if (
			array_key_exists(
				$slug,
				static::$items[$carrierSlug]
			)
		) {
			ErrorHandler::error(
				sprintf(
					'Recipient with %s slug for %s Carrier in %s Store already exists.',
					$slug,
					$carrierSlug,
					self::class
				)
			);

			return;
		}

		static::$items[$carrierSlug][$slug] = $recipient;
	}

	/**
	 * Gets all items
	 *
	 * @return array<string, array<string, \BracketSpace\Notification\Interfaces\Receivable>>
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
	 * Gets all Recipients for specific Carrier
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @return array<string, \BracketSpace\Notification\Interfaces\Receivable>
	 * @since  8.0.0
	 */
	public static function allForCarrier(string $carrierSlug): array
	{
		if (
			!array_key_exists(
				$carrierSlug,
				static::$items
			)
		) {
			ErrorHandler::error(
				sprintf(
					'Carrier %s in %s Store doesn\'t have any Recipients.',
					$carrierSlug,
					self::class
				)
			);

			return [];
		}

		return static::$items[$carrierSlug];
	}

	/**
	 * Gets Recipient for Carrier by a slug
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @param string $slug Recipient slug.
	 * @return mixed
	 * @since  8.0.0
	 */
	public static function get(string $carrierSlug, $slug)
	{
		$carrierRecipients = static::allForCarrier($carrierSlug);

		if (
			!array_key_exists(
				$slug,
				$carrierRecipients
			)
		) {
			ErrorHandler::error(
				sprintf(
					'Carrier %s in %s Store doesn\'t have %s Recipient.',
					$carrierSlug,
					self::class,
					$slug
				)
			);

			return;
		}

		return $carrierRecipients[$slug];
	}
}
