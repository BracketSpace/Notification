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
class UserPasswordResetLink extends StringTag
{

	/**
	 * Trigger property to get the reset key from
	 *
	 * @var string
	 */
	protected $keyPropertyName = 'password_reset_key';

	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.2.2
	 */
	public function __construct($params = [])
	{

		if (isset($params['key_property_name']) && !empty($params['key_property_name'])) {
			$this->keyPropertyName = $params['key_property_name'];
		}

		$this->setTriggerProp($params['user_property_name'] ?? 'user_object');

		$args = wp_parse_args(
			[
				'slug' => 'user_password_reset_link',
				'name' => __(
					'Password reset link',
					'notification'
				),
				'description' => __(
					'http://example.com/wp-login.php?action=rp&key=mm2sAR8jmIyjSiMsCJRm&login=admin',
					'notification'
				),
				'example' => true,
				'group' => __(
					'User action',
					'notification'
				),
				'resolver' => function ($trigger) {
					return network_site_url(
						sprintf(
							'wp-login.php?action=rp&key=%s&login=%s',
							$trigger->{$this->keyPropertyName},
							$trigger->{$this->getTriggerProp()}->data->userLogin
						)
					);
				},
			]
		);

		parent::__construct($args);
	}
}
