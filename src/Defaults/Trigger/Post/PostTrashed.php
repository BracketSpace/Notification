<?php

/**
 * Post trashed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post trashed trigger class
 */
class PostTrashed extends PostTrigger
{

	/**
	 * Post trashing user object
	 *
	 * @var \WP_User|false
	 */
	public $trashingUser;

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
			'slug' => 'post/' . $postType . '/trashed',
			]
		);

		$this->addAction('trash_' . $postType, 10, 2);
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name(): string
	{
		// translators: singular post name.
		return sprintf(__('%s trashed', 'notification'), WpObjectHelper::get_post_type_name($this->postType));
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
			__('Fires when %1$s (%2$s) is moved to trash', 'notification'),
			WpObjectHelper::get_post_type_name($this->postType),
			$this->postType
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $postId Post ID.
	 * @param object  $post    Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $postId, $post )
	{

		if ($post->postType !== $this->postType) {
			return false;
		}

		$this->{ $this->postType } = $post;

		$this->author = get_userdata((int)$this->{ $this->postType }->postAuthor);
		$this->lastEditor = get_userdata((int)get_post_meta($this->{ $this->postType }->ID, '_edit_last', true));
		$this->trashingUser = get_userdata(get_current_user_id());

		$this->{ $this->postType . '_creation_datetime' } = strtotime($this->{ $this->postType }->postDateGmt);
		$this->{ $this->postType . '_modification_datetime' } = strtotime($this->{ $this->postType }->postModifiedGmt);
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		$postTypeName = WpObjectHelper::get_post_type_name($this->postType);

		parent::merge_tags();

		// Trashing user.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
				'slug' => sprintf('%s_trashing_user_ID', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user ID', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
				'slug' => sprintf('%s_trashing_user_login', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user login', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
				'slug' => sprintf('%s_trashing_user_email', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user email', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
				'slug' => sprintf('%s_trashing_user_nicename', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user nicename', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
				'slug' => sprintf('%s_trashing_user_display_name', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user display name', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
				'slug' => sprintf('%s_trashing_user_firstname', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user first name', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
				'slug' => sprintf('%s_trashing_user_lastname', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user last name', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
				'slug' => sprintf('%s_trashing_user_avatar', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user avatar', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserRole(
				[
				'slug' => sprintf('%s_trashing_user_role', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s trashing user role', 'notification'), $postTypeName),
				'property_name' => 'trashing_user',
				'group' => __('Trashing user', 'notification'),
				]
			)
		);
	}
}
