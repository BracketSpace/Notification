<?php
/**
 * Basic resolver
 * Resolves Merge Tag values as is
 * Pattern match examples:
 * - `{value}`
 * - `{another_value}`
 * - `{another-value}`
 * - `{nested_this_is_not_captured {this_is_captured} tags}`
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
	const PATTERN = '/(?<!\!)\{(?:[^{}])*\}/';

	/**
	 * {@inheritdoc}
	 *
	 * @param array       $match   Match array.
	 * @param Triggerable $trigger Trigger object.
	 * @return mixed               Resolved value
	 */
	public function resolve_merge_tag( $match, Triggerable $trigger ) {

		$merge_tags = $trigger->get_merge_tags( 'all', true );
		$tag_slug   = trim( str_replace( [ '{', '}' ], '', $match[0] ) );

		if ( ! isset( $merge_tags[ $tag_slug ] ) ) {
			return $match[0];
		}

		$resolved = apply_filters(
			'notification/merge_tag/value/resolved',
			$merge_tags[ $tag_slug ]->resolve(),
			$merge_tags[ $tag_slug ]
		);

		return $resolved;

	}

}
