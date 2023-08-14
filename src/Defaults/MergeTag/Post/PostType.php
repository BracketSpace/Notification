<?php

/**
 * Post type merge tag
 *
 * Requirements:
 * - Trigger property `post_type` with the post type slug
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post type merge tag class
 */
class PostType extends StringTag
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

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'post_type',
				'name' => __('Post Type', 'notification'),
				'description' => 'post',
				'example' => true,
				'group' => WpObjectHelper::getPostTypeName($this->getTriggerProp()),
				'resolver' => static function ($trigger) {
					return $trigger->postType;
				},
			]
		);

		parent::__construct($args);
	}
}
