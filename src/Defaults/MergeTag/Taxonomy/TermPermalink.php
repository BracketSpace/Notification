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

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;

/**
 * Taxonomy term permalink merge tag class
 */
class TermPermalink extends UrlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 * @param array<mixed> $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		$this->setTriggerProp($params['property_name'] ?? 'term');

		$args = wp_parse_args(
			[
				'slug' => sprintf('%s_link', $this->getTriggerProp()),
				'name' => __('Term link', 'notification'),
				'description' => 'http://example.com/category/nature',
				'example' => true,
				'group' => __('Term', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->termPermalink;
				},
			]
		);

		parent::__construct($args);
	}
}
