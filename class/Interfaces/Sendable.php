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
	 * Sends the notification
	 *
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send( Triggerable $trigger );

	/**
	 * Generates an unique hash for notification instance
	 *
	 * @return string
	 */
	public function hash();

}
