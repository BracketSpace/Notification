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
	 * @var array
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

		parent::__construct('user/role_changed', __('User role changed', 'notification'));

		$this->add_action('set_user_role', 1000, 3);

		$this->set_description(__('Fires when user role changes', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId User ID.
	 * @param string  $role      User new role.
	 * @param array   $oldRoles User previous roles.
	 * @return mixed
	 */
	public function context( $userId, $role, $oldRoles )
	{

		if (empty($oldRoles)) {
			return false;
		}

		$this->user_id = $userId;
		$this->user_object = get_userdata($this->user_id);
		$this->user_meta = get_user_meta($this->user_id);
		$this->new_role = $role;
		$this->old_role = implode(', ', $oldRoles);

		$this->user_registered_datetime = strtotime($this->user_object->user_registered);
		$this->user_role_change_datetime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		parent::merge_tags();

		$this->add_merge_tag(new MergeTag\User\UserNicename());
		$this->add_merge_tag(new MergeTag\User\UserDisplayName());
		$this->add_merge_tag(new MergeTag\User\UserFirstName());
		$this->add_merge_tag(new MergeTag\User\UserLastName());
		$this->add_merge_tag(new MergeTag\User\UserBio());

		$this->add_merge_tag(
			new MergeTag\StringTag(
				[
				'slug' => 'new_role',
				'name' => __('New role', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->new_role;
				},
				'group' => __('Roles', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				[
				'slug' => 'old_role',
				'name' => __('Old role', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->old_role;
				},
				'group' => __('Roles', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => 'user_role_change_datetime',
				'name' => __('User role change datetime', 'notification'),
				]
			)
		);
	}
}
