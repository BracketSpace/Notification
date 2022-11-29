<?php

/**
 * User deleted trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User deleted trigger class
 */
class UserDeleted extends UserTrigger
{

	/**
	 * User meta data
	 *
	 * @var array
	 */
	public $userMeta;

	/**
	 * User deletion date and time
	 *
	 * @var int|false
	 */
	public $userDeletedDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct('user/deleted', __('User deleted', 'notification'));

		$this->addAction('delete_user', 10, 1);

		$this->setDescription(__('Fires when user account is deleted', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId User ID.
	 * @return void
	 */
	public function context( $userId )
	{

		$this->userId = $userId;
		$this->userObject = get_userdata($this->userId);
		$this->userMeta = get_user_meta($this->userId);

		$this->userRegisteredDatetime = strtotime($this->userObject->userRegistered);
		$this->userDeletedDatetime = time();
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
				]
			)
		);
	}
}
