<?php

/**
 * Trigger Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Trigger Store
 */
class Trigger implements Interfaces\Storable
{
	/** @use Storage<Interfaces\Triggerable> */
	use Storage;

	/**
	 * Gets all Triggers grouped.
	 *
	 * @return array<string, array<string, Interfaces\Triggerable>>
	 * @since  8.0.0
	 */
	public static function grouped(): array
	{
		$groups = [];

		foreach (static::all() as $trigger) {
			if (!isset($groups[$trigger->getGroup()])) {
				$groups[(string)$trigger->getGroup()] = [];
			}

			$groups[(string)$trigger->getGroup()][$trigger->getSlug()] = $trigger;
		}

		return $groups;
	}
}
