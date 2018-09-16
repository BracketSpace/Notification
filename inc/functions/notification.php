<?php
/**
 * Notification functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;

/**
 * Registers notification
 * Uses notification/notifications filter
 *
 * @param  Interfaces\Sendable $notification notification object.
 * @return void
 */
function register_notification( Interfaces\Sendable $notification ) {

	add_filter(
		'notification/notifications',
		function( $notifications ) use ( $notification ) {

			if ( isset( $notifications[ $notification->get_slug() ] ) ) {
				throw new \Exception( 'Notification with that slug already exists' );
			} else {
				$notifications[ $notification->get_slug() ] = $notification;
			}

			return $notifications;

		}
	);

}

/**
 * Gets all registered notifications
 *
 * @since  5.0.0
 * @return array notifications
 */
function notification_get_notifications() {
	return apply_filters( 'notification/notifications', array() );
}

/**
 * Gets single registered notification
 *
 * @since  5.0.0
 * @param  string $notification_slug notification slug.
 * @return mixed                     notification object or false
 */
function notification_get_single_notification( $notification_slug ) {
	$notifications = notification_get_notifications();
	return isset( $notifications[ $notification_slug ] ) ? $notifications[ $notification_slug ] : false;
}
