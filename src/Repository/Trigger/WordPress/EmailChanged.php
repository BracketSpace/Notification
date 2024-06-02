<?php

/**
 * Site email changed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\WordPress;

use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Site email changed trigger
 */
class EmailChanged extends Abstracts\Trigger
{
	/**
	 * User object
	 *
	 * @var \WP_User
	 */
	public $userObject;

	/**
	 * Site new email
	 *
	 * @var string
	 */
	public $siteNewEmail;

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
		parent::__construct('wordpress/email_address_changed', __('Site email changed', 'notification'));

		$this->addAction('delete_option_new_admin_email');

		$this->setGroup(__('WordPress', 'notification'));
		$this->setDescription(
			__('Fires when admin confirms his new email address. Trigger performing by user.', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @return mixed
	 */
	public function context()
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

		/** @var string $newEmail */
		$newEmail = get_option('admin_email');
		$this->siteNewEmail = $newEmail;
		$this->emailChangedDatetime = time();
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
			new MergeTag\EmailTag(
				[
					'slug' => 'site_new_email',
					'name' => __('Site new email', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->siteNewEmail;
					},
					'group' => __('Site', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'email_changed_datetime',
					'name' => __('Site email changed datetime', 'notification'),
				]
			)
		);
	}
}
