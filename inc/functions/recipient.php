<?php
/**
 * Recipient functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;

/**
 * Registers recipient
 * Uses notification/recipients filter
 *
 * @since  6.0.0 Changed naming convention from Notification to Carrier.
 * @param  string                $carrier_slug Carrier slug.
 * @param  Interfaces\Receivable $recipient    Recipient object.
 * @return void
 */
function notification_register_recipient( $carrier_slug, Interfaces\Receivable $recipient ) {

	add_filter( 'notification/recipients', function( $recipients ) use ( $carrier_slug, $recipient ) {

		if ( ! isset( $recipients[ $carrier_slug ] ) ) {
			$recipients[ $carrier_slug ] = [];
		}

		if ( isset( $recipients[ $carrier_slug ][ $recipient->get_slug() ] ) ) {
			throw new \Exception( 'Recipient with that slug is already registered for ' . $carrier_slug . ' Carrier' );
		} else {
			$recipients[ $carrier_slug ][ $recipient->get_slug() ] = $recipient;
		}

		return $recipients;

	} );

}

/**
 * Gets all registered recipients
 *
 * @since  5.0.0
 * @return array recipients
 */
function notification_get_recipients() {
	return apply_filters( 'notification/recipients', [] );
}

/**
 * Gets registered recipients for specific Carrier
 *
 * @since  6.0.0
 * @param  string $carrier_slug Carrier slug.
 * @return array                Recipients array
 */
function notification_get_carrier_recipients( $carrier_slug ) {
	$recipients = notification_get_recipients();
	return isset( $recipients[ $carrier_slug ] ) ? $recipients[ $carrier_slug ] : [];
}

/**
 * Gets single registered recipient for specific Carrier
 *
 * @since  6.0.0
 * @param  string $carrier_slug   Carrier slug.
 * @param  string $recipient_slug Recipient slug.
 * @return mixed                  Recipient object or false
 */
function notification_get_recipient( $carrier_slug, $recipient_slug ) {
	$recipients = notification_get_recipients();
	return isset( $recipients[ $carrier_slug ][ $recipient_slug ] ) ? $recipients[ $carrier_slug ][ $recipient_slug ] : false;
}

/**
 * Parses recipient raw value to values which can be used by notifications
 *
 * @since  5.0.0
 * @since  6.0.0 Changed naming convention from Notification to Carrier.
 * @param  string $carrier_slug        Slug of the Carrier.
 * @param  string $recipient_type      Slug of the Recipient.
 * @param  mixed  $recipient_raw_value Raw value.
 * @return mixed                       Parsed value
 */
function notification_parse_recipient( $carrier_slug, $recipient_type, $recipient_raw_value ) {

	$recipient = notification_get_recipient( $carrier_slug, $recipient_type );

	if ( ! $recipient instanceof Interfaces\Receivable ) {
		return [];
	}

	return $recipient->parse_value( $recipient_raw_value );

}
