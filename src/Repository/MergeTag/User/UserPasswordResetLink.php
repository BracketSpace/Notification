<?php

/**
 * User login merge tag
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object, preferably WP_User
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\User;

use BracketSpace\Notification\Repository\MergeTag\UrlTag;

/**
 * User login merge tag class
 */
class UserPasswordResetLink extends UrlTag
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
	 * @param array{slug?: string, name?: string, property_name?: string, group?: string|null, description?: string,
	 *               example?: bool|string, resolver?: callable} $params
	 *        merge tag configuration params.
	 * @since 5.2.2
	 */
	public function __construct($params = [])
	{
		if (isset($params['key_property_name']) && !empty($params['key_property_name'])) {
			$this->keyPropertyName = $params['key_property_name'];
		}

		$this->setTriggerProp($params['property_name'] ?? 'user_object');

		$args = wp_parse_args(
			[
				'slug' => 'user_password_reset_link',
				'name' => __('Password reset link', 'notification'),
				'description' => __(
					'http://example.com/wp-login.php?action=rp&key=mm2sAR8jmIyjSiMsCJRm&login=admin',
					'notification'
				),
				'example' => true,
				'group' => __('User action', 'notification'),
				'resolver' => function ($trigger) {
					$user = $trigger->{$this->getTriggerProp()};
					// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					$userLogin = $user->user_login ?? '';

					// Defensive check: ensure user_login is valid
					// If user_login is empty or corrupted, use the user_email as fallback
					// when the user was likely registered with an email address
					// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					if (empty($userLogin) && !empty($user->user_email) && is_email($user->user_email)) {
						// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						$userLogin = $user->user_email;
					}

					// WordPress sanitizes usernames, removing special characters like @ and spaces
					// For password reset links, we need the original unsanitized value
					// If the original user_login would be different after sanitization, use email instead
					// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					if (
						!empty($userLogin) && sanitize_user($userLogin) !== $userLogin &&
						// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						!empty($user->user_email) && is_email($user->user_email)
					) {
						// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						$userLogin = $user->user_email;
					}

					return network_site_url(
						sprintf(
							'wp-login.php?action=rp&key=%s&login=%s',
							$trigger->{$this->keyPropertyName},
							rawurlencode($userLogin)
						),
						'login'
					);
				},
			]
		);

		parent::__construct($args);
	}
}
