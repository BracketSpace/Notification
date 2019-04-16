<?php
/**
 * Resolver abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Resolver class
 */
abstract class Resolver implements Interfaces\Resolvable {

	/**
	 * Gets resolver slug
	 * Note: it's automatically generated from the class name.
	 *
	 * @since  [Next]
	 * @return string
	 */
	public function get_slug() {
		return sanitize_title_with_dashes( __CLASS__ );
	}

	/**
	 * Gets merge tag pattern
	 *
	 * @since  [Next]
	 * @return string
	 */
	public function get_pattern() {
		return static::PATTERN;
	}

	/**
	 * Gets resolver priority
	 *
	 * @since  [Next]
	 * @return int
	 */
	public function get_priority() {
		return static::PRIORITY;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array       $match   Match array.
	 * @param Triggerable $trigger Trigger object.
	 * @return string              Resolved value
	 */
	abstract public function resolve_merge_tag( $match, Triggerable $trigger );

}
