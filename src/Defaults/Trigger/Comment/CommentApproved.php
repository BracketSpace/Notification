<?php

/**
 * Comment approved trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment added trigger class
 */
class CommentApproved extends CommentTrigger
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
				'slug' => 'comment/' . $commentType . '/approved',
				'name' => sprintf(
				// Translators: %s comment type.
					__(
						'%s approved',
						'notification'
					),
					WpObjectHelper::getCommentTypeName($commentType)
				),
				'comment_type' => $commentType,
			]
		);

		$this->addAction(
			'transition_comment_status',
			10,
			3
		);

		$this->setDescription(
			sprintf(
			// translators: comment type.
				__(
					'Fires when %s is approved',
					'notification'
				),
				WpObjectHelper::getCommentTypeName($commentType)
			)
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $commentNewStatus New comment status.
	 * @param string $commentOldStatus Old comment status.
	 * @param object $comment Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($commentNewStatus, $commentOldStatus, $comment)
	{

		$this->comment = $comment;

		if (
			$this->comment->comment_approved === 'spam' &&
			\BracketSpace\Notification\getSetting('triggers/comment/akismet')
		) {
			return false;
		}

		if (!$this->isCorrectType($this->comment)) {
			return false;
		}

		if ($commentNewStatus === $commentOldStatus || $commentNewStatus !== 'approved') {
			return false;
		}

		parent::assignProperties();
	}
}
