<?php

/**
 * User nickname merge tag
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object, preferably WP_User
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\User;

use BracketSpace\Notification\Repository\MergeTag\StringTag;

/**
 * User nickname merge tag class
 */
class UserNickname extends StringTag
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
				'slug' => 'user_nickname',
				'name' => __('User nickname', 'notification'),
				'description' => __('jonny69', 'notification'),
				'example' => true,
				'group' => __('User', 'notification'),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->nickname;
				},
			]
		);

		parent::__construct($args);
	}
}
