<?php

/**
 * Post permalink merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post permalink merge tag class
 */
class PostPermalink extends UrlTag
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
					'%s_permalink',
					$this->getTriggerProp()
				),
				'name' => sprintf(
				// translators: singular post name.
					__(
						'%s permalink',
						'notification'
					),
					$postTypeName
				),
				'description' => __(
					'https://example.com/hello-world/',
					'notification'
				),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return get_permalink($trigger->{$this->getTriggerProp()}->ID);
				},
			]
		);

		parent::__construct($args);
	}
}
