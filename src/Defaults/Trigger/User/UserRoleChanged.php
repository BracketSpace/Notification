<?php

/**
 * User role changed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User role changed trigger class
 */
class UserRoleChanged extends UserTrigger
{
	/**
	 * User meta data
	 *
	 * @var array<mixed>
	 */
	public $userMeta;

	/**
	 * New role
	 *
	 * @var string
	 */
	public $newRole;

	/**
	 * Old role
	 *
	 * @var string
	 */
	public $oldRole;

	/**
	 * User role change date and time
	 *
	 * @var int|false
	 */
	public $userRoleChangeDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct(
			'user/role_changed',
			__('User role changed', 'notification')
		);

		$this->addAction('set_user_role', 1000, 3);

		$this->setDescription(
			__('Fires when user role changes', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId User ID.
	 * @param string $role User new role.
	 * @param array<mixed> $oldRoles User previous roles.
	 * @return mixed
	 */
	public function context($userId, $role, $oldRoles)
	{

		if (empty($oldRoles)) {
			return false;
		}

		$this->userId = $userId;

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return false;
		}

		$this->userObject = $user;
		$this->userMeta = get_user_meta($this->userId);
		$this->newRole = $role;
		$this->oldRole = implode(', ', $oldRoles);

		$this->userRegisteredDatetime = strtotime($this->userObject->user_registered);
		$this->userRoleChangeDatetime = time();
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
			new MergeTag\StringTag(
				[
					'slug' => 'new_role',
					'name' => __('New role', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->newRole;
					},
					'group' => __('Roles', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'old_role',
					'name' => __('Old role', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->oldRole;
					},
					'group' => __('Roles', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'user_role_change_datetime',
					'name' => __('User role change datetime', 'notification'),
				]
			)
		);
	}
}
