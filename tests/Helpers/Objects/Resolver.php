<?php
/**
 * Carrier class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Abstracts\Resolver as AbstractResolver;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Resolver class
 */
class Resolver extends AbstractResolver {

	/**
	 * Dummy resolver slug
	 *
	 * @since [Next]
	 * @return string
	 */
	public function get_slug() {
		return 'BracketSpace-Notification-Dummy';
	}

	/**
	 * Gets merge tag pattern
	 *
	 * @return string Pattern
	 */
	public function get_pattern(){

	}

	/**
	 * Gets resolver priority
	 *
	 * @return int Priority
	 */
	public function get_priority(){

	}

	/**
	 * Resolves single matched merge tag
	 *
	 * @param array       $match   Match array.
	 * @param Triggerable $trigger Trigger object.
	 * @return string              Resolved value
	 */
	public function resolve_merge_tag( $match, Triggerable $trigger ){
		return;
	}

}
