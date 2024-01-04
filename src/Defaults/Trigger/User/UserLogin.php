<?php

/**
 * User login trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User login trigger class
 */
class UserLogin extends UserTrigger
{
	/**
	 * User meta data
	 *
	 * @var array<mixed>
	 */
	public $userMeta;

	/**
	 * User login date and time
	 *
	 * @var int|false
	 */
	public $userLoggedInDatetime;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'user/login',
			__('User login', 'notification')
		);

		$this->addAction('wp_login', 10, 2);

		$this->setDescription(
			__('Fires when user log into WordPress', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $userLogin Logged in user login.
	 * @param object $user User object.
	 * @return void
	 */
	public function context($userLogin, $user)
	{
		$this->userId = $user->ID;

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return;
		}

		$this->userObject = $user;
		$this->userMeta = get_user_meta($this->userId);

		$this->userRegisteredDatetime = strtotime($this->userObject->user_registered);
		$this->userLoggedInDatetime = time();
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
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'user_logged_in_datetime',
					'name' => __('User login time', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\IPTag(
				[
					'slug' => 'user_IP',
					'name' => __('User IP', 'notification'),
					'description' => '127.0.0.1',
					'example' => true,
					'resolver' => static function ($trigger) {
						if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
							return sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
						}

						if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
							return sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
						}

						if (!empty($_SERVER['REMOTE_ADDR'])) {
							return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
						}
						return '';
					},
					'group' => __('User', 'notification'),
				]
			)
		);
	}
}
