<?php
/**
 * Triggerable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

/**
 * Triggerable interface
 */
interface Triggerable extends Nameable {

	/**
	 * Attaches the Notification to the Trigger
	 *
	 * @param  Sendable $notification Notification class.
	 * @return void
	 */
	public function attach( Sendable $notification );

	/**
	 * Detaches the Notification from the Trigger
	 *
	 * @param  Sendable $notification Notification class.
	 * @return void
	 */
	public function detach( Sendable $notification );

}
