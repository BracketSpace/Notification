<?php

/**
 * User login merge tag
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object, preferabely WP_User
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\User;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * User login merge tag class
 */
class UserLogin extends StringTag
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
				'slug' => 'user_login',
				'name' => __(
					'User login',
					'notification'
				),
				'description' => __(
					'johndoe',
					'notification'
				),
				'example' => true,
				'group' => __(
					'User',
					'notification'
				),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->userLogin;
				},
			]
		);

		parent::__construct($args);
	}
}
