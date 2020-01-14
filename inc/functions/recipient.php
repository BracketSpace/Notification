<?php
/**
 * Recipient functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Defaults\Store\Recipient as RecipientStore;

/**
 * Registers recipient
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Recipient Store
 * @param  string                $carrier_slug Carrier slug.
 * @param  Interfaces\Receivable $recipient    Recipient object.
 * @return \WP_Error | true
 */
function notification_register_recipient( $carrier_slug, Interfaces\Receivable $recipient ) {

	$store = new RecipientStore();

	try {
		$store[ $carrier_slug ] = $recipient;
	} catch ( \Exception $e ) {
		return new \WP_Error( 'notification_register_trigger_error', $e->getMessage() );
	}

	do_action( 'notification/recipient/registered', $recipient );

	return true;

}

/**
 * Gets all registered recipients
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Recipient Store
 * @return array recipients
 */
function notification_get_recipients() {
	$store = new RecipientStore();
	return $store->get_items();
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
	return isset( $recipients[ $carrier_slug ] ) ? $recipients[ $carrier_slug ] : array();
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
		return array();
	}

	return $recipient->parse_value( $recipient_raw_value );

}
