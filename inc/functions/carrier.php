<?php
/**
 * Carrier functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Defaults\Store\Carrier as CarrierStore;
/**
 * Registers Carrier
 *
 * @since  [next] Use Carrier Store.
 * @param  Interfaces\Sendable $carrier Carrier object.
 * @return \WP_Error | true
 */
function notification_register_carrier( Interfaces\Sendable $carrier ) {

	$store = new CarrierStore();

	try {
		$store[] = $carrier;
	} catch ( \Exception $e ) {
		return new \WP_Error( 'notification_register_carrier_error', $e->getMessage() );
	}

}

/**
 * Gets all registered Carriers
 *
 * @since  [next] Use Carrier Store.
 * @return array carriers
 */
function notification_get_carriers() {
	$store = new CarrierStore();

	return $store->get_items();
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
