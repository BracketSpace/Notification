<?php

/**
 * Binds the Trigger actions
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

/**
 * Binder class
 */
class Binder
{
	/**
	 * Binds the trigger registered actions
	 *
	 * @action notification/trigger/registered 100
	 *
	 * @param array<\BracketSpace\Notification\Interfaces\Triggerable> $triggers Array of Triggers or single Trigger.
	 * @return void
	 * @since 8.0.0
	 */
	public static function bind($triggers)
	{
		if (!is_array($triggers)) {
			$triggers = [$triggers];
		}

		foreach ($triggers as $trigger) {
			\assert($trigger instanceof \BracketSpace\Notification\Interfaces\Triggerable);
			foreach ($trigger->getActions() as $action) {
				add_action(
					$action['tag'],
					[new Runner($trigger), 'run'],
					$action['priority'],
					$action['accepted_args']
				);
			}
		}
	}
}
