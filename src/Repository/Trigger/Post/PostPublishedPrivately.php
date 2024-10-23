<?php

/**
 * Post published privately trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Post;

use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post published privately trigger class
 */
class PostPublishedPrivately extends PostTrigger
{
	/**
	 * Status name of published post
	 *
	 * @var string
	 */
	protected static $publishStatus = 'private';

	/**
	 * Post publishing user object
	 *
	 * @var \WP_User|false
	 */
	public $publishingUser;

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
				'slug' => 'post/' . $postType . '/published-privately',
			]
		);

		$this->addAction('transition_post_status', 10, 3);
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
			__('%s published privately', 'notification'),
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
			__('Fires when %1$s (%2$s) is published privately', 'notification'),
			WpObjectHelper::getPostTypeName($this->postType),
			$this->postType
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $newStatus New post status.
	 * @param string $oldStatus Old post status.
	 * @param \WP_Post $post Post object.
	 * @return false|void
	 */
	public function context($newStatus, $oldStatus, $post)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type !== $this->postType) {
			return false;
		}

		if (self::$publishStatus === $oldStatus || self::$publishStatus !== $newStatus) {
			return false;
		}

		$this->post = $post;

		$this->author = get_userdata((int)$this->post->post_author);
		$this->lastEditor = get_userdata((int)get_post_meta($this->post->ID, '_edit_last', true));
		$this->publishingUser = get_userdata(get_current_user_id());
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

		// Publishing user.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => sprintf(
						'%s_publishing_user_ID',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user ID', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
					'slug' => sprintf(
						'%s_publishing_user_login',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user login', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => sprintf(
						'%s_publishing_user_email',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user email', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
					'slug' => sprintf(
						'%s_publishing_user_nicename',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user nicename', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNickname(
				[
					'slug' => sprintf(
						'%s_publishing_user_nickname',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user nickname', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => sprintf(
						'%s_publishing_user_display_name',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user display name', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
					'slug' => sprintf(
						'%s_publishing_user_firstname',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user first name', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
					'slug' => sprintf(
						'%s_publishing_user_lastname',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user last name', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
					'slug' => sprintf(
						'%s_publishing_user_avatar',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user avatar', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserRole(
				[
					'slug' => sprintf(
						'%s_publishing_user_role',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publishing user role', 'notification'), $postTypeName),
					'property_name' => 'publishingUser',
					'group' => __('Publishing user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => sprintf(
						'%s_publication_datetime',
						$this->postType
					),
					// translators: singular post name.
					'name' => sprintf(__('%s publication date and time', 'notification'), $postTypeName),
					'timestamp' => static function ($trigger) {
						return strtotime($trigger->post->post_date_gmt);
					},
				]
			)
		);
	}
}
