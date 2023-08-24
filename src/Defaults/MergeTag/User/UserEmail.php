<?php

/**
 * User Email merge tag
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object, preferably WP_User
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\User;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * User Email merge tag class
 */
class UserEmail extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		$this->setTriggerProp($params['property_name'] ?? 'user_object');

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'user_email',
				'name' => __('User email', 'notification'),
				'description' => __('john.doe@example.com', 'notification'),
				'example' => true,
				'group' => __('User', 'notification'),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->user_email;
				},
			]
		);

		parent::__construct($args);
	}
}
