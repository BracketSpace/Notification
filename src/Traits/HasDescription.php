<?php
/**
 * Has Description Trait.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * HasDescription trait
 */
trait HasDescription {

	/**
	 * Human readable, translated description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Gets description
	 *
	 * @return string|null Description
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Sets description
	 *
	 * @param  string $description Description.
	 * @return $this
	 */
	public function set_description( string $description ) {
		$this->description = $description;

		return $this;
	}

}
