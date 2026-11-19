<?php
/**
 * Carrier class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Repository\Resolver\BaseResolver;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Resolver class
 */
class Resolver extends BaseResolver {

	/**
	 * Dummy resolver slug
	 *
	 * @since 6.3.0
	 * @return string
	 */
	public function getSlug() {
		return 'BracketSpace-Notification-Dummy';
	}

	/**
	 * Resolves single matched merge tag
	 *
	 * @param array       $match   Match array.
	 * @param Triggerable $trigger Trigger object.
	 * @return string
	 */
	public function resolveMergeTag( $match, Triggerable $trigger ) {
		return '';
	}

}
