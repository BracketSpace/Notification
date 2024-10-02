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
	 * Old user email
	 *
	 * @var string
	 */
	public $oldUserEmail;

	/**
	 * New user email
	 *
	 * @var string
	 */
	public $newUserEmail;

	/**
	 * Site URL.
	 *
	 * @var  string
	 */
	public $siteUrl;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('user/email_changed', __('User email changed', 'notification'));

		$this->addAction('profile_update', 10, 3);

		$this->setDescription(__('Fires when user changes his email address, after confirms by link.', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $userId Updated user ID.
	 * @param \WP_User $oldUser Old user instance.
	 * @param array<string, mixed> $newData New user data.
	 * @return false|void
	 */
	public function context($userId, $oldUser, $newData)
	{
		/**
		 * Make sure that the action comes from verifying the email address
		 */
		if (! isset($_REQUEST['newuseremail'])) {
			return false;
		}

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($newData['user_email'] === $oldUser->user_email) {
			return false;
		}

		$user = get_userdata($userId);

		if (! $user instanceof \WP_User) {
			return false;
		}

		$this->userId = $userId;
		$this->userObject = $user;

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$this->oldUserEmail = $oldUser->user_email;
		$this->newUserEmail = is_string($newData['user_email']) ? $newData['user_email'] : '';
		$this->siteUrl = get_site_url();
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
			new MergeTag\UrlTag(
				[
					'slug' => 'site_url',
					'name' => __('Site url', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->siteUrl;
					},
					'group' => __('Site', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'email_changed_datetime',
					'name' => __('Email changed time', 'notification'),
					'group' => __('Email', 'notification'),
					'timestamp' => static function () {
						return time();
					},
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\EmailTag(
				[
					'slug' => 'old_email',
					'name' => __('Old email address', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->oldUserEmail;
					},
					'group' => __('Email', 'notification'),
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
					'group' => __('Email', 'notification'),
				]
			)
		);
	}
}
