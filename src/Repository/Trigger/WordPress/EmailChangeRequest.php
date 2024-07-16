<?php

/**
 * Site email change request trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\WordPress;

use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Repository\Trigger\BaseTrigger;

/**
 * Site Email Change Request
 */
class EmailChangeRequest extends BaseTrigger
{
	/**
	 * User object
	 *
	 * @var \WP_User
	 */
	public $userObject;

	/**
	 * Old admin email
	 *
	 * @var string
	 */
	public $oldAdminEmail;

	/**
	 * New admin email
	 *
	 * @var string
	 */
	public $newAdminEmail;

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
	 * Email change timestamp
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
			'wordpress/email_change_request',
			__('Site email change request', 'notification')
		);

		/**
		 * Those actions must be executed after `update_option_new_admin_email`
		 * function in order to get change request hash.
		 *
		 * @see /wp-admin/includes/admin-filters.php
		 * @see /wp-admin/includes/misc.php
		 */
		$this->addAction('add_option_new_admin_email', 20, 2);
		$this->addAction('update_option_new_admin_email', 20, 2);

		$this->setGroup(__('WordPress', 'notification'));

		$this->setDescription(
			__('Fires when admin requests change of site email address', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param mixed $_unused Unused.
	 * @param string $newEmail New email value.
	 *
	 * @return mixed
	 * @since 8.0.0
	 */
	public function context($_unused, $newEmail)
	{
		$user = wp_get_current_user();

		if (! $user instanceof \WP_User) {
			return false;
		}

		$data = get_option('adminhash');

		if (!is_array($data) || !isset($data['hash'])) {
			return false;
		}

		$this->userObject = $user;
		$this->oldAdminEmail = is_string(get_option('admin_email')) ? get_option('admin_email') : '';
		$this->newAdminEmail = $newEmail;
		$this->emailChangeDatetime = time();
		$this->siteUrl = get_site_url();
		$this->confirmationUrl = esc_url(admin_url('options.php?adminhash=' . $data['hash']));
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 * @since 8.0.0
	 */
	public function mergeTags()
	{
		$this->addMergeTag(new MergeTag\User\UserID());
		$this->addMergeTag(new MergeTag\User\UserLogin());
		$this->addMergeTag(new MergeTag\User\UserEmail());
		$this->addMergeTag(new MergeTag\User\UserRole());
		$this->addMergeTag(new MergeTag\User\Avatar());
		$this->addMergeTag(new MergeTag\User\UserDisplayName());
		$this->addMergeTag(new MergeTag\User\UserFirstName());
		$this->addMergeTag(new MergeTag\User\UserLastName());
		$this->addMergeTag(new MergeTag\User\UserNicename());
		$this->addMergeTag(new MergeTag\User\UserNickname());

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
					'name' => __('Site email change time', 'notification'),
					'group' => __('Email', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\EmailTag(
				[
					'slug' => 'old_email',
					'name' => __('Old email address', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->oldAdminEmail;
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
						return $trigger->newAdminEmail;
					},
					'group' => __('Email', 'notification'),
				]
			)
		);
	}
}
