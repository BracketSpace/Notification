<?php

/**
 * Post title merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post title merge tag class
 */
class PostTitle extends StringTag
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
				'slug' => sprintf('%s_title', $this->getTriggerProp()),
				// translators: singular post name.
				'name' => sprintf(__('%s title', 'notification'), $postTypeName),
				'description' => __('Hello World', 'notification'),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return html_entity_decode(get_the_title($trigger->{$this->getTriggerProp()}));
				},
			]
		);

		parent::__construct($args);
	}
}
