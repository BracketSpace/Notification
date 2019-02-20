<?php
/**
 * Registerer helper class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers;

use BracketSpace\Notification\Tests\Helpers\Objects;
use BracketSpace\Notification\Core\Notification;

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

		notification_register_trigger( $trigger );

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

	/**
	 * Registers Notification
	 *
	 * @since  [Next]
	 * @param  mixed $trigger  Trigger object or null
	 * @param  array $carriers Array of Carrier objects
	 * @return Notifiation     Registered Notification.
	 */
	public static function register_notification( $trigger = null, $carriers = [] ) {
		$notification = new Notification( [
			'trigger'  => $trigger,
			'carriers' => $carriers,
		] );
		notification_add( $notification );
		return $notification;
	}

	/**
	 * Registers Default Notification
	 *
	 * @since  [Next]
	 * @param  bool $postponed If trigger should be postponed.
	 * @return Notifiation Registered Notification.
	 */
	public static function register_default_notification( $postponed = false ) {
		$trigger          = static::register_trigger( $postponed );
		$carrier          = static::register_carrier();
		$carrier->enabled = true;
		return static::register_notification( $trigger, [ $carrier ] );
	}

}
