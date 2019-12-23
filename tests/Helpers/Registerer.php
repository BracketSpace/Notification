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
	 * @since  6.0.0
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
	 * @since  6.0.0
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
	 * @since  6.0.0
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
	 * @since  6.0.0
	 * @param  bool $postponed If trigger should be postponed.
	 * @return Notifiation Registered Notification.
	 */
	public static function register_default_notification( $postponed = false ) {
		$trigger = static::register_trigger( $postponed );
		$carrier = static::register_carrier()->enable();
		return static::register_notification( $trigger, [ $carrier ] );
	}

	/**
	 * Register Resolver
	 *
	 * @since [Next]
	 * @return Objects\Resolver Registered Resovler.
	 */
	public static function register_resolver(){
		$resolver = new Objects\Resolver();

		notification_register_resolver( $resolver );
		return $resolver;
	}

	/**
	 * Register Recipient
	 *
	 * @since [Next]
	 * @return Obejcts\Recipient Registered Recipient.
	 */
	public static function register_recipient( $params = [] ){
		$recipient = new Objects\Recipient( $params );

		notification_register_recipient( new Objects\Carrier(), $recipient );
		return $recipient;
	}
}
