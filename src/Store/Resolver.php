<?php
/**
 * Resolver Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Resolver Store
 */
class Resolver implements Interfaces\Storable {
	use Storage;

	/**
	 * Gets all Resolvers sorted by priority.
	 *
	 * @since  8.0.0
	 * @return array<string,Interfaces\Resolvable>
	 */
	public static function sorted() : array {
		$resolvers = static::all();

		usort( $resolvers, function ( $a, $b ) {
			if ( $a->get_priority() === $b->get_priority() ) {
				return 0;
			}

			return $a->get_priority() < $b->get_priority() ? -1 : 1;
		} );

		return $resolvers;
	}
}
