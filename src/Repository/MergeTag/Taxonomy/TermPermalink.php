<?php

/**
 * Taxonomy term permalink merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Taxonomy;

use BracketSpace\Notification\Repository\MergeTag\UrlTag;

/**
 * Taxonomy term permalink merge tag class
 */
class TermPermalink extends UrlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.2.2
	 */
	public function __construct($params = [])
	{
		$this->setTriggerProp($params['property_name'] ?? 'term');

		$args = wp_parse_args(
			[
				'slug' => sprintf('%s_link', $this->getTriggerProp()),
				'name' => __('Term link', 'notification'),
				'description' => 'http://example.com/category/nature',
				'example' => true,
				'group' => __('Term', 'notification'),
				'resolver' => static function ($trigger) {
					// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					return $trigger->term_permalink;
				},
			]
		);

		parent::__construct($args);
	}
}
