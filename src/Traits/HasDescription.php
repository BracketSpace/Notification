<?php

/**
 * Has Description Trait.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

/**
 * HasDescription trait
 */
trait HasDescription
{
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
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Sets description
	 *
	 * @param string $description Description.
	 * @return $this
	 */
	public function setDescription(string $description)
	{
		$this->description = $description;

		return $this;
	}
}
