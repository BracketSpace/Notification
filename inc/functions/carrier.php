<?php
/**
 * Carrier functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;

/**
 * Registers Carrier
 *
 * @uses notification/carriers filter
 * @since  6.0.0
 * @param  Interfaces\Sendable $carrier Carrier object.
 * @return void
 */
function notification_register_carrier( Interfaces\Sendable $carrier ) {

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
 * Gets all registered Carriers
 *
 * @since  6.0.0
 * @return array carriers
 */
function notification_get_carriers() {
	return apply_filters( 'notification/carriers', [] );
}

/**
 * Gets single registered Carrier
 *
 * @since  6.0.0
 * @param  string $carrier_slug Carrier slug.
 * @return mixed                Carrier object or false
 */
function notification_get_carrier( $carrier_slug ) {
	$carriers = notification_get_carriers();
	return isset( $carriers[ $carrier_slug ] ) ? $carriers[ $carrier_slug ] : false;
}
