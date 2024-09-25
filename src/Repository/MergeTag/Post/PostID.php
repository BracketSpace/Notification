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

namespace BracketSpace\Notification\Repository\MergeTag\Post;

use BracketSpace\Notification\Repository\MergeTag\IntegerTag;
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
		$this->setTriggerProp($params['property_name'] ?? 'post');

		$postTypeName = WpObjectHelper::getPostTypeName($params['post_type'] ?? 'post');

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf('%s_ID', $params['post_type'] ?? 'post'),
				// translators: singular post name.
				'name' => sprintf(__('%s ID', 'notification'), $postTypeName),
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
