<?php

/**
 * User password change requested trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User password change requested trigger class
 */
class UserPasswordResetRequest extends UserTrigger
{
	/**
	 * Password reset request date and time
	 *
	 * @var int|false
	 */
	public $passwordResetRequestDatetime;

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
		$user = get_user_by(
			'login',
			$username
		);

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

		$this->userId = $user->data->ID;

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return false;
		}

		$this->userObject = $user;

		$this->passwordResetKey = $resetKey;

		$this->userRegisteredDatetime = strtotime($this->userObject->user_registered);
		$this->passwordResetRequestDatetime = time();
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
				]
			)
		);
	}
}
