<?php

/**
 * User email changed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User Email Change Request
 */
class UserEmailChangeRequest extends UserTrigger
{
	/**
	 * User meta
	 *
	 * @var array<mixed>
	 */
	public $userMeta;

	/**
	 * New user email
	 *
	 * @var string
	 */
	public $newUserEmail;

	/**
	 * Email change confirmation URL
	 *
	 * @var string
	 */
	public $confirmationUrl;

	/**
	 * Email change datetime
	 *
	 * @var int
	 */
	public $emailChangeDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/email_change_request',
			__('User email change request', 'notification')
		);

		$this->addAction('personal_options_update', 10, 1);

		$this->setDescription(
			__('Fires when user requests change of his email address', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId User ID.
	 * @return mixed
	 * @since 8.0.0
	 */
	public function context($userId)
	{
		$newEmail = get_user_meta(
			$userId,
			'_new_email',
			true
		);

		if (!$newEmail) {
			return false;
		}

		$this->userId = $userId;

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return false;
		}

		$this->userObject = $user;
		$this->userMeta = get_user_meta($this->userId);
		$this->newUserEmail = $newEmail['newemail'];
		$this->confirmationUrl = esc_url(admin_url('profile.php?newuseremail=' . $newEmail['hash']));
		$this->emailChangeDatetime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 * @since 8.0.0
	 */
	public function mergeTags()
	{
		$this->addMergeTag(new MergeTag\User\UserNicename());
		$this->addMergeTag(new MergeTag\User\UserDisplayName());
		$this->addMergeTag(new MergeTag\User\UserFirstName());
		$this->addMergeTag(new MergeTag\User\UserLastName());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'user_email_change_datetime',
					'name' => __('User email change time', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\EmailTag(
				[
					'slug' => 'new_email',
					'name' => __('New email address', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->newUserEmail;
					},
					'group' => __('User', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\UrlTag(
				[
					'slug' => 'confirmation_url',
					'name' => __('Email change confirmation url', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->confirmationUrl;
					},
					'group' => __('Site', 'notification'),
				]
			)
		);
	}
}
