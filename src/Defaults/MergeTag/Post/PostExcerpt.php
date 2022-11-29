<?php

/**
 * Post excerpt merge tag
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
 * Post excerpt merge tag class
 */
class PostExcerpt extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array $params merge tag configuration params.
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
					'%s_excerpt',
					$this->getTriggerProp()
				),
				// translators: singular post name.
				'name' => sprintf(
					__(
						'%s excerpt',
						'notification'
					),
					$postTypeName
				),
				'description' => __(
					'Welcome to WordPress...',
					'notification'
				),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return get_the_excerpt($trigger->{$this->getTriggerProp()});
				},
			]
		);

		parent::__construct($args);
	}
}
