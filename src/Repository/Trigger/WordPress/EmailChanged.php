<?php

/**
 * Site email changed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\WordPress;

use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Repository\Trigger\BaseTrigger;

/**
 * Site email changed trigger
 */
class EmailChanged extends BaseTrigger
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
	 * Email changed timestamp
	 *
	 * @var int
	 */
	public $emailChangedDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('wordpress/email_changed', __('Site email changed', 'notification'));

		$this->addAction('update_option_admin_email', 10, 2);

		$this->setGroup(__('WordPress', 'notification'));
		$this->setDescription(
			__('Fires when admin confirms his new email address. Trigger performing by user.', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $oldEmail Old email value.
	 * @param string $newEmail New email value.
	 *
	 * @return mixed
	 */
	public function context($oldEmail, $newEmail)
	{
		/* Make sure that the action comes from verifying the email address */
		if (! isset($_REQUEST['adminhash'])) {
			return false;
		}

		$user = wp_get_current_user();

		if (! $user instanceof \WP_User) {
			return false;
		}

		$this->userObject = $user;
		$this->oldAdminEmail = $oldEmail;
		$this->newAdminEmail = $newEmail;
		$this->emailChangedDatetime = time();
		$this->siteUrl = get_site_url();
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
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'email_changed_datetime',
					'name' => __('Site email changed time', 'notification'),
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
