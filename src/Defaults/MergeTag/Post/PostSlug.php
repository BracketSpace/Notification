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
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		$this->setTriggerProp($params['post_type'] ?? 'post');

		$postTypeName = WpObjectHelper::get_post_type_name($this->getTriggerProp());

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf('%s_slug', $this->getTriggerProp()),
				// translators: singular post name.
				'name' => sprintf(__('%s slug', 'notification'), $postTypeName),
				'description' => __('hello-world', 'notification'),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->getTriggerProp() }->postName;
				},
			]
		);

		parent::__construct($args);
	}
}
