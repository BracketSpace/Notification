<?php

/**
 * User login failed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag;

/**
 * User login failed trigger class
 */
class UserLoginFailed extends UserTrigger
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/login_failed',
			__('User login failed', 'notification')
		);

		$this->addAction('wp_login_failed', 10, 1);

		$this->setDescription(
			__('Fires when user login failed', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $username username.
	 * @return mixed
	 */
	public function context($username)
	{
		$user = get_user_by(
			'login',
			$username
		);

		// Bail if no user has been found to limit the spam login notifications.
		if (!$user) {
			return false;
		}

		$this->userId = $user->ID;
		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return false;
		}
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
		$this->addMergeTag(new MergeTag\User\UserBio());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'user_login_failed_datetime',
					'name' => __('User login failed datetime', 'notification'),
					'timestamp' => static function () {
						return time();
					},
				]
			)
		);
	}
}
