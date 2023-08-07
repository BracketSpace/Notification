<?php

/**
 * Comment added trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment trashed trigger class
 */
class CommentTrashed extends CommentTrigger
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
				'slug' => 'comment/' . $commentType . '/trashed',
				'name' => sprintf(
				// Translators: %s comment type.
					__(
						'%s trashed',
						'notification'
					),
					WpObjectHelper::getCommentTypeName($commentType)
				),
				'comment_type' => $commentType,
			]
		);

		$this->addAction(
			'trashed_comment',
			10,
			2
		);

		$this->setDescription(
			sprintf(
			// translators: comment type.
				__(
					'Fires when %s is trashed',
					'notification'
				),
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

		if ($this->comment->comment_approved === 'spam' && \BracketSpace\Notification\getSetting('triggers/comment/akismet')) {
			return false;
		}

		if (!$this->isCorrectType($this->comment)) {
			return false;
		}

		// fix for action being called too early, before WP marks the comment as trashed.
		$this->comment->comment_approved = 'trash';

		parent::assignProperties();
	}
}
