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

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Resolver;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Basic resolver
 */
class Basic extends Abstracts\Resolver
{

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
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return mixed               Resolved value
	 */
	public function resolve_merge_tag( $match, Triggerable $trigger )
	{

		$mergeTags = $trigger->get_merge_tags('all', true);
		$tagSlug = trim(str_replace([ '{', '}' ], '', $match[0]));

		if (! isset($mergeTags[$tagSlug])) {
			return $match[0];
		}

		return apply_filters(
			'notification/merge_tag/value/resolved',
			$mergeTags[$tagSlug]->resolve(),
			$mergeTags[$tagSlug]
		);
	}
}
