<?php

/**
 * Resolver Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Resolver Store
 */
class Resolver implements Interfaces\Storable
{
	/** @use Storage<Interfaces\Resolvable> */
	use Storage;

	/**
	 * Gets all Resolvers sorted by priority.
	 *
	 * @return array<Interfaces\Resolvable>
	 * @since  8.0.0
	 */
	public static function sorted(): array
	{
		$resolvers = static::all();

		usort(
			$resolvers,
			static function ($a, $b) {
				if ($a->getPriority() === $b->getPriority()) {
					return 0;
				}

				return $a->getPriority() < $b->getPriority()
					? -1
					: 1;
			}
		);

		return $resolvers;
	}
}
