<?php

/**
 * Comment trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Comment;

use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment trigger class
 */
abstract class CommentTrigger extends Abstracts\Trigger
{
	/**
	 * Comment Type slug
	 *
	 * @var string
	 */
	public $commentType;

	/**
	 * Comment object
	 *
	 * @var \WP_Comment
	 */
	public $comment;

	/**
	 * Comment author user object
	 *
	 * @var \stdClass
	 */
	public $userObject;

	/**
	 * Post object
	 *
	 * @var \WP_Post
	 */
	public $post;

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	public $postType;

	/**
	 * Post creation date and time
	 *
	 * @var int|false
	 */
	public $postCreationDatetime;

	/**
	 * Post modification date and time
	 *
	 * @var int|false
	 */
	public $postModificationDatetime;

	/**
	 * Comment date and time
	 *
	 * @var int|false
	 */
	public $commentDatetime;

	/**
	 * Post author user object
	 *
	 * @var \WP_User
	 */
	public $postAuthor;

	/**
	 * Constructor
	 *
	 * @param array<mixed> $params trigger configuration params.
	 */
	public function __construct($params = [])
	{
		if (!isset($params['comment_type'], $params['slug'], $params['name'])) {
			trigger_error('CommentTrigger requires comment_type, slug and name params.', E_USER_ERROR);
		}

		$this->commentType = $params['comment_type'];

		parent::__construct($params['slug'], $params['name']);

		$this->setGroup((string)WpObjectHelper::getCommentTypeName($this->commentType));
	}

	/**
	 * Sets trigger's context
	 *
	 * @return void
	 */
	public function assignProperties()
	{
		$this->userObject = new \StdClass();
		$this->userObject->ID = ($this->comment->user_id)
			? ($this->comment->user_id) : 0;
		$this->userObject->displayName = $this->comment->comment_author;
		$this->userObject->userEmail = $this->comment->comment_author_email;

		$this->post = get_post((int)$this->comment->comment_post_ID);

		if (!$this->post instanceof \WP_Post) {
			return;
		}

		$this->postType = $this->post->post_type;

		$this->postCreationDatetime = strtotime($this->post->post_date_gmt);
		$this->postModificationDatetime = strtotime($this->post->post_modified_gmt);
		$this->commentDatetime = strtotime($this->comment->comment_date_gmt);

		$user = get_userdata((int)$this->post->post_author);

		if (!($user instanceof \WP_User)) {
			return;
		}

		$this->postAuthor = $user;
	}

	/**
	 * Check if comment is correct type
	 *
	 * @param mixed $comment Comment object or Comment ID.
	 * @return bool
	 */
	public function isCorrectType($comment)
	{
		return get_comment_type($comment) === $this->commentType;
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		$commentTypeName = WpObjectHelper::getCommentTypeName($this->commentType);
		$postTypeName = WpObjectHelper::getPostTypeName('post');

		$this->addMergeTag(
			new MergeTag\Comment\CommentID(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentContent(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentContentHtml(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentStatus(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentType(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentIsReply(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'comment_datetime',
					// Translators: Comment type name.
					'name' => sprintf(__('%s date and time', 'notification'), $commentTypeName),
					'group' => $commentTypeName,
				]
			)
		);

		// Author.
		$this->addMergeTag(
			new MergeTag\Comment\CommentAuthorIP(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentAuthorUserAgent(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\Comment\CommentAuthorUrl(['comment_type' => $this->commentType])
		);

		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => 'comment_author_user_ID',
					// Translators: Comment type name.
					'name' => sprintf(__('%s author user ID', 'notification'), $commentTypeName),
					// Translators: comment type author.
					'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => 'comment_author_user_email',
					// Translators: Comment type name.
					'name' => sprintf(__('%s author user email', 'notification'), $commentTypeName),
					// Translators: comment type author.
					'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => 'comment_author_user_display_name',
					// Translators: Comment type name.
					'name' => sprintf(__('%s author user display name', 'notification'), $commentTypeName),
					// Translators: comment type author.
					'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
					'slug' => 'comment_author_user_avatar',
					// Translators: Comment type name.
					'name' => sprintf(__('%s author user avatar', 'notification'), $commentTypeName),
					// Translators: comment type author.
					'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\AvatarUrl(
				[
					'slug' => 'comment_author_user_avatar_url',
					// Translators: Comment type name.
					'name' => sprintf(__('%s author user avatar url', 'notification'), $commentTypeName),
					// Translators: comment type author.
					'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				]
			)
		);

		// Post.
		$this->addMergeTag(new MergeTag\Post\PostID());
		$this->addMergeTag(new MergeTag\Post\PostPermalink());
		$this->addMergeTag(new MergeTag\Post\PostTitle());
		$this->addMergeTag(new MergeTag\Post\PostSlug());
		$this->addMergeTag(new MergeTag\Post\PostContent());
		$this->addMergeTag(new MergeTag\Post\PostExcerpt());
		$this->addMergeTag(new MergeTag\Post\PostStatus());
		$this->addMergeTag(new MergeTag\Post\PostType());

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'post_creation_datetime',
					// Translators: singular post name.
					'name' => sprintf(__('%s creation date and time', 'notification'), __('Post', 'notification')),
					'group' => $postTypeName,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'post_modification_datetime',
					// Translators: singular post name.
					'name' => sprintf(__('%s modification date and time', 'notification'), __('Post', 'notification')),
					'group' => $postTypeName,
				]
			)
		);

		// Post Author.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => 'post_author_user_ID',
					// Translators: singular post name.
					'name' => sprintf(__('%s author user ID', 'notification'), __('Post', 'notification')),
					'property_name' => 'post_author',
					// Translators: Post type name.
					'group' => sprintf(__('%s author', 'notification'), $postTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
					'slug' => 'post_author_user_login',
					// Translators: singular post name.
					'name' => sprintf(__('%s author user login', 'notification'), __('Post', 'notification')),
					'property_name' => 'post_author',
					// Translators: Post type name.
					'group' => sprintf(__('%s author', 'notification'), $postTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => 'post_author_user_email',
					// Translators: singular post name.
					'name' => sprintf(__('%s author user email', 'notification'), __('Post', 'notification')),
					'property_name' => 'post_author',
					// Translators: Post type name.
					'group' => sprintf(__('%s author', 'notification'), $postTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
					'slug' => 'post_author_user_nicename',
					// Translators: singular post name.
					'name' => sprintf(__('%s author user nicename', 'notification'), __('Post', 'notification')),
					'property_name' => 'post_author',
					// Translators: Post type name.
					'group' => sprintf(__('%s author', 'notification'), $postTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => 'post_author_user_display_name',
					// Translators: singular post name.
					'name' => sprintf(__('%s author user display name', 'notification'), __('Post', 'notification')),
					'property_name' => 'post_author',
					// Translators: Post type name.
					'group' => sprintf(__('%s author', 'notification'), $postTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
					'slug' => 'post_author_user_firstname',
					// Translators: singular post name.
					'name' => sprintf(__('%s author user first name', 'notification'), __('Post', 'notification')),
					'property_name' => 'post_author',
					// Translators: Post type name.
					'group' => sprintf(__('%s author', 'notification'), $postTypeName),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
					'slug' => 'post_author_user_lastname',
					// Translators: singular post name.
					'name' => sprintf(__('%s author user last name', 'notification'), __('Post', 'notification')),
					'property_name' => 'post_author',
					// Translators: Post type name.
					'group' => sprintf(__('%s author', 'notification'), $postTypeName),
				]
			)
		);
	}
}
