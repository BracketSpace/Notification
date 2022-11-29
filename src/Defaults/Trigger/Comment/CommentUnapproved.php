<?php

/**
 * Comment unapproved trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment unapproved trigger class
 */
class CommentUnapproved extends CommentTrigger
{

	/**
	 * Constructor
	 *
	 * @param string $commentType optional, default: comment.
	 */
	public function __construct( $commentType = 'comment' )
	{

		parent::__construct(
			[
			'slug' => 'comment/' . $commentType . '/unapproved',
			// Translators: %s comment type.
			'name' => sprintf(__('%s unapproved', 'notification'), WpObjectHelper::getCommentTypeName($commentType)),
			'comment_type' => $commentType,
			]
		);

		$this->addAction('transition_comment_status', 10, 3);

		// translators: comment type.
		$this->setDescription(sprintf(__('Fires when %s is marked as unapproved', 'notification'), WpObjectHelper::getCommentTypeName($commentType), 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $commentNewStatus New comment status.
	 * @param string $commentOldStatus Old comment status.
	 * @param object $comment            Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $commentNewStatus, $commentOldStatus, $comment )
	{

		$this->comment = $comment;

		if ($this->comment->commentApproved === 'spam' && notification_get_setting('triggers/comment/akismet')) {
			return false;
		}

		if (! $this->isCorrectType($this->comment)) {
			return false;
		}

		if ($commentNewStatus === $commentOldStatus || $commentNewStatus !== 'unapproved') {
			return false;
		}

		parent::assignProperties();
	}
}
