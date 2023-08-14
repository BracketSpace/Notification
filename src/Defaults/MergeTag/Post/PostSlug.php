<?php

/**
 * Post slug merge tag
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
 * Post slug merge tag class
 */
class PostSlug extends StringTag
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
				'slug' => sprintf('%s_slug', $this->getTriggerProp()),
				// translators: singular post name.
				'name' => sprintf(__('%s slug', 'notification'), $postTypeName),
				'description' => __('hello-world', 'notification'),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->post_name;
				},
			]
		);

		parent::__construct($args);
	}
}
