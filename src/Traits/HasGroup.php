<?php
/**
 * Has Group Trait.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * HasGroup trait
 */
trait HasGroup {

	/**
	 * Human readable, translated group name
	 *
	 * @var string
	 */
	protected $group;

	/**
	 * Gets group
	 *
	 * @return string|null Group name
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * Sets group
	 *
	 * @param  string $group Group name.
	 * @return $this
	 */
	public function set_group( string $group ) {
		$this->group = $group;

		return $this;
	}

}
