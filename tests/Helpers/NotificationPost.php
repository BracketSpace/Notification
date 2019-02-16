<?php
/**
 * NotificationPost helper class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Interfaces\Adaptable;

/**
 * NotificationPost helper class
 */
class NotificationPost {

	/**
	 * Inserts notification post based on trigger and carrier
	 *
	 * @since  5.3.1
	 * @since  [Next] Changed to adapter implementation
	 * @param  Triggerable $trigger Trigger class or slug.
	 * @param  Sendable    $carrier Carrier class or slug.
	 * @return Adaptable            Notifcation WordPress adapter.
	 */
	public static function insert( Triggerable $trigger, Sendable $carrier ) {

		// Make sure the carrier is enabled.
		$carrier->enabled = true;

		$notification = new Notification( [
			'enabled'  => true,
			'trigger'  => $trigger,
			'carriers' => [ $carrier ],
		] );

		$adapter = notification_adapt( 'WordPress', $notification );
		$adapter->save();

		return $adapter;

	}

}
