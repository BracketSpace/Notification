<?php

/**
 * User password change requested trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag;

/**
 * User password change requested trigger class
 */
class UserPasswordResetRequest extends UserTrigger
{
	/**
	 * Password reset key
	 *
	 * @var string
	 */
	public $passwordResetKey;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/password_reset_request',
			__('User password reset request', 'notification')
		);

		$this->addAction('retrieve_password_key', 10, 2);

		$this->setDescription(
			__('Fires when user requests password change', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $username username.
	 * @param string $resetKey password reset key.
	 * @return mixed
	 */
	public function context($username, $resetKey)
	{
		$user = get_user_by('login', $username);

		if (!$user instanceof \WP_User) {
			return false;
		}

		/**
		 * Bail if we are handling the registration.
		 * Use the filter to integrate with 3rd party code.
		 */
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (
			(isset($_REQUEST['action']) && $_REQUEST['action'] === 'register') ||
			apply_filters(
				'notification/trigger/wordpress/user_password_reset_request/bail_for_registration',
				false,
				$user
			)
		) {
			return false;
		}

		$this->userId = $user->ID;
		$this->userObject = $user;
		$this->passwordResetKey = $resetKey;
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		parent::mergeTags();

		$this->addMergeTag(new MergeTag\User\UserNicename());
		$this->addMergeTag(new MergeTag\User\UserDisplayName());
		$this->addMergeTag(new MergeTag\User\UserFirstName());
		$this->addMergeTag(new MergeTag\User\UserLastName());
		$this->addMergeTag(new MergeTag\User\UserPasswordResetLink());
		$this->addMergeTag(new MergeTag\User\UserBio());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'password_reset_request_datetime',
					'name' => __('Password reset request date', 'notification'),
					'timestamp' => static function () {
						return time();
					},
				]
			)
		);
	}
}
