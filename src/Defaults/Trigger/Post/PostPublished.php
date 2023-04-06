<?php

/**
 * Post published trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post published trigger class
 */
class PostPublished extends PostTrigger
{
	/**
	 * Status name of published post
	 *
	 * @var string
	 */
	protected static $publishStatus = 'publish';

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
				'slug' => 'post/' . $postType . '/published',
			]
		);

		$this->addAction(
			'transition_post_status',
			10,
			3
		);
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
			__(
				'%s published',
				'notification'
			),
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
			__(
				'Fires when %1$s (%2$s) is published',
				'notification'
			),
			WpObjectHelper::getPostTypeName($this->postType),
			$this->postType
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $newStatus New post status.
	 * @param string $oldStatus Old post status.
	 * @param object $post Post object.
	 * @return mixed void or false if no notifications should be sent
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

		/** @var \WP_Post $post */
		$this->posts[$this->postType] = $post;

		$this->author = get_userdata((int)$this->posts[$this->postType]->post_author);
		$this->lastEditor = get_userdata(
			(int)get_post_meta(
				$this->posts[$this->postType]->ID,
				'_edit_last',
				true
			)
		);
		$this->publishingUser = get_userdata(get_current_user_id());

		$this->{$this->postType . '_creation_datetime'} = strtotime(
			$this->posts[$this->postType]->post_date_gmt
		);
		$this->{$this->postType . '_publication_datetime'} = strtotime(
			$this->posts[$this->postType]->post_date_gmt
		);
		$this->{$this->postType . '_modification_datetime'} = strtotime(
			$this->posts[$this->postType]->post_modified_gmt
		);
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user ID',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user login',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user email',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user nicename',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user display name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user first name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user last name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user avatar',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publishing user role',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'publishing_user',
					'group' => __(
						'Publishing user',
						'notification'
					),
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
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s publication date and time',
							'notification'
						),
						$postTypeName
					),
				]
			)
		);
	}
}
