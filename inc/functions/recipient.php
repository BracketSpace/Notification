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
 * @param  string                $notification notification slug.
 * @param  Interfaces\Receivable $recipient    recipient object.
 * @return void
 */
function register_recipient( $notification, Interfaces\Receivable $recipient ) {

	add_filter(
		'notification/recipients',
		function( $recipients ) use ( $notification, $recipient ) {

			if ( ! isset( $recipients[ $notification ] ) ) {
				$recipients[ $notification ] = array();
			}

			if ( isset( $recipients[ $notification ][ $recipient->get_slug() ] ) ) {
				throw new \Exception( 'Recipient with that slug already registered for ' . $notification . ' notification' );
			} else {
				$recipients[ $notification ][ $recipient->get_slug() ] = $recipient;
			}

			return $recipients;

		}
	);

}

/**
 * Gets all registered recipients
 *
 * @since  5.0.0
 * @return array recipients
 */
function notification_get_recipients() {
	return apply_filters( 'notification/recipients', array() );
}

/**
 * Gets register recipients for notification type
 *
 * @since  5.0.0
 * @param  string $notification_type notification slug.
 * @return array                     recipients array
 */
function notification_get_notification_recipients( $notification_type ) {
	$recipients = notification_get_recipients();
	return isset( $recipients[ $notification_type ] ) ? $recipients[ $notification_type ] : array();
}

/**
 * Gets single registered recipient for notification type
 *
 * @since  5.0.0
 * @param  string $notification_type notification slug.
 * @param  string $recipient_slug    recipient slug.
 * @return mixed                     recipient object or false
 */
function notification_get_single_recipient( $notification_type, $recipient_slug ) {
	$recipients = notification_get_recipients();
	return isset( $recipients[ $notification_type ][ $recipient_slug ] ) ? $recipients[ $notification_type ][ $recipient_slug ] : false;
}

/**
 * Parses recipient raw value to values which can be used by notifications
 *
 * @since  5.0.0
 * @param  string $notification_slug   slug of notification.
 * @param  string $recipient_type      slug of recipient.
 * @param  mixed  $recipient_raw_value raw value.
 * @return mixed                       parsed value
 */
function notification_parse_recipient( $notification_slug, $recipient_type, $recipient_raw_value ) {

	$recipient = notification_get_single_recipient( $notification_slug, $recipient_type );

	if ( ! $recipient instanceof Interfaces\Receivable ) {
		return array();
	}

	return $recipient->parse_value( $recipient_raw_value );

}
