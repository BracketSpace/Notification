<?php
/**
 * Basic resolver
 * Resolves Merge Tag values as is
 * Pattern match examples:
 * - `{value}`
 * - `{another_value}`
 * - `{another-value}`
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Resolver;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Basic resolver
 */
class Basic extends Abstracts\Resolver {

	/**
	 * Resolver priority
	 * Higher number means later execution
	 */
	const PRIORITY = 100;

	/**
	 * Resolver pattern
	 */
	const PATTERN = '/\{([^\}]*)\}/';

	/**
	 * {@inheritdoc}
	 *
	 * @param array       $match   Match array.
	 * @param Triggerable $trigger Trigger object.
	 * @return mixed               Resolved value
	 */
	public function resolve_merge_tag( $match, Triggerable $trigger ) {

		$merge_tags = $trigger->get_merge_tags( 'all', true );
		$tag_slug   = $match[1];

		$strip_merge_tags = notification_get_setting( 'general/content/strip_empty_tags' );
		$strip_merge_tags = apply_filters_deprecated( 'notification/value/strip_empty_mergetags', [
			$strip_merge_tags,
		], '[Next]', 'notification/resolve/strip_empty_mergetags' );
		$strip_merge_tags = apply_filters( 'notification/resolve/strip_empty_mergetags', $strip_merge_tags );

		if ( ! isset( $merge_tags[ $tag_slug ] ) ) {
			return $strip_merge_tags ? '' : $match[0];
		}

		$resolved = apply_filters_deprecated( 'notificaiton/merge_tag/value/resolved', [
			$merge_tags[ $tag_slug ]->resolve(),
			$merge_tags[ $tag_slug ],
		], '[Next]', 'notification/merge_tag/value/resolved' );
		$resolved = apply_filters( 'notification/merge_tag/value/resolved', $resolved, $merge_tags[ $tag_slug ] );

		return $resolved;

	}

}