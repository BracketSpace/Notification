<?php
/**
 * Global functions
 *
 * @package notification
 */

use underDEV\Notification\Interfaces;
use underDEV\Notification\Recipients;

/**
 * Registers trigger
 * Uses notification/triggers filter
 *
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
 *
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

/**
 * Registers recipient
 * Uses notification/recipients filter
 *
 * @param  string                $notification notification slug
 * @param  Interfaces\Receivable $recipient    recipient object
 * @return void
 */
function register_recipient( $notification, Interfaces\Receivable $recipient ) {

	add_filter( 'notification/recipients', function( $recipients ) use ( $notification, $recipient ) {

		if ( ! isset( $recipients[ $notification ] ) ) {
			$recipients[ $notification ] = array();
		}

		if ( isset( $recipients[ $notification ][ $recipient->get_slug() ] ) ) {
			throw new \Exception( 'Recipient with that slug already registered for ' . $notification . ' notification' );
		} else {
			$recipients[ $notification ][ $recipient->get_slug() ] = $recipient;
		}

		return $recipients;

	} );

}

function notification_parse_recipient( $notification_slug, $recipient_type, $recipient_raw_value ) {

	$recipients = new Recipients();
	$recipient  = $recipients->get_single( $notification_slug, $recipient_type );

	if ( ! $recipient instanceof Interfaces\Receivable ) {
		return array();
	}

	return $recipient->parse_value( $recipient_raw_value );

}
