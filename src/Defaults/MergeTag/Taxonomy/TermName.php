<?php

/**
 * Taxonomy term name merge tag
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
 * Taxonomy term name merge tag class
 */
class TermName extends StringTag
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
				'slug' => sprintf('%s_name', $this->getTriggerProp()),
				'name' => __('Term name', 'notification'),
				'description' => 'Nature',
				'example' => true,
				'group' => __('Term', 'notification'),
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->getTriggerProp() }->name;
				},
			]
		);

		parent::__construct($args);
	}
}
