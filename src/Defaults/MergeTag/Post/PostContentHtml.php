<?php

/**
 * Post content HTMl merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\HtmlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post content HTML merge tag class
 */
class PostContentHtml extends HtmlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.2.4
	 */
	public function __construct($params = [])
	{

		$this->setTriggerProp($params['post_type'] ?? 'post');

		$postTypeName = WpObjectHelper::getPostTypeName($this->getTriggerProp());

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf(
					'%s_content_html',
					$this->getTriggerProp()
				),
				'name' => sprintf(
				// translators: singular post name.
					__(
						'%s content HTML',
						'notification'
					),
					$postTypeName
				),
				'description' => __(
					'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!',
					'notification'
				),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					return apply_filters(
						'the_content',
						$trigger->{$this->getTriggerProp()}->postContent
					);
				},
			]
		);

		parent::__construct($args);
	}
}
