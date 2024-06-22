<?php

/**
 * Media trigger abstract
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Media;

use BracketSpace\Notification\Repository\Trigger\BaseTrigger;
use BracketSpace\Notification\Repository\MergeTag;

/**
 * Media trigger class
 */
abstract class MediaTrigger extends BaseTrigger
{
	/**
	 * Attachment post object
	 *
	 * @var \WP_Post
	 */
	public $attachment;

	/**
	 * User ID
	 *
	 * @var int
	 */
	public $userId;

	/**
	 * User object
	 *
	 * @var \WP_User
	 */
	public $userObject;

	/**
	 * Attachment creation date and time
	 *
	 * @var int|false
	 */
	public $attachmentCreationDate;

	/**
	 * Constructor
	 *
	 * @param string $slug $params trigger slug.
	 * @param string $name $params trigger name.
	 */
	public function __construct($slug, $name)
	{
		parent::__construct($slug, $name);

		$this->setGroup(__('Media', 'notification'));
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		$this->addMergeTag(new MergeTag\Media\AttachmentID());
		$this->addMergeTag(new MergeTag\Media\AttachmentPage());
		$this->addMergeTag(new MergeTag\Media\AttachmentTitle());
		$this->addMergeTag(new MergeTag\Media\AttachmentMimeType());
		$this->addMergeTag(new MergeTag\Media\AttachmentDirectUrl());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'attachment_creation_date',
					'name' => __('Attachment creation date', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		// Author.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => 'attachment_author_user_ID',
					'name' => __('Attachment author user ID', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
					'slug' => 'attachment_author_user_login',
					'name' => __('Attachment author user login', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => 'attachment_author_user_email',
					'name' => __('Attachment author user email', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
					'slug' => 'attachment_author_user_nicename',
					'name' => __('Attachment author user nicename', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNickname(
				[
					'slug' => 'attachment_author_user_nickname',
					'name' => __('Attachment author user nickname', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => 'attachment_author_user_display_name',
					'name' => __('Attachment author user display name', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
					'slug' => 'attachment_author_user_firstname',
					'name' => __('Attachment author user first name', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
					'slug' => 'attachment_author_user_lastname',
					'name' => __('Attachment author user last name', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
					'slug' => 'attachment_author_user_avatar',
					'name' => __('Attachment author user avatar', 'notification'),
					'group' => __('Author', 'notification'),
				]
			)
		);
	}
}
