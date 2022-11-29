<?php

/**
 * Media trashed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Media;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Media trashed trigger class
 */
class MediaTrashed extends MediaTrigger
{

	/**
	 * Trashing user object
	 *
	 * @var \WP_User
	 */
	public $trashingUser;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct('media/trashed', __('Media trashed', 'notification'));

		$this->add_action('delete_attachment', 10, 1);
		$this->set_description(__('Fires when attachment is removed', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $attachmentId Attachment Post ID.
	 * @return void
	 */
	public function context( $attachmentId )
	{

		$this->attachment = get_post($attachmentId);

		$this->user_id = get_current_user_id();

		$this->user_object = get_userdata($this->user_id);
		$this->trashing_user = get_userdata($this->user_id);

		$this->attachment_creation_date = strtotime($this->attachment->post_date_gmt);
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		parent::merge_tags();

		// Trashing user.
		$this->add_merge_tag(
			new MergeTag\User\UserID(
				[
				'slug' => 'attachment_trashing_user_ID',
				'name' => __('Attachment trashing user ID', 'notification'),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLogin(
				[
				'slug' => 'attachment_trashing_user_login',
				'name' => __('Attachment trashing user login', 'notification'),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserEmail(
				[
				'slug' => 'attachment_trashing_user_email',
				'name' => __('Attachment trashing user email', 'notification'),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserNicename(
				[
				'slug' => 'attachment_trashing_user_nicename',
				'name' => __('Attachment trashing user nicename', 'notification'),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserDisplayName(
				[
				'slug' => 'attachment_trashing_user_display_name',
				'name' => __('Attachment trashing user display name', 'notification'),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserFirstName(
				[
				'slug' => 'attachment_trashing_user_firstname',
				'name' => __('Attachment trashing user first name', 'notification'),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLastName(
				[
				'slug' => 'attachment_trashing_user_lastname',
				'name' => __('Attachment trashing user last name', 'notification'),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);
	}
}
