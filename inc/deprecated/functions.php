<?php
/**
 * Deprecated functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;

/**
 * Checks if notification post has been just started
 *
 * @since  5.0.0
 * @deprecated [Next] Changed name for consistency.
 * @param  mixed $post Post ID or WP_Post.
 * @return boolean     True if notification has been just started
 */
function notification_is_new_notification( $post ) {
	_deprecated_function( 'notification_is_new_notification', '[Next]', 'notification_post_is_new' );
	return notification_post_is_new( $post );
}

/**
 * Registers notification
 *
 * @deprecated [Next] Changed name for consistency.
 * @param  Interfaces\Sendable $notification Carrier object.
 * @return void
 */
function register_notification( Interfaces\Sendable $notification ) {
	_deprecated_function( 'register_notification', '[Next]', 'notification_register_carrier' );
	notification_register_carrier( $notification );
}

/**
 * Gets all registered notifications
 *
 * @since  5.0.0
 * @deprecated [Next] Changed name for consistency.
 * @return array notifications
 */
function notification_get_notifications() {
	_deprecated_function( 'notification_get_notifications', '[Next]', 'notification_get_carriers' );
	return notification_get_carriers();
}

/**
 * Gets single registered notification
 *
 * @deprecated [Next] Changed name for consistency.
 * @param  string $notification_slug notification slug.
 * @return mixed                     notification object or false
 */
function notification_get_single_notification( $notification_slug ) {
	_deprecated_function( 'notification_get_single_notification', '[Next]', 'notification_get_carrier' );
	return notification_get_carrier( $notification_slug );
}

/**
 * Registers trigger
 * Uses notification/triggers filter
 *
 * @deprecated [Next] Changed name for consistency.
 * @param  Interfaces\Triggerable $trigger trigger object.
 * @return void
 */
function register_trigger( Interfaces\Triggerable $trigger ) {
	_deprecated_function( 'register_trigger', '[Next]', 'notification_register_trigger' );
	notification_register_trigger( $trigger );
}

/**
 * Gets single registered recipient for notification type
 *
 * @since  5.0.0
 * @deprecated [Next] Changed name for consistency.
 * @param  string $carrier_slug   Carrier slug.
 * @param  string $recipient_slug Recipient slug.
 * @return mixed                  Recipient object or false
 */
function notification_get_single_recipient( $carrier_slug, $recipient_slug ) {
	_deprecated_function( 'notification_get_single_recipient', '[Next]', 'notification_get_recipient' );
	return notification_get_recipient( $carrier_slug, $recipient_slug );
}

/**
 * Gets register recipients for notification type
 *
 * @since  5.0.0
 * @deprecated [Next] Changed name for consistency.
 * @param  string $carrier_slug Carrier slug.
 * @return array                Recipients array
 */
function notification_get_notification_recipients( $carrier_slug ) {
	_deprecated_function( 'notification_get_notification_recipients', '[Next]', 'notification_get_carrier_recipients' );
	return notification_get_carrier_recipients( $carrier_slug );
}

/**
 * Gets single registered trigger
 *
 * @since  5.0.0
 * @deprecated [Next] Changed name for consistency.
 * @param  string $trigger_slug trigger slug.
 * @return mixed                trigger object or false
 */
function notification_get_single_trigger( $trigger_slug ) {
	_deprecated_function( 'notification_get_single_trigger', '[Next]', 'notification_get_trigger' );
	return notification_get_trigger( $trigger_slug );
}
