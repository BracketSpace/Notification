<?php

/**
 * Has Name Trait.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;

/**
 * HasName trait
 */
trait HasName
{
	use Casegnostic;

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
	public function getName()
	{
		if ($this->name === null) {
			return $this->getNiceClassName();
		}

		return $this->name;
	}

	/**
	 * Sets name
	 *
	 * @param string $name Name.
	 * @return $this
	 */
	public function setName(string $name)
	{
		$this->name = $name;

		return $this;
	}
}
