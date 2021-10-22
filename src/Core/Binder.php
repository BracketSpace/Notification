<?php
/**
 * Binds the Trigger actions
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

/**
 * Binder class
 */
class Binder {

	/**
	 * Binds the trigger registered actions
	 *
	 * @action notification/trigger/registered 100
	 *
	 * @since 8.0.0
	 * @param Triggerable[]|Triggerable $triggers Array of Triggers or single Trigger.
	 * @return void
	 */
	public static function bind( $triggers ) {

		if ( ! is_array( $triggers ) ) {
			$triggers = [ $triggers ];
		}

		foreach ( $triggers as $trigger ) {
			foreach ( $trigger->get_actions() as $action ) {
				add_action(
					$action['tag'],
					[ new Runner( $trigger ), 'run' ],
					$action['priority'],
					$action['accepted_args']
				);
			}
		}

	}

}
