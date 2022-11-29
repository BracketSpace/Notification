<?php

/**
 * Taxonomy slug merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Taxonomy slug merge tag class
 */
class TaxonomySlug extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 * @param array<mixed> $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		$this->setTriggerProp($params['property_name'] ?? 'taxonomy');

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf('%s_slug', $params['tag_name'] ?? 'taxonomy'),
				'name' => __('Taxonomy slug', 'notification'),
				'description' => __('hello-world', 'notification'),
				'example' => true,
				'group' => __('Taxonomy', 'notification'),
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->getTriggerProp() }->name ?? '';
				},
			]
		);

		parent::__construct($args);
	}
}
