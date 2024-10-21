<?php

/**
 * User registered trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Core\Debugging;
use BracketSpace\Notification\Repository\MergeTag;

/**
 * User registered trigger class
 */
class UserRegistered extends UserTrigger
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/registered',
			__('User registration', 'notification')
		);

		$this->addAction('user_register', 1000);

		$this->setDescription(
			__('Fires when user registers new account', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId User ID.
	 * @return void
	 */
	public function context($userId)
	{
		$this->userId = $userId;

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return;
		}

		$this->userObject = $user;
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		parent::mergeTags();

		$this->addMergeTag(
			new MergeTag\UrlTag(
				[
					'slug' => 'user_password_setup_link',
					'name' => __('User password setup link', 'notification'),
					'description' => network_site_url(
						'wp-login.php?action=rp&key=37f62f1363b04df4370753037853fe88&login=userlogin',
						'login'
					) . "\n" .
						__(
							'After using this Merge Tag, no other password setup links will work.',
							'notification'
						),
					'example' => true,
					'resolver' => static function ($trigger) {
						return network_site_url(
							sprintf(
								'wp-login.php?action=rp&key=%s&login=%s',
								$trigger->getPasswordResetKey(),
								rawurlencode($trigger->userObject->user_login)
							),
							'login'
						);
					},
					'group' => __('User', 'notification'),
				]
			)
		);
	}

	/**
	 * Gets password reset key
	 *
	 * @return string
	 * @since  5.1.5
	 */
	public function getPasswordResetKey()
	{
		add_filter(
			'allow_password_reset',
			'__return_true',
			999999999
		);
		add_filter(
			'notification/trigger/wordpress/user_password_reset_request/bail_for_registration',
			'__return_true',
			999999999
		);
		$resetKey = get_password_reset_key($this->userObject);
		remove_filter(
			'allow_password_reset',
			'__return_true',
			999999999
		);
		remove_filter(
			'notification/trigger/wordpress/user_password_reset_request/bail_for_registration',
			'__return_true',
			999999999
		);

		if (is_wp_error($resetKey)) {
			Debugging::log(
				'Core',
				'error',
				'User registration trigger error: ' . $resetKey->get_error_message()
			);
			return '';
		}

		return $resetKey;
	}
}
