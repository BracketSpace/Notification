<?php

/**
 * Post updated trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post updated trigger class
 */
class PostUpdated extends PostTrigger
{

	/**
	 * Post updating user object
	 *
	 * @var \WP_User|false
	 */
	public $updatingUser;

	/**
	 * Constructor
	 *
	 * @param string $postType optional, default: post.
	 */
	public function __construct( $postType = 'post' )
	{
		parent::__construct(
			[
			'post_type' => $postType,
			'slug' => 'post/' . $postType . '/updated',
			]
		);

		$this->addAction('post_updated', 10, 3);
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function getName(): string
	{
		// translators: singular post name.
		return sprintf(__('%s updated', 'notification'), WpObjectHelper::get_post_type_name($this->postType));
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function getDescription(): string
	{
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__('Fires when %1$s (%2$s) is updated', 'notification'),
			WpObjectHelper::get_post_type_name($this->postType),
			$this->postType
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $postId Post ID.
	 * @param \WP_Post $post        Post object.
	 * @param \WP_Post $postBefore Post before object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $postId, $post, $postBefore )
	{

		if ($post->postType !== $this->postType) {
			return false;
		}

		// Filter the post statuses for which the notification should be sent. By default it will be send only if you update already published post.
		$updatedPostStatuses = apply_filters('notification/trigger/wordpress/post/updated/statuses', [ 'publish' ], $this->postType);

		// Pending posts doesn't have the slug, otherwise we should bail.
		if ($post->postStatus !== 'pending' && empty($post->postName)) {
			return false;
		}

		if (! in_array($postBefore->postStatus, $updatedPostStatuses, true) || $post->postStatus === 'trash') {
			return false;
		}

		$this->{ $this->postType } = $post;

		$updatingUserId = get_current_user_id();

		$this->author = get_userdata((int)$this->{ $this->postType }->postAuthor);
		$this->lastEditor = get_userdata((int)get_post_meta($this->{ $this->postType }->ID, '_edit_last', true));
		$this->updatingUser = get_userdata($updatingUserId);

		$this->{ $this->postType . '_creation_datetime' } = strtotime($this->{ $this->postType }->postDateGmt);
		$this->{ $this->postType . '_modification_datetime' } = strtotime($this->{ $this->postType }->postModifiedGmt);
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{

		$postTypeName = WpObjectHelper::get_post_type_name($this->postType);

		parent::merge_tags();

		// updating user.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
				'slug' => sprintf('%s_updating_user_ID', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user ID', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
				'slug' => sprintf('%s_updating_user_login', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user login', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
				'slug' => sprintf('%s_updating_user_email', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user email', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
				'slug' => sprintf('%s_updating_user_nicename', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user nicename', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
				'slug' => sprintf('%s_updating_user_display_name', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user display name', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
				'slug' => sprintf('%s_updating_user_firstname', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user first name', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
				'slug' => sprintf('%s_updating_user_lastname', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user last name', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
				'slug' => sprintf('%s_updating_user_avatar', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user email', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserRole(
				[
				'slug' => sprintf('%s_updating_user_role', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user role', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		// add revision link tag if revisions are enabled.
		if (!defined('WP_POST_REVISIONS') || !WP_POST_REVISIONS) {
			return;
		}

		$this->addMergeTag(new MergeTag\Post\RevisionLink());
	}
}
