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

	/**
	 * Registers Recipient
	 *
	 * @since  [Next]
	 * @param  string                $carrier_slug Carrier slug.
	 * @param  Interfaces\Receivable $recipient    Recipient object.
	 * @return Interfaces\Receivable
	 */
	public static function recipient( string $carrier_slug, Interfaces\Receivable $recipient ) {
		Store\Recipient::insert( $carrier_slug, $recipient->get_slug(), $recipient );
		do_action( 'notification/recipient/registered', $recipient, $carrier_slug );

		return $recipient;
	}

}
