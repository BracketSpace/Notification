<?php

/**
 * Post ID merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post ID merge tag class
 */
class PostID extends IntegerTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{

		$this->setTriggerProp($params['post_type'] ?? 'post');

		$postTypeName = WpObjectHelper::getPostTypeName($this->getTriggerProp());

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf(
					'%s_ID',
					$this->getTriggerProp()
				),
				'name' => sprintf(
				// translators: singular post name.
					__('%s ID', 'notification'),
					$postTypeName
				),
				'description' => '35',
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->ID;
				},
			]
		);

		parent::__construct($args);
	}
}
