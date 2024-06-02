<?php

/**
 * User logout trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag;

/**
 * User logout trigger class
 */
class UserLogout extends UserTrigger
{
	/**
	 * User meta data
	 *
	 * @var array<mixed>
	 */
	public $userMeta;

	/**
	 * User logout date and time
	 *
	 * @var int|false
	 */
	public $userLogoutDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/logout',
			__('User logout', 'notification')
		);

		$this->addAction('wp_logout', 10, 1);

		$this->setDescription(
			__('Fires when user log out from WordPress', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId User ID.
	 * @return void
	 */
	public function context($userId = 0)
	{
		// Fix for WordPress <5.5 where the param is not available.
		if ($userId === 0) {
			$userId = get_current_user_id();
		}

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return;
		}

		$this->userObject = $user;
		$this->userMeta = get_user_meta($userId);

		$this->userRegisteredDatetime = strtotime($this->userObject->user_registered);
		$this->userLogoutDatetime = time();
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
					'slug' => 'user_logout_datetime',
					'name' => __('User logout time', 'notification'),
				]
			)
		);
	}
}
