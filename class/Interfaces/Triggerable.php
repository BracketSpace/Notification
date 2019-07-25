<?php
/**
 * Triggerable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

use BracketSpace\Notification\Core\Notification;

/**
 * Triggerable interface
 */
interface Triggerable extends Nameable {

	/**
	 * Attaches the Notification to the Trigger
	 *
	 * @param  Notification $notification Notification class.
	 * @return void
	 */
	public function attach( Notification $notification );

	/**
	 * Detaches the Notification from the Trigger
	 *
	 * @param  Notification $notification Notification class.
	 * @return void
	 */
	public function detach( Notification $notification );

}
