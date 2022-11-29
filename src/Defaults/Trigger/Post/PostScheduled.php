<?php

/**
 * Post sent for reviewscheduled trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post sent for review trigger class
 */
class PostScheduled extends PostTrigger
{

	/**
	 * Post scheduling user object
	 *
	 * @var \WP_User|false
	 */
	public $schedulingUser;

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
			'slug' => 'post/' . $postType . '/scheduled',
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
		// translators: singular post name.
		return sprintf(__('%s scheduled', 'notification'), WpObjectHelper::get_post_type_name($this->postType));
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
			__('Fires when %1$s (%2$s) is scheduled', 'notification'),
			WpObjectHelper::get_post_type_name($this->postType),
			$this->postType
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $newStatus New post status.
	 * @param string $oldStatus Old post status.
	 * @param object $post       Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $newStatus, $oldStatus, $post )
	{

		if ($post->postType !== $this->postType) {
			return false;
		}

		if ($oldStatus === 'future' || $newStatus !== 'future') {
			return false;
		}

		$this->{ $this->postType } = $post;

		$schedulingUserId = get_current_user_id();

		$this->author = get_userdata((int)$this->{ $this->postType }->postAuthor);
		$this->lastEditor = get_userdata((int)get_post_meta($this->{ $this->postType }->ID, '_edit_last', true));
		$this->schedulingUser = get_userdata($schedulingUserId);

		$this->{ $this->postType . '_creation_datetime' } = strtotime($this->{ $this->postType }->postDateGmt);
		$this->{ $this->postType . '_publication_datetime' } = strtotime($this->{ $this->postType }->postDateGmt);
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

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => sprintf('%s_publication_datetime', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s publication date and time', 'notification'), $postTypeName),
				]
			)
		);

		// Scheduling user.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
				'slug' => sprintf('%s_scheduling_user_ID', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user ID', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
				'slug' => sprintf('%s_scheduling_user_login', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user login', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
				'slug' => sprintf('%s_scheduling_user_email', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user email', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
				'slug' => sprintf('%s_scheduling_user_nicename', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user nicename', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
				'slug' => sprintf('%s_scheduling_user_display_name', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user display name', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
				'slug' => sprintf('%s_scheduling_user_firstname', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user first name', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
				'slug' => sprintf('%s_scheduling_user_lastname', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user last name', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
				'slug' => sprintf('%s_scheduling_user_avatar', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user email', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserRole(
				[
				'slug' => sprintf('%s_scheduling_user_role', $this->postType),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user role', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);
	}
}
