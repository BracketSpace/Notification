<?php

/**
 * Site email changed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\WordPress;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Site email changed trigger
 */
class EmailChanged extends Abstracts\Trigger
{
	/**
	 * User login
	 *
	 * @var string
	 */
	public $userLogin;

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
		$this->setDescription(__('Fires when admin confirms his new email address', 'notification'));
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

		$currentUser = wp_get_current_user();
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$this->userLogin = $currentUser->user_login;

		// Makes sure that site new email is string
		$siteNewEmail = get_option('admin_email');
		if (! is_string($siteNewEmail)) {
			$siteNewEmail = '';
		}

		$this->siteNewEmail = $siteNewEmail;
		$this->emailChangedDatetime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		parent::mergeTags();

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'user_login',
					'name' => __('Admin login', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->userLogin;
					},
					'group' => __('Site', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
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
					'slug' => 'site_email_changed_datetime',
					'name' => __('Site email changed datetime', 'notification'),
				]
			)
		);
	}
}
