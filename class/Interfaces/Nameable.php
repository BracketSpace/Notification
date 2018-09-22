<?php
/**
 * Nameable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

/**
 * Nameable interface
 */
interface Nameable {

	/**
	 * Gets name
	 *
	 * @return string name
	 */
	public function get_name();

	/**
	 * Gets slug
	 *
	 * @return string slug
	 */
	public function get_slug();

}
