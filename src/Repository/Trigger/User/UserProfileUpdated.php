<?php

/**
 * User profile updated trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag;

/**
 * User profile updated trigger class
 */
class UserProfileUpdated extends UserTrigger
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/profile_updated',
			__('User profile updated', 'notification')
		);

		$this->addAction('profile_update', 10, 2);

		$this->setDescription(
			__(
				'Fires when user updates his profile',
				'notification'
			)
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

		$this->addMergeTag(new MergeTag\User\UserNicename());
		$this->addMergeTag(new MergeTag\User\UserDisplayName());
		$this->addMergeTag(new MergeTag\User\UserFirstName());
		$this->addMergeTag(new MergeTag\User\UserLastName());
		$this->addMergeTag(new MergeTag\User\UserBio());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'user_profile_updated_datetime',
					'name' => __('User profile update time', 'notification'),
					'timestamp' => static function () {
						return time();
					},
				]
			)
		);
	}
}
