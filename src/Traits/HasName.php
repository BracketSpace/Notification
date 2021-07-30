<?php
/**
 * Has Name Trait.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * HasName trait
 */
trait HasName {

	/**
	 * Human readable, translated name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Gets name
	 *
	 * @return string name
	 */
	public function get_name() {
		return $this->name;
	}

}
