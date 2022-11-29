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
	public function __construct( $commentType = 'comment' )
	{

		parent::__construct(
			[
			'slug' => 'comment/' . $commentType . '/replied',
			// Translators: %s comment type.
			'name' => sprintf(__('%s replied', 'notification'), WpObjectHelper::get_comment_type_name($commentType)),
			'comment_type' => $commentType,
			]
		);

		$this->add_action('transition_comment_status', 10, 3);
		$this->add_action('notification_insert_comment_proxy', 10, 3);

		// translators: comment type.
		$this->set_description(sprintf(__('Fires when %s is replied and the reply is approved', 'notification'), WpObjectHelper::get_comment_type_name($commentType)));
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

		if ($this->comment->comment_approved === 'spam' && notification_get_setting('triggers/comment/akismet')) {
			return false;
		}

		if ($commentNewStatus === $commentOldStatus || $commentNewStatus !== 'approved') {
			return false;
		}

		// Bail if comment is not a reply.
		if (empty($this->comment->comment_parent)) {
			return false;
		}

		if (! $this->is_correct_type($this->comment)) {
			return false;
		}

		$this->parent_comment = get_comment($this->comment->comment_parent);

		$this->parent_comment_user_object = new \StdClass();
		$this->parent_comment_user_object->ID = ( $this->parent_comment->user_id ) ? $this->parent_comment->user_id : 0;
		$this->parent_comment_user_object->display_name = $this->parent_comment->comment_author;
		$this->parent_comment_user_object->user_email = $this->parent_comment->comment_author_email;

		parent::assign_properties();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		parent::merge_tags();

		$this->add_merge_tag(
			new MergeTag\Comment\CommentActionApprove(
				[
				'comment_type' => $this->comment_type,
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentActionTrash(
				[
				'comment_type' => $this->comment_type,
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentActionDelete(
				[
				'comment_type' => $this->comment_type,
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentActionSpam(
				[
				'comment_type' => $this->comment_type,
				]
			)
		);

		// Parent comment.
		$this->add_merge_tag(
			new MergeTag\Comment\CommentID(
				[
				'slug' => 'parent_comment_ID',
				'name' => __('Parent comment ID', 'notification'),
				'property_name' => 'parent_comment',
				'group' => __('Parent comment', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentContent(
				[
				'slug' => 'parent_comment_content',
				'name' => __('Parent comment content', 'notification'),
				'property_name' => 'parent_comment',
				'group' => __('Parent comment', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentStatus(
				[
				'slug' => 'parent_comment_status',
				'name' => __('Parent comment status', 'notification'),
				'property_name' => 'parent_comment',
				'group' => __('Parent comment', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentType(
				[
				'slug' => 'parent_comment_type',
				'name' => __('Parent comment type', 'notification'),
				'property_name' => 'parent_comment',
				'group' => __('Parent comment', 'notification'),
				]
			)
		);

		// Parent comment author.
		$this->add_merge_tag(
			new MergeTag\Comment\CommentAuthorIP(
				[
				'slug' => 'parent_comment_author_IP',
				'name' => __('Parent comment author IP', 'notification'),
				'property_name' => 'parent_comment',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentAuthorUserAgent(
				[
				'slug' => 'parent_comment_user_agent',
				'name' => __('Parent comment user agent', 'notification'),
				'property_name' => 'parent_comment',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentAuthorUrl(
				[
				'slug' => 'parent_comment_author_url',
				'name' => __('Parent comment author URL', 'notification'),
				'property_name' => 'parent_comment',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserID(
				[
				'slug' => 'parent_comment_author_user_ID',
				'name' => __('Parent comment author user ID', 'notification'),
				'property_name' => 'parent_comment_user_object',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserEmail(
				[
				'slug' => 'parent_comment_author_user_email',
				'name' => __('Parent comment author user email', 'notification'),
				'property_name' => 'parent_comment_user_object',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserDisplayName(
				[
				'slug' => 'parent_comment_author_user_display_name',
				'name' => __('Parent comment author user display name', 'notification'),
				'property_name' => 'parent_comment_user_object',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\Avatar(
				[
				'slug' => 'parent_comment_author_user_avatar',
				'name' => __('Parent comment author user avatar', 'notification'),
				'property_name' => 'parent_comment_user_object',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\AvatarUrl(
				[
				'slug' => 'parent_comment_author_user_avatar_url',
				'name' => __('Parent comment author user avatar url', 'notification'),
				'property_name' => 'parent_comment_user_object',
				'group' => __('Parent comment author', 'notification'),
				]
			)
		);
	}
}
