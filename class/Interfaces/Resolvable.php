<?php
/**
 * Resolvable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

/**
 * Resolvable interface
 */
interface Resolvable {

	/**
	 * Gets slug
	 *
	 * @return string Slug
	 */
	public function get_slug();

	/**
	 * Gets merge tag pattern
	 *
	 * @return string Pattern
	 */
	public function get_pattern();

	/**
	 * Gets resolver priority
	 *
	 * @return int Priority
	 */
	public function get_priority();

	/**
	 * Resolves single matched merge tag
	 *
	 * @param array       $match   Match array.
	 * @param Triggerable $trigger Trigger object.
	 * @return string              Resolved value
	 */
	public function resolve_merge_tag( $match, Triggerable $trigger );

}
