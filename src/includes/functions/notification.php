<?php
/**
 * Notification functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Defaults\Store\Notification as NotificationStore;
use BracketSpace\Notification\Interfaces;

/**
 * Creates new Notification from array
 *
 * Accepts both array with Trigger and Carriers objects or static values.
 *
 * @since  6.0.0
 * @param  array $data Notification data.
 * @return \WP_Error | true
 */
function notification( $data = [] ) {

	try {
		notification_add( new Notification( notification_convert_data( $data ) ) );
	} catch ( \Exception $e ) {
		return new \WP_Error( 'notification_error', $e->getMessage() );
	}

	return true;

}

/**
 * Adds Notification to Store
 *
 * @since  6.0.0
 * @param  Notification $notification Notification object.
 * @return \WP_Error | true
 */
function notification_add( Notification $notification ) {

	$store = new NotificationStore();

	try {
		$store[ $notification->get_hash() ] = $notification;
	} catch ( \Exception $e ) {
		return new \WP_Error( 'notification_add_error', $e->getMessage() );
	}

	do_action( 'notification/notification/registered', $notification );

	return true;

}

/**
 * Converts the static data to Trigger and Carrier objects
 *
 * If no `trigger` nor `carriers` keys are available it does nothing.
 * If the data is already in form of objects it does nothing.
 *
 * @since  6.0.0
 * @param  array $data Notification static data.
 * @return array       Converted data.
 */
function notification_convert_data( $data = [] ) {

	// Trigger conversion.
	if ( isset( $data['trigger'] ) && ! ( $data['trigger'] instanceof Interfaces\Triggerable ) ) {
		$data['trigger'] = notification_get_trigger( $data['trigger'] );
	}

	// Carriers conversion.
	if ( isset( $data['carriers'] ) ) {
		$carriers = [];

		foreach ( $data['carriers'] as $carrier_slug => $carrier_data ) {
			if ( $carrier_data instanceof Interfaces\Sendable ) {
				$carriers[ $carrier_slug ] = $carrier_data;
				continue;
			}

			$registered_carrier = notification_get_carrier( $carrier_slug );

			if ( ! empty( $registered_carrier ) ) {
				$carrier = clone $registered_carrier;
				$carrier->set_data( $carrier_data );
				$carriers[ $carrier_slug ] = $carrier;
			}
		}

		$data['carriers'] = $carriers;
	}

	return $data;

}
