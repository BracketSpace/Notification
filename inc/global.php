<?php
/**
 * Global functions
 */

use underDEV\Notification\Interfaces;

/**
 * Registers trigger
 * Uses notification/triggers filter
 * @param  Interfaces\Triggerable $trigger trigger object
 * @return void
 */
function register_trigger( Interfaces\Triggerable $trigger ) {

	add_filter( 'notification/triggers', function( $triggers ) use ( $trigger ) {

		if ( isset( $triggers[ $trigger->get_slug() ] ) ) {
			throw new \Exception( 'Trigger with that slug already exists' );
		} else {
			$triggers[ $trigger->get_slug() ] = $trigger;
		}

		return $triggers;

	} );

}

/**
 * Registers notification
 * Uses notification/notifications filter
 * @param  Interfaces\Sendable $trigger      trigger object
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
