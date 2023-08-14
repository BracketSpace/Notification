<?php

/**
 * Comment spammed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment spammed trigger class
 */
class CommentSpammed extends CommentTrigger
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
				'slug' => 'comment/' . $commentType . '/spammed',
				// Translators: %s comment type.
				'name' => sprintf(__('%s spammed', 'notification'), WpObjectHelper::getCommentTypeName($commentType)),
				'comment_type' => $commentType,
			]
		);

		$this->addAction('spammed_comment', 100, 2);

		$this->setDescription(
			sprintf(
				// translators: comment type.
				__('Fires when %s is marked as spam', 'notification'),
				WpObjectHelper::getCommentTypeName($commentType)
			)
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $commentId Comment ID.
	 * @param object $comment Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($commentId, $comment)
	{
		$this->comment = $comment;

		if ($this->comment->comment_approved === 'spam' && notificationGetSetting('triggers/comment/akismet')) {
			return false;
		}

		if (!$this->isCorrectType($this->comment)) {
			return false;
		}

		// fix for action being called too early, before WP marks the comment as spam.
		$this->comment->comment_approved = 'spam';

		parent::assignProperties();
	}
}
