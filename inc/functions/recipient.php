<?php
/**
 * Recipient functions
 */

use underDEV\Notification\Interfaces;

/**
 * Registers recipient
 * Uses notification/recipients filter
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
