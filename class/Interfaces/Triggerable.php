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
	 * Attaches the Carrier to the Trigger
	 *
	 * @param  Sendable $carrier Carrier class.
	 * @return void
	 */
	public function attach( Sendable $carrier );

	/**
	 * Detaches the Carrier from the Trigger
	 *
	 * @param  Sendable $carrier Carrier class.
	 * @return void
	 */
	public function detach( Sendable $carrier );

}
