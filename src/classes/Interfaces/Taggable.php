<?php
/**
 * Taggable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

use BracketSpace\Notification\Interfaces\Nameable;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Taggable interface
 */
interface Taggable extends Nameable {

	/**
	 * Resolves the merge tag value
	 *
	 * @return mixed
	 */
	public function resolve();

	/**
	 * Gets merge tag resolved value
	 *
	 * @return mixed
	 */
	public function get_value();

	/**
	 * Cleans merge tag value
	 *
	 * @return void
	 */
	public function clean_value();

	/**
	 * Checks if merge tag is already resolved
	 *
	 * @return boolean
	 */
	public function is_resolved();

	/**
	 * Gets value type
	 *
	 * @return string
	 */
	public function get_value_type();

	/**
	 * Sets trigger object
	 *
	 * @param Triggerable $trigger Trigger object.
	 */
	public function set_trigger( Triggerable $trigger );

}
