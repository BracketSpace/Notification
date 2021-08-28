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
	 * If the name is not set, automatically generated
	 * one is used with title case and spaces.
	 *
	 * @return string name
	 */
	public function get_name() {
		if ( null === $this->name ) {
			return $this->get_nice_class_name();
		}

		return $this->name;
	}

	/**
	 * Sets name
	 *
	 * @param  string $name Name.
	 * @return $this
	 */
	public function set_name( string $name ) {
		$this->name = $name;

		return $this;
	}

}
