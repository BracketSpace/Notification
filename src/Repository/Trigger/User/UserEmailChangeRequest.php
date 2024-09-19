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
 * User Email Change Request
 */
class UserEmailChangeRequest extends UserTrigger
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
	 * Confirmation URL.
	 *
	 * @var string
	 */
	public $confirmationUrl;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/email_change_request',
			__('User email change request', 'notification')
		);

		/**
		 * Those actions must be executed after `send_confirmation_on_profile_email`
		 * function in order to get change request hash.
		 *
		 * @see /wp-admin/includes/admin-filters.php
		 * @see /wp-includes/user.php
		 */
		$this->addAction('personal_options_update', 20);

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
		$user = get_userdata($userId);

		if (! $user instanceof \WP_User) {
			return false;
		}

		$data = get_user_meta($userId, '_new_email', true);

		if (!is_array($data) || !isset($data['newemail']) || !isset($data['hash'])) {
			return false;
		}

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($user->user_email === $data['newemail']) {
			return false;
		}

		$this->userId = $userId;
		$this->userObject = $user;

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$this->oldUserEmail = $user->user_email;
		$this->newUserEmail = $data['newemail'];
		$this->siteUrl = get_site_url();
		$this->confirmationUrl = esc_url(admin_url('profile.php?newuseremail=' . $data['hash']));
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 * @since 8.0.0
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

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'email_change_datetime',
					'name' => __('Email change time', 'notification'),
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
