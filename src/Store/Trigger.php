<?php

/**
 * Triger Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Trigger Store
 *
 * @method static array<string, \BracketSpace\Notification\Interfaces\Triggerable> all() Gets all registered Triggers
 * @method static \BracketSpace\Notification\Interfaces\Triggerable|null get(string $index) Gets registered Trigger
 */
class Trigger implements Interfaces\Storable
{
	use Storage;

	/**
	 * Gets all Triggers grouped.
	 *
	 * @since  8.0.0
	 * @return array<string, array<string, \BracketSpace\Notification\Interfaces\Triggerable>>
	 */
	public static function grouped(): array
	{
		$groups = [];

		foreach (static::all() as $trigger) {
			if (! isset($groups[$trigger->get_group()])) {
				$groups[(string)$trigger->get_group()] = [];
			}

			$groups[(string)$trigger->get_group()][$trigger->get_slug()] = $trigger;
		}

		return $groups;
	}
}
