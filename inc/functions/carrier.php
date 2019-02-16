<?php
/**
 * Carrier functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;

/**
 * Registers carrier
 * Uses notification/carriers filter
 *
 * @since  [Next] Changed naming convention from Notification to Carrier.
 * @param  Interfaces\Sendable $carrier Carrier object.
 * @return void
 */
function register_notification( Interfaces\Sendable $carrier ) {

	add_filter( 'notification/carriers', function( $carriers ) use ( $carrier ) {

		if ( isset( $carriers[ $carrier->get_slug() ] ) ) {
			throw new \Exception( 'Carrier with that slug already exists' );
		} else {
			$carriers[ $carrier->get_slug() ] = $carrier;
		}

		return $carriers;

	} );

	do_action( 'notification/carrier/registered', $carrier );

}

/**
 * Gets all registered carriers
 *
 * @since  5.0.0
 * @since  [Next] Changed naming convention from Notification to Carrier.
 * @return array carriers
 */
function notification_get_notifications() {
	return apply_filters( 'notification/carriers', [] );
}

/**
 * Gets single registered carrier
 *
 * @since  5.0.0
 * @since  [Next] Changed naming convention from Notification to Carrier.
 * @param  string $carrier_slug Carrier slug.
 * @return mixed                     Carrier object or false
 */
function notification_get_single_notification( $carrier_slug ) {
	$carriers = notification_get_notifications();
	return isset( $carriers[ $carrier_slug ] ) ? $carriers[ $carrier_slug ] : false;
}
