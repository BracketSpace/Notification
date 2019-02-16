<?php
/**
 * Registerer helper class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers;

use BracketSpace\Notification\Tests\Helpers\Objects;

/**
 * Registerer helper class
 */
class Registerer {

	/**
	 * Registers Trigger
	 *
	 * @since  [Next]
	 * @param  bool $postponed If Trigger should be Postponed Trigger.
	 * @return Objects\Notification Registered Trigger.
	 */
	public static function register_trigger( $postponed = false ) {

		if ( $postponed ) {
			$trigger = new Objects\PostponedTrigger();
		} else {
			$trigger = new Objects\SimpleTrigger();
		}

		register_trigger( $trigger );

		return $trigger;

	}

	/**
	 * Registers Carrier
	 *
	 * @since  [Next]
	 * @return Objects\Carrier Registered Carrier.
	 */
	public static function register_carrier() {
		$carrier = new Objects\Carrier();
		notification_register_carrier( $carrier );
		return $carrier;
	}

}
