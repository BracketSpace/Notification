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
	public function __construct( $commentType = 'comment' )
	{

		parent::__construct(
			[
			'slug' => 'comment/' . $commentType . '/added',
			// Translators: %s comment type.
			'name' => sprintf(__('%s added', 'notification'), WpObjectHelper::get_comment_type_name($commentType)),
			'comment_type' => $commentType,
			]
		);

		$this->addAction('wp_insert_comment', 10, 2);

		// Translators: comment type.
		$this->setDescription(sprintf(__('Fires when new %s is added to database and awaits moderation or is published. Includes comment replies.', 'notification'), WpObjectHelper::get_comment_type_name($commentType)));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $commentId Comment ID.
	 * @param object  $comment    Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $commentId, $comment )
	{

		$this->comment = $comment;

		if ($this->comment->commentApproved === 'spam' && notification_get_setting('triggers/comment/akismet')) {
			return false;
		}

		if (! $this->isCorrectType($this->comment)) {
			return false;
		}

		parent::assign_properties();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{

		parent::merge_tags();

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
