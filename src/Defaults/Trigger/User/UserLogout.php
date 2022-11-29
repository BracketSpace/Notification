<?php

/**
 * User logout trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User logout trigger class
 */
class UserLogout extends UserTrigger
{

	/**
	 * User meta data
	 *
	 * @var array
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

		parent::__construct('user/logout', __('User logout', 'notification'));

		$this->add_action('wp_logout', 10, 1);

		$this->set_description(__('Fires when user log out from WordPress', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId User ID.
	 * @return void
	 */
	public function context( $userId = 0 )
	{
		// Fix for WordPress <5.5 where the param is not available.
		if ($userId === 0) {
			$userId = get_current_user_id();
		}

		$this->user_object = get_userdata($userId);
		$this->user_meta = get_user_meta($userId);

		$this->user_registered_datetime = strtotime($this->user_object->user_registered);
		$this->user_logout_datetime = time();
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
			new MergeTag\DateTime\DateTime(
				[
				'slug' => 'user_logout_datetime',
				'name' => __('User logout time', 'notification'),
				]
			)
		);
	}
}
