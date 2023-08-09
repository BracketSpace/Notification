<?php

/**
 * User role merge tag
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
 * User role merge tag class
 */
class UserRole extends StringTag
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
				'slug' => 'user_role',
				'name' => __('User role', 'notification'),
				'description' => __('Subscriber', 'notification'),
				'example' => true,
				'group' => __('User', 'notification'),
				'resolver' => function () {
					$roles = array_map(
						static function ($role) {
							$roleObject = get_role($role);
							return translate_user_role(ucfirst($roleObject->name));
						},
						$this->trigger->{$this->getTriggerProp()}->roles
					);

					return implode(', ', $roles);
				},
			]
		);

		parent::__construct($args);
	}
}
