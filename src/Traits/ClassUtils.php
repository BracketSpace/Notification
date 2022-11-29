<?php

/**
 * Class Utils Trait.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

/**
 * ClassUtils trait
 */
trait ClassUtils
{

	/**
	 * Get short class name without namespace
	 *
	 * @return string
	 */
	public function get_short_class_name()
	{
		return ( new \ReflectionClass($this) )->getShortName();
	}

	/**
	 * Get nice class names with title case and spaces
	 *
	 * @return string
	 */
	public function get_nice_class_name()
	{
		return (string)preg_replace('/(.)(?=[A-Z])/u', '$1 ', $this->getShortClassName());
	}

	/**
	 * Get class slug with dash separators
	 *
	 * @return string
	 */
	public function get_class_slug()
	{
		return strtolower((string)preg_replace('/(.)(?=[A-Z])/u', '$1-', $this->getShortClassName()));
	}
}
