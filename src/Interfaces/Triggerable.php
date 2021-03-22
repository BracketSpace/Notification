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

	/**
	 * Sets up the merge tags
	 *
	 * @return void
	 */
	public function setup_merge_tags();

	/**
	 * Gets Trigger's merge tags
	 *
	 * @param string $type    Optional, all|visible|hidden, default: all.
	 * @param bool   $grouped Optional, default: false.
	 * @return array<Taggable>
	 */
	public function get_merge_tags( $type = 'all', $grouped = false );

}
