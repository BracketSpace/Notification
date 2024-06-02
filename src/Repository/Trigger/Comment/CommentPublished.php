<?php

/**
 * Comment published trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment published trigger class
 */
class CommentPublished extends CommentTrigger
{
	/**
	 * Constructor
	 *
	 * @param string $commentType optional, default: comment.
	 */
	public function __construct($commentType = 'comment')
	{
		parent::__construct(
			[
				'slug' => 'comment/' . $commentType . '/published',
				// Translators: %s comment type.
				'name' => sprintf(__('%s published', 'notification'), WpObjectHelper::getCommentTypeName($commentType)),
				'comment_type' => $commentType,
			]
		);

		$this->addAction('notification_comment_published_proxy', 10, 1);

		$this->setDescription(
			sprintf(
				// Translators: comment type.
				__('Fires when new %s is published on the website. Includes comment replies.', 'notification'),
				WpObjectHelper::getCommentTypeName($commentType)
			)
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param object $comment Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($comment)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($comment->comment_approved !== '1') {
			return false;
		}

		if (!$this->isCorrectType($comment)) {
			return false;
		}

		$this->comment = $comment;

		parent::assignProperties();
	}
}
