<?php

/**
 * Has Group Trait.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

/**
 * HasGroup trait
 */
trait HasGroup
{
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
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * Sets group
	 *
	 * @param string $group Group name.
	 * @return $this
	 */
	public function setGroup(string $group)
	{
		$this->group = $group;

		return $this;
	}
}
