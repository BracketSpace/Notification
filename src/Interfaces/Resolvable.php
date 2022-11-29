<?php

/**
 * Resolvable interface class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Interfaces;

/**
 * Resolvable interface
 */
interface Resolvable
{

	/**
	 * Gets slug
	 *
	 * @return string Slug
	 */
	public function getSlug();

	/**
	 * Gets merge tag pattern
	 *
	 * @return string Pattern
	 */
	public function getPattern();

	/**
	 * Gets resolver priority
	 *
	 * @return int Priority
	 */
	public function getPriority();

	/**
	 * Resolves single matched merge tag
	 *
	 * @param array       $match   Match array.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return string              Resolved value
	 */
	public function resolveMergeTag( $match, Triggerable $trigger );
}
