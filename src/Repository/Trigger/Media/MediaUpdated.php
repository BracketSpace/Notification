<?php

/**
 * Media updated trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Media;

use BracketSpace\Notification\Repository\MergeTag;

/**
 * Media added trigger class
 */
class MediaUpdated extends MediaTrigger
{
	/**
	 * Updating user object
	 *
	 * @var \WP_User
	 */
	public $updatingUser;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'media/updated',
			__('Media updated', 'notification')
		);

		$this->addAction('attachment_updated', 10, 1);
		$this->setDescription(__('Fires when attachment is updated', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $attachmentId Attachment Post ID.
	 * @return void
	 */
	public function context($attachmentId)
	{
		$this->attachment = get_post($attachmentId);

		$this->userId = get_current_user_id();

		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return;
		}

		$this->userObject = $user;
		$this->updatingUser = $user;

		$this->attachmentCreationDate = strtotime($this->attachment->post_date_gmt);
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		parent::mergeTags();

		// Updating user.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => 'attachment_updating_user_ID',
					'name' => __('Attachment updating user ID', 'notification'),
					'property_name' => 'updating_user',
					'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
					'slug' => 'attachment_updating_user_login',
					'name' => __('Attachment updating user login', 'notification'),
					'property_name' => 'updating_user',
					'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => 'attachment_updating_user_email',
					'name' => __('Attachment updating user email', 'notification'),
					'property_name' => 'updating_user',
					'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
					'slug' => 'attachment_updating_user_nicename',
					'name' => __('Attachment updating user nicename', 'notification'),
					'property_name' => 'updating_user',
					'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNickname(
				[
					'slug' => 'attachment_updating_user_nickname',
					'name' => __('Attachment updating user nickname', 'notification'),
					'property_name' => 'updating_user',
					'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
					'slug' => 'attachment_updating_user_firstname',
					'name' => __('Attachment updating user first name', 'notification'),
					'property_name' => 'updating_user',
					'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
					'slug' => 'attachment_updating_user_lastname',
					'name' => __('Attachment updating user last name', 'notification'),
					'property_name' => 'updating_user',
					'group' => __('Updating user', 'notification'),
				]
			)
		);
	}
}
