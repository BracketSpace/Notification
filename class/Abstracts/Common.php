<?php
/**
 * Common abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;

/**
 * Common class
 * Forces usage of slug and name and provides helper methods
 */
abstract class Common implements Interfaces\Nameable {

	/**
	 * Object slug
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Human readable, translated name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Gets slug
	 *
	 * @return string slug
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Gets name
	 *
	 * @return string name
	 */
	public function get_name() {
		return $this->name;
	}

}
