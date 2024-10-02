<?php

/**
 * User trigger abstract
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Repository\Trigger\BaseTrigger;
use BracketSpace\Notification\Repository\MergeTag;

/**
 * User trigger class
 */
abstract class UserTrigger extends BaseTrigger
{
	/**
	 * User ID
	 *
	 * @var int
	 */
	public $userId;

	/**
	 * User object
	 *
	 * @var \WP_User
	 */
	public $userObject;

	/**
	 * Constructor
	 *
	 * @param string $slug $params trigger slug.
	 * @param string $name $params trigger name.
	 */
	public function __construct($slug, $name)
	{
		parent::__construct(
			$slug,
			$name
		);
		$this->setGroup(
			__(
				'User',
				'notification'
			)
		);
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		$this->addMergeTag(new MergeTag\User\UserID());
		$this->addMergeTag(new MergeTag\User\UserLogin());
		$this->addMergeTag(new MergeTag\User\UserEmail());
		$this->addMergeTag(new MergeTag\User\UserRole());
		$this->addMergeTag(new MergeTag\User\UserNickname());
		$this->addMergeTag(new MergeTag\User\Avatar());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'user_registered_datetime',
					'name' => __('User registration date', 'notification'),
					'group' => __('User', 'notification'),
					'timestamp' => static function (UserTrigger $trigger) {
						return strtotime($trigger->userObject->user_registered);
					},
				]
			)
		);
	}
}
