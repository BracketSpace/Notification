<?php

declare(strict_types=1);

/**
 * Has Slug Trait.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * HasSlug trait
 */
trait HasSlug
{

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
	public function get_slug()
	{
		if ($this->slug === null) {
			return $this->get_class_slug();
		}

		return $this->slug;
	}

	/**
	 * Sets slug
	 *
	 * @param  string $slug Slug.
	 * @return $this
	 */
	public function set_slug( string $slug )
	{
		$this->slug = $slug;

		return $this;
	}
}
