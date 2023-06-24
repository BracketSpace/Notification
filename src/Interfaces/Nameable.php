<?php

/**
 * Nameable interface class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Interfaces;

/**
 * Nameable interface
 */
interface Nameable
{
	/**
	 * Gets name
	 *
	 * @return string name
	 */
	public function getName();

	/**
	 * Gets slug
	 *
	 * @return string slug
	 */
	public function getSlug();
}
