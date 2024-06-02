<?php

/**
 * User email changed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag;

/**
 * User email changed class
 */
class UserEmailChanged extends UserTrigger
{
	/**
	 * User meta data
	 *
	 * @var array<mixed>|mixed
	 */
	public $userMeta;

	/**
	 * User new email address
	 *
	 * @var int|false
	 */
	public $userChangeEmailDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('user/email_changed', __('User email changed', 'notification'));

		$this->addAction('delete_user_meta', 10, 4);

		$this->setDescription(__('Fires when user changes his email address, after confirms by link.', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param array<int> $metaIds User meta Ids
	 * @param int $objectId User ID
	 * @param string $metaKey User meta key
	 * @param string $metaValue User meta value
	 * @return false|void
	 */
	public function context($metaIds, $objectId, $metaKey, $metaValue)
	{
		if ($metaKey !== '_new_email') {
			return false;
		}

		/**
		 * Make sure that the action comes from verifying the email address
		 */
		if (! isset($_REQUEST['newuseremail'])) {
			return false;
		}

		$this->userId = $objectId;
		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return false;
		}

		$this->userObject = $user;
		$this->userMeta = get_user_meta($this->userId);

		$this->userRegisteredDatetime = strtotime($this->userObject->user_registered);
		$this->userChangeEmailDatetime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		parent::mergeTags();

		$this->addMergeTag(new MergeTag\User\UserDisplayName());
		$this->addMergeTag(new MergeTag\User\UserFirstName());
		$this->addMergeTag(new MergeTag\User\UserLastName());
		$this->addMergeTag(new MergeTag\User\UserNicename());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'user_change_email_datetime',
					'name' => __('User change email date time', 'notification'),
				]
			)
		);
	}
}
