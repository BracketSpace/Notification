<?php

/**
 * Comment added trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment added trigger class
 */
class CommentAdded extends CommentTrigger
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
				'slug' => 'comment/' . $commentType . '/added',

				// Translators: %s comment type.
				'name' => sprintf(__('%s added', 'notification'), WpObjectHelper::getCommentTypeName($commentType)),
				'comment_type' => $commentType,
			]
		);

		$this->addAction(
			'wp_insert_comment',
			10,
			2
		);

		$this->setDescription(
			sprintf(
				// Translators: comment type.
				__(
					'Fires when new %s is added to database and awaits moderation or is published.' .
						' Includes comment replies.',
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

		if ($this->comment->comment_approved === 'spam' && notificationGetSetting('triggers/comment/akismet')) {
			return false;
		}

		if (!$this->isCorrectType($this->comment)) {
			return false;
		}

		parent::assignProperties();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		parent::mergeTags();

		$this->addMergeTag(
			new MergeTag\Comment\CommentActionApprove(
				[
					'comment_type' => $this->commentType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentActionTrash(
				[
					'comment_type' => $this->commentType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentActionDelete(
				[
					'comment_type' => $this->commentType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentActionSpam(
				[
					'comment_type' => $this->commentType,
				]
			)
		);
	}
}
