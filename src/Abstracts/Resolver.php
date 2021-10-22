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
	 * Resolver priority
	 * Higher number means later execution
	 */
	const PRIORITY = 100;

	/**
	 * Resolver pattern
	 */
	const PATTERN = '';

	/**
	 * Gets resolver slug
	 * Note: it's automatically generated from the class name.
	 *
	 * @since  6.0.0
	 * @return string
	 */
	public function get_slug() {
		$prepared = str_replace( '\\', '-', get_class( $this ) );
		$prepared = str_replace( 'BracketSpace-Notification-', '', $prepared );
		return sanitize_title_with_dashes( $prepared );
	}

	/**
	 * Gets merge tag pattern
	 *
	 * @since  6.0.0
	 * @return string
	 */
	public function get_pattern() {
		return static::PATTERN;
	}

	/**
	 * Gets resolver priority
	 *
	 * @since  6.0.0
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
