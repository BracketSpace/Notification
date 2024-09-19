<?php

/**
 * User deleted trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag;

/**
 * User deleted trigger class
 */
class UserDeleted extends UserTrigger
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/deleted',
			__('User deleted', 'notification')
		);

		$this->addAction('delete_user', 10, 1);

		$this->setDescription(__('Fires when user account is deleted', 'notification'));
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
					'slug' => 'user_deleted_datetime',
					'name' => __('User deletion time', 'notification'),
					'timestamp' => static function () {
						return time();
					},
				]
			)
		);
	}
}
