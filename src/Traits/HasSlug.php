<?php

/**
 * Has Slug Trait.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;

/**
 * HasSlug trait
 */
trait HasSlug
{
	use Casegnostic;

	/**
	 * Object slug
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Gets slug
	 *
	 * If the slug is not set, automatically generated
	 * one is used with words separated by `-`.
	 *
	 * @return string slug
	 */
	public function getSlug()
	{
		if ($this->slug === null) {
			return $this->getClassSlug();
		}

		return $this->slug;
	}

	/**
	 * Sets slug
	 *
	 * @param string $slug Slug.
	 * @return $this
	 */
	public function setSlug(string $slug)
	{
		$this->slug = $slug;

		return $this;
	}
}
