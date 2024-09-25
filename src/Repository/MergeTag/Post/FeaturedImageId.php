<?php

/**
 * Post featured image ID merge tag
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
 * Post featured image id merge tag class
 */
class FeaturedImageId extends IntegerTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params Merge tag configuration params.
	 * @since 6.0.0
	 */
	public function __construct($params = [])
	{
		$this->setTriggerProp($params['property_name'] ?? 'post');

		$postTypeName = WpObjectHelper::getPostTypeName($params['post_type'] ?? 'post');

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf('%s_featured_image_id', $params['post_type'] ?? 'post'),
				// translators: singular post name.
				'name' => sprintf(__('%s featured image id', 'notification'), $postTypeName),
				'description' => __('123', 'notification'),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					$postId = $trigger->{$this->getTriggerProp()}->ID;

					return (int)get_post_thumbnail_id($postId);
				},
			]
		);

		parent::__construct($args);
	}
}
