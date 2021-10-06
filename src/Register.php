<?php
/**
 * Register class
 *
 * @package notification
 */

namespace BracketSpace\Notification;

/**
 * Register class
 */
class Register {

	/**
	 * Registers Carrier
	 *
	 * @since  [Next]
	 * @param  Interfaces\Sendable $carrier Carrier object.
	 * @return Interfaces\Sendable
	 */
	public static function carrier( Interfaces\Sendable $carrier ) {
		Store\Carrier::insert( $carrier->get_slug(), $carrier );
		do_action( 'notification/carrier/registered', $carrier );

		return $carrier;
	}

}
