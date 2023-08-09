<?php

/**
 * Site email change request trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\WordPress;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Site Email Change Request
 */
class EmailChangeRequest extends Abstracts\Trigger
{
	/**
	 * User login
	 *
	 * @var string
	 */
	public $userLogin;

	/**
	 * New admin email
	 *
	 * @var string
	 */
	public $newAdminEmail;

	/**
	 * Confirmation email
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
	 * [description]
	 */

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct(
			'wordpress/email_change_request',
			__('Site email change request', 'notification')
		);

		$this->addAction('update_option_new_admin_email', 10, 2);

		$this->setGroup(__('WordPress', 'notification'));

		$this->setDescription(
			__('Fires when admin requests change of site email address', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $oldValue Old email value.
	 * @param string $value New email value.
	 *
	 * @return mixed
	 * @since 8.0.0
	 *
	 */
	public function context($oldValue, $value)
	{

		if ($oldValue === $value) {
			return false;
		}

		$data = get_option('adminhash');
		$currentUser = wp_get_current_user();
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$this->userLogin = $currentUser->user_login;
		$this->newAdminEmail = $data['newemail'];
		$this->confirmationUrl = esc_url(admin_url('options.php?adminhash=' . $data['hash']));
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

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'site_email_change_datetime',
					'name' => __('Site email change time', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'admin_login',
					'name' => __('Admin login', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->userLogin;
					},
					'group' => __('Site', 'notification'),
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
	}
}
