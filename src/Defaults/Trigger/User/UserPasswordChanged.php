<?php

/**
 * User password changed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User password changed trigger class
 */
class UserPasswordChanged extends UserTrigger
{
	/**
	 * User meta data
	 *
	 * @var array<mixed>
	 */
	public $userMeta;

	/**
	 * Password change date and time
	 *
	 * @var int|false
	 */
	public $passwordChangeDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct(
			'user/password_changed',
			__('User password changed', 'notification')
		);

		$this->addAction('password_reset', 10, 1);

		$this->setDescription(
			__(
				'Fires when user changed his password',
				'notification'
			)
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param object $user User object.
	 * @return void
	 */
	public function context($user)
	{

		$this->userId = $user->ID;
		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return;
		}

		$this->userMeta = get_user_meta($this->userId);

		$this->userRegisteredDatetime = strtotime($this->userObject->user_registered);
		$this->passwordChangeDatetime = time();
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
					'slug' => 'password_change_datetime',
					'name' => __('Password change date', 'notification'),
				]
			)
		);
	}
}
