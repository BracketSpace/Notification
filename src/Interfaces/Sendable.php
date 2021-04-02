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

	/**
	 * Gets form fields array
	 *
	 * @return Fillable[] fields
	 */
	public function get_form_fields();

	/**
	 * Checks if Carrier is enabled
	 *
	 * @return bool
	 */
	public function is_enabled();

	/**
	 * Checks if Carrier is active
	 *
	 * @return bool
	 */
	public function is_active();

}
