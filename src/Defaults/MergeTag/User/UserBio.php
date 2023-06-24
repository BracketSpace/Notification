<?php

/**
 * User Bio merge tag
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
 * User Bio merge tag class
 */
class UserBio extends StringTag
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
				'slug' => 'user_bio',
				'name' => __(
					'User bio',
					'notification'
				),
				'description' => __(
					'Developer based in Ontario, Canada',
					'notification'
				),
				'example' => true,
				'group' => __(
					'User',
					'notification'
				),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->description;
				},
			]
		);

		parent::__construct($args);
	}
}
