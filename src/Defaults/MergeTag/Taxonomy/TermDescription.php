<?php

/**
 * Taxonomy term description merge tag
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
 * Taxonomy term description merge tag class
 */
class TermDescription extends StringTag
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
				'slug' => sprintf('%s_description', $this->getTriggerProp()),
				'name' => __('Term description', 'notification'),
				'description' => 'Lorem ipsum sit dolor amet',
				'example' => true,
				'group' => __('Term', 'notification'),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->description;
				},
			]
		);

		parent::__construct($args);
	}
}
