<?php

/**
 * Taxonomy term ID merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;

/**
 * Taxonomy term ID merge tag class
 */
class TermID extends IntegerTag
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
				'slug' => sprintf('%s_ID', $this->getTriggerProp()),
				'name' => __('Term ID', 'notification'),
				'description' => '35',
				'example' => true,
				'group' => __('Term', 'notification'),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->term_id;
				},
			]
		);

		parent::__construct($args);
	}
}
