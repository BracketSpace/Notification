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

		$this->add_action('post_updated', 10, 3);
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name(): string
	{
		// translators: singular post name.
		return sprintf(__('%s updated', 'notification'), WpObjectHelper::get_post_type_name($this->post_type));
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description(): string
	{
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__('Fires when %1$s (%2$s) is updated', 'notification'),
			WpObjectHelper::get_post_type_name($this->post_type),
			$this->post_type
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

		if ($post->post_type !== $this->post_type) {
			return false;
		}

		// Filter the post statuses for which the notification should be sent. By default it will be send only if you update already published post.
		$updatedPostStatuses = apply_filters('notification/trigger/wordpress/post/updated/statuses', [ 'publish' ], $this->post_type);

		// Pending posts doesn't have the slug, otherwise we should bail.
		if ($post->post_status !== 'pending' && empty($post->post_name)) {
			return false;
		}

		if (! in_array($postBefore->post_status, $updatedPostStatuses, true) || $post->post_status === 'trash') {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$updatingUserId = get_current_user_id();

		$this->author = get_userdata((int)$this->{ $this->post_type }->post_author);
		$this->last_editor = get_userdata((int)get_post_meta($this->{ $this->post_type }->ID, '_edit_last', true));
		$this->updating_user = get_userdata($updatingUserId);

		$this->{ $this->post_type . '_creation_datetime' } = strtotime($this->{ $this->post_type }->post_date_gmt);
		$this->{ $this->post_type . '_modification_datetime' } = strtotime($this->{ $this->post_type }->post_modified_gmt);
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		$postTypeName = WpObjectHelper::get_post_type_name($this->post_type);

		parent::merge_tags();

		// updating user.
		$this->add_merge_tag(
			new MergeTag\User\UserID(
				[
				'slug' => sprintf('%s_updating_user_ID', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user ID', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLogin(
				[
				'slug' => sprintf('%s_updating_user_login', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user login', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserEmail(
				[
				'slug' => sprintf('%s_updating_user_email', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user email', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserNicename(
				[
				'slug' => sprintf('%s_updating_user_nicename', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user nicename', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserDisplayName(
				[
				'slug' => sprintf('%s_updating_user_display_name', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user display name', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserFirstName(
				[
				'slug' => sprintf('%s_updating_user_firstname', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user first name', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLastName(
				[
				'slug' => sprintf('%s_updating_user_lastname', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user last name', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\Avatar(
				[
				'slug' => sprintf('%s_updating_user_avatar', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s updating user email', 'notification'), $postTypeName),
				'property_name' => 'updating_user',
				'group' => __('Updating user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserRole(
				[
				'slug' => sprintf('%s_updating_user_role', $this->post_type),
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

		$this->add_merge_tag(new MergeTag\Post\RevisionLink());
	}
}
