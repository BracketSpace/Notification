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
 */
class Trigger implements Interfaces\Storable {
	use Storage;

	/**
	 * Gets all Triggers grouped.
	 *
	 * @since  [Next]
	 * @return array<string,array<string, Interfaces\Triggerable>>
	 */
	public static function grouped() : array {
		$groups = [];

		/**
		 * @var Interfaces\Triggerable $trigger
		 */
		foreach ( static::all() as $trigger ) {
			if ( ! isset( $groups[ $trigger->get_group() ] ) ) {
				$groups[ (string) $trigger->get_group() ] = [];
			}

			$groups[ (string) $trigger->get_group() ][ $trigger->get_slug() ] = $trigger;
		}

		return $groups;
	}
}
