<?php

/**
 * Taxonomy name merge tag
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
 * Taxonomy name merge tag class
 */
class TaxonomyName extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.2.2
	 */
	public function __construct($params = [])
	{

		$this->setTriggerProp($params['property_name'] ?? 'taxonomy');

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf(
					'%s_name',
					$params['tag_name'] ?? 'taxonomy'
				),
				'name' => __('Taxonomy name', 'notification'),
				'description' => __('Hello World', 'notification'),
				'example' => true,
				'group' => __('Taxonomy', 'notification'),
				'resolver' => function ($trigger) {

					return $trigger->{$this->getTriggerProp()}->labels->singular_name ?? '';
				},
			]
		);

		parent::__construct($args);
	}
}
