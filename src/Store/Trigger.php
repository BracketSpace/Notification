<?php
/**
 * Triger Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Trigger Store
 *
 * @method static array<string,Interfaces\Triggerable> all() Gets all registered Triggers
 * @method static Interfaces\Triggerable|null get(string $index) Gets registered Trigger
 */
class Trigger implements Interfaces\Storable {
	use Storage;

	/**
	 * Gets all Triggers grouped.
	 *
	 * @since  8.0.0
	 * @return array<string,array<string, Interfaces\Triggerable>>
	 */
	public static function grouped() : array {
		$groups = [];

		foreach ( static::all() as $trigger ) {
			if ( ! isset( $groups[ $trigger->get_group() ] ) ) {
				$groups[ (string) $trigger->get_group() ] = [];
			}

			$groups[ (string) $trigger->get_group() ][ $trigger->get_slug() ] = $trigger;
		}

		return $groups;
	}
}
