<?php

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

class UserEmailChanged extends UserTrigger
{
	public $changedTo;

	public function __construct()
	{
		parent::__construct(
			'user/email_changed',
			__(
				'User email changed',
				'notification'
			)
		);

		$this->addAction(
			'delete_user_meta',
			10,
			4
		);

		$this->setDescription(
			__(
				'Fires when user changes his email address. After confirms his new email address by confirmation link.',
				'notification'
			)
		);
	}

	public function context($metaIds, $objectId, $metaKey, $metaValue)
	{
		if ('_new_email' !== $metaKey) {
			return false;
		}

		$this->userId = $objectId;

		if (! isset($_GET['newuseremail'])) {
			return false;
		}

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return false;
		}

		$this->userObject = $user;
		$this->userMeta = get_user_meta($this->userId);

		$this->userRegisteredDatetime = strtotime($this->userObject->user_registered);
		$this->userChaneEmailDatetime = time();

		$this->changedTo = $user->user_email;
	}

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
					'name' => __(
						'User change email time',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\EmailTag([
				'slug' => 'email_changed_to',
				'name' => __(
					'Email changed to',
					'notification'
				),
				'resolver' => static function ($trigger) {
					return $trigger->changedTo;
				},
			])
		);
	}
}
