<?php
/**
 * Has Slug Trait.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * HasSlug trait
 */
trait HasSlug {

	/**
	 * Object slug
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Gets slug
	 *
	 * @return string slug
	 */
	public function get_slug() {
		return $this->slug;
	}

}
