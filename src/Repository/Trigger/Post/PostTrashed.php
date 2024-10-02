<?php

/**
 * Post trashed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Post;

use BracketSpace\Notification\Repository\MergeTag;
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
	public function __construct($postType = 'post')
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
	public function getName(): string
	{
		return sprintf(
			// translators: singular post name.
			__('%s trashed', 'notification'),
			WpObjectHelper::getPostTypeName($this->postType)
		);
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
			__('Fires when %1$s (%2$s) is moved to trash', 'notification'),
			WpObjectHelper::getPostTypeName($this->postType),
			$this->postType
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $postId Post ID.
	 * @param \WP_Post $post Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($postId, $post)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type !== $this->postType) {
			return false;
		}

		$this->post = $post;

		$this->author = get_userdata((int)$this->post->post_author);
		$this->lastEditor = get_userdata((int)get_post_meta($this->post->ID, '_edit_last', true));
		$this->trashingUser = get_userdata(get_current_user_id());
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		$postTypeName = WpObjectHelper::getPostTypeName($this->postType);

		parent::mergeTags();

		// Trashing user.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => sprintf(
						'%s_trashing_user_ID',
						$this->postType
					),
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
					'slug' => sprintf(
						'%s_trashing_user_login',
						$this->postType
					),
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
					'slug' => sprintf(
						'%s_trashing_user_email',
						$this->postType
					),
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
					'slug' => sprintf(
						'%s_trashing_user_nicename',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s trashing user nicename', 'notification'), $postTypeName),
					'property_name' => 'trashing_user',
					'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNickname(
				[
					'slug' => sprintf(
						'%s_trashing_user_nickname',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s trashing user nickname', 'notification'), $postTypeName),
					'property_name' => 'trashing_user',
					'group' => __('Trashing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => sprintf(
						'%s_trashing_user_display_name',
						$this->postType
					),
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
					'slug' => sprintf(
						'%s_trashing_user_firstname',
						$this->postType
					),
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
					'slug' => sprintf(
						'%s_trashing_user_lastname',
						$this->postType
					),
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
					'slug' => sprintf(
						'%s_trashing_user_avatar',
						$this->postType
					),
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
					'slug' => sprintf(
						'%s_trashing_user_role',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s trashing user role', 'notification'), $postTypeName),
					'property_name' => 'trashing_user',
					'group' => __('Trashing user', 'notification'),
				]
			)
		);
	}
}
