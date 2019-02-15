<?php
/**
 * Adaptable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

/**
 * Adaptable interface
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

}
