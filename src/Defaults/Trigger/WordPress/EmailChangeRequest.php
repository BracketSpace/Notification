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

		parent::__construct('wordpress/email_change_request', __('Site email change request', 'notification'));

		$this->add_action('update_option_new_admin_email', 10, 2);

		$this->set_group(__('WordPress', 'notification'));
		$this->set_description(__('Fires when admin requests change of site email address', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @since 8.0.0
	 *
	 * @param string $oldValue Old email value.
	 * @param string $value New email value.
	 *
	 * @return mixed
	 */
	public function context( $oldValue, $value )
	{

		if ($oldValue === $value) {
			return false;
		}

		$data = get_option('adminhash');
		$currentUser = wp_get_current_user();
		$this->user_login = $currentUser->user_login;
		$this->new_admin_email = $data['newemail'];
		$this->confirmation_url = esc_url(admin_url('options.php?adminhash=' . $data['hash']));
		$this->email_change_datetime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @since 8.0.0
	 * @return void
	 */
	public function merge_tags()
	{

		$this->add_merge_tag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => 'site_email_change_datetime',
				'name' => __('Site email change time', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				[
				'slug' => 'admin_login',
				'name' => __('Admin login', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->user_login;
				},
				'group' => __('Site', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\EmailTag(
				[
				'slug' => 'new_email',
				'name' => __('New email address', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->new_admin_email;
				},
				'group' => __('Site', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\UrlTag(
				[
				'slug' => 'confirmation_url',
				'name' => __('Email change confirmation url', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->confirmation_url;
				},
				'group' => __('Site', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\UrlTag(
				[
				'slug' => 'site_url',
				'name' => __('Site url', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->site_url;
				},
				'group' => __('Site', 'notification'),
				]
			)
		);
	}
}
