<?php

/**
 * Comment replied trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment replied trigger class
 */
class CommentReplied extends CommentTrigger
{

	/**
	 * Parent comment object
	 *
	 * @var \WP_Comment
	 */
	public $parentComment;

	/**
	 * Parent comment user object
	 *
	 * @var \stdClass
	 */
	public $parentCommentUserObject;

	/**
	 * Constructor
	 *
	 * @param string $commentType optional, default: comment.
	 */
	public function __construct($commentType = 'comment')
	{

		parent::__construct(
			[
				'slug' => 'comment/' . $commentType . '/replied',
				'name' => sprintf(
				// Translators: %s comment type.
					__(
						'%s replied',
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
		$this->addAction(
			'notification_insert_comment_proxy',
			10,
			3
		);

		$this->setDescription(
			sprintf(
			// translators: comment type.
				__(
					'Fires when %s is replied and the reply is approved',
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

		if ($this->comment->commentApproved === 'spam' && notification_get_setting('triggers/comment/akismet')) {
			return false;
		}

		if ($commentNewStatus === $commentOldStatus || $commentNewStatus !== 'approved') {
			return false;
		}

		// Bail if comment is not a reply.
		if (empty($this->comment->commentParent)) {
			return false;
		}

		if (!$this->isCorrectType($this->comment)) {
			return false;
		}

		$this->parentComment = get_comment($this->comment->commentParent);

		$this->parentCommentUserObject = new \StdClass();
		$this->parentCommentUserObject->ID = ($this->parentComment->userId)
			? $this->parentComment->userId
			: 0;
		$this->parentCommentUserObject->displayName = $this->parentComment->commentAuthor;
		$this->parentCommentUserObject->userEmail = $this->parentComment->commentAuthorEmail;

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

		// Parent comment.
		$this->addMergeTag(
			new MergeTag\Comment\CommentID(
				[
					'slug' => 'parent_comment_ID',
					'name' => __(
						'Parent comment ID',
						'notification'
					),
					'property_name' => 'parent_comment',
					'group' => __(
						'Parent comment',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentContent(
				[
					'slug' => 'parent_comment_content',
					'name' => __(
						'Parent comment content',
						'notification'
					),
					'property_name' => 'parent_comment',
					'group' => __(
						'Parent comment',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentStatus(
				[
					'slug' => 'parent_comment_status',
					'name' => __(
						'Parent comment status',
						'notification'
					),
					'property_name' => 'parent_comment',
					'group' => __(
						'Parent comment',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentType(
				[
					'slug' => 'parent_comment_type',
					'name' => __(
						'Parent comment type',
						'notification'
					),
					'property_name' => 'parent_comment',
					'group' => __(
						'Parent comment',
						'notification'
					),
				]
			)
		);

		// Parent comment author.
		$this->addMergeTag(
			new MergeTag\Comment\CommentAuthorIP(
				[
					'slug' => 'parent_comment_author_IP',
					'name' => __(
						'Parent comment author IP',
						'notification'
					),
					'property_name' => 'parent_comment',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentAuthorUserAgent(
				[
					'slug' => 'parent_comment_user_agent',
					'name' => __(
						'Parent comment user agent',
						'notification'
					),
					'property_name' => 'parent_comment',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentAuthorUrl(
				[
					'slug' => 'parent_comment_author_url',
					'name' => __(
						'Parent comment author URL',
						'notification'
					),
					'property_name' => 'parent_comment',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => 'parent_comment_author_user_ID',
					'name' => __(
						'Parent comment author user ID',
						'notification'
					),
					'property_name' => 'parent_comment_user_object',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => 'parent_comment_author_user_email',
					'name' => __(
						'Parent comment author user email',
						'notification'
					),
					'property_name' => 'parent_comment_user_object',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => 'parent_comment_author_user_display_name',
					'name' => __(
						'Parent comment author user display name',
						'notification'
					),
					'property_name' => 'parent_comment_user_object',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
					'slug' => 'parent_comment_author_user_avatar',
					'name' => __(
						'Parent comment author user avatar',
						'notification'
					),
					'property_name' => 'parent_comment_user_object',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\AvatarUrl(
				[
					'slug' => 'parent_comment_author_user_avatar_url',
					'name' => __(
						'Parent comment author user avatar url',
						'notification'
					),
					'property_name' => 'parent_comment_user_object',
					'group' => __(
						'Parent comment author',
						'notification'
					),
				]
			)
		);
	}
}
