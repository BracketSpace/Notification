<?php
/**
 * Sendable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

/**
 * Sendable interface
 */
interface Sendable extends Nameable {

	/**
	 * Sends the carrier
	 *
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send( Triggerable $trigger );

	/**
	 * Generates an unique hash for carrier instance
	 *
	 * @return string
	 */
	public function hash();

}
