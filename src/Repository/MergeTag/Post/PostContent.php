<?php

/**
 * Post content merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Post;

use BracketSpace\Notification\Repository\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post content merge tag class
 */
class PostContent extends StringTag
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
				'slug' => sprintf('%s_content', $params['post_type'] ?? 'post'),
				// translators: singular post name.
				'name' => sprintf(__('%s content', 'notification'), $postTypeName),
				'description' => __(
					'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!',
					'notification'
				),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return apply_filters('the_content', $trigger->{$this->getTriggerProp()}->post_content);
				},
			]
		);

		parent::__construct($args);
	}
}
