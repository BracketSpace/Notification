<?php
/**
 * Adaptable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

use BracketSpace\Notification\Core\Notification;

/**
 * Adaptable interface
 *
 * @mixin Notification
 */
interface Adaptable {

	/**
	 * Reads the data
	 *
	 * @param mixed $input Input data.
	 * @return $this
	 */
	public function read( $input = null );

	/**
	 * Saves the data
	 *
	 * @return mixed
	 */
	public function save();

	/**
	 * Gets Notification object
	 *
	 * @return Notification
	 */
	public function get_notification();

}
