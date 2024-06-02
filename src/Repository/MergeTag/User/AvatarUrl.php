<?php

/**
 * Avatar url merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\User;

use BracketSpace\Notification\Repository\MergeTag\UrlTag;

/**
 * Avatar Url merge tag class
 */
class AvatarUrl extends UrlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.0.0
	 */
	public function __construct(array $params = [])
	{
		$this->setTriggerProp($params['property_name'] ?? 'user_object');

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'user_avatar_url',
				'name' => __('User avatar url', 'notification'),
				'description' => __(
					'http://0.gravatar.com/avatar/320eab812ab24ef3dbaa2e6dc6e024e0?s=96&d=mm&r=g',
					'notification'
				),
				'example' => true,
				'group' => __('User', 'notification'),
				'resolver' => function ($trigger) {
					if (isset($trigger->{$this->getTriggerProp()}->user_email)) {
						return get_avatar_url($trigger->{$this->getTriggerProp()}->user_email);
					}

					return '';
				},
			]
		);

		parent::__construct($args);
	}
}
