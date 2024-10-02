<?php

/**
 * Post featured image url merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Post;

use BracketSpace\Notification\Repository\MergeTag\UrlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post featured image url merge tag class
 */
class FeaturedImageUrl extends UrlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 6.0.0
	 */
	public function __construct($params = [])
	{
		$this->setTriggerProp($params['property_name'] ?? 'post');

		$postTypeName = WpObjectHelper::getPostTypeName($params['post_type'] ?? 'post');

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf('%s_featured_image_url', $params['post_type'] ?? 'post'),
				// translators: singular post name.
				'name' => sprintf(__('%s featured image url', 'notification'), $postTypeName),
				'description' => __('https://example.com/wp-content/2019/01/image.jpg', 'notification'),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return wp_get_attachment_image_url(
						get_post_thumbnail_id($trigger->{$this->getTriggerProp()}->ID),
						'full'
					);
				},
			]
		);

		parent::__construct($args);
	}
}
