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

	/**
	 * Clears the merge tags
	 *
	 * @return $this
	 */
	public function clear_merge_tags();

	/**
	 * Stops the trigger.
	 *
	 * @return void
	 */
	public function stop();

	/**
	 * Checks if trigger has been stopped
	 *
	 * @return boolean
	 */
	public function is_stopped() : bool;

	/**
	 * Gets Trigger actions
	 *
	 * @since 8.0.0
	 * @return array<int, array{tag: string, priority: int, accepted_args: int}>
	 */
	public function get_actions() : array;

	/**
	 * Gets group
	 *
	 * @return string|null
	 */
	public function get_group();
}
