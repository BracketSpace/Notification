<?php
/**
 * Registerer helper class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers;

use BracketSpace\Notification\Tests\Helpers\Objects;
use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Store;

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
	public static function register_trigger( $tag = null, $postponed = false ) {

		if ( $postponed ) {
			$trigger_tag = $tag ?? 'notification/tests/postponed_trigger';
			$trigger = new Objects\PostponedTrigger( $trigger_tag );
		} else {
			$trigger_tag = $tag ?? 'notification/tests/simple_trigger';
			$trigger = new Objects\SimpleTrigger( $trigger_tag );
		}

		notification_register_trigger( $trigger );

		return $trigger;

	}

	/**
	 * Clears all Triggers
	 *
	 * @since  [Next]
	 * @return void
	 */
	public static function clear_triggers() {
		Store\Trigger::clear();
	}

	/**
	 * Registers Carrier
	 *
	 * @since  6.0.0
	 * @return Objects\Carrier Registered Carrier.
	 */
	public static function register_carrier( $carrier_slug = 'dummmy'  ) {
		$carrier = new Objects\Carrier( $carrier_slug );
		notification_register_carrier( $carrier );
		return $carrier;
	}

	/**
	 * Clears all Carriers
	 *
	 * @since  [Next]
	 * @return void
	 */
	public static function clear_carriers() {
		Store\Carrier::clear();
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
		$trigger = static::register_trigger( '', $postponed );
		$carrier = static::register_carrier()->enable();
		return static::register_notification( $trigger, [ $carrier ] );
	}

	/**
	 * Clears all Notifications
	 *
	 * @since  [Next]
	 * @return void
	 */
	public static function clear_notifications() {
		Store\Notification::clear();
	}

	/**
	 * Register Resolver
	 *
	 * @since 6.3.0
	 * @return Objects\Resolver Registered Resovler.
	 */
	public static function register_resolver() {
		$resolver = new Objects\Resolver();
		notification_register_resolver( $resolver );
		return $resolver;
	}

	/**
	 * Clears all Resolvers
	 *
	 * @since  [Next]
	 * @return void
	 */
	public static function clear_resolvers() {
		Store\Resolver::clear();
	}

	/**
	 * Register Recipient
	 *
	 * @since 6.3.0
	 * @param  string            $carrier_slug Carrier slug.
	 * @return Obejcts\Recipient               Registered Recipient.
	 */
	public static function register_recipient( $carrier_slug = 'dummy_carrier' ) {
		$recipient = new Objects\Recipient();
		notification_register_recipient( $carrier_slug, $recipient );
		return $recipient;
	}

	/**
	 * Clears all Recipients
	 *
	 * @since  [Next]
	 * @return void
	 */
	public static function clear_recipients() {
		Store\Recipient::clear();
	}

	/**
	 * Clears all registered items
	 *
	 * @since  [Next]
	 * @return void
	 */
	public static function clear() {
		static::clear_triggers();
		static::clear_carriers();
		static::clear_notifications();
		static::clear_resolvers();
		static::clear_recipients();
	}
}
