<?php
/**
 * Notification functions
 */

use underDEV\Notification\Interfaces;

/**
 * Registers notification
 * Uses notification/notifications filter
 * @param  Interfaces\Sendable $notification notification object
 * @return void
 */
function register_notification( Interfaces\Sendable $notification ) {

	add_filter( 'notification/notifications', function( $notifications ) use ( $notification ) {

		if ( isset( $notifications[ $notification->get_slug() ] ) ) {
			throw new \Exception( 'Notification with that slug already exists' );
		} else {
			$notifications[ $notification->get_slug() ] = $notification;
		}

		return $notifications;

	} );

}
