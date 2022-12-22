<?php

/**
 * Media added trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Media;

/**
 * Media added trigger class
 */
class MediaAdded extends MediaTrigger
{

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct(
			'media/added',
			__(
				'Media added',
				'notification'
			)
		);

		$this->addAction(
			'add_attachment',
			10,
			1
		);
		$this->setDescription(
			__(
				'Fires when new attachment is added',
				'notification'
			)
		);
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
		if (!$this->attachment instanceof \WP_Post) {
			return;
		}
		$this->userId = (int)$this->attachment->post_author;
		$user = get_userdata($this->userId);

		if (!$user instanceof \WP_User) {
			return;
		}
		$this->userObject = $user;

		$this->attachmentCreationDate = strtotime($this->attachment->post_date_gmt);
	}
}
