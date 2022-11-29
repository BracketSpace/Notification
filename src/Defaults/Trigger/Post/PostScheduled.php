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

		$this->add_action('transition_post_status', 10, 3);
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name(): string
	{
		// translators: singular post name.
		return sprintf(__('%s scheduled', 'notification'), WpObjectHelper::get_post_type_name($this->post_type));
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
			__('Fires when %1$s (%2$s) is scheduled', 'notification'),
			WpObjectHelper::get_post_type_name($this->post_type),
			$this->post_type
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

		if ($post->post_type !== $this->post_type) {
			return false;
		}

		if ($oldStatus === 'future' || $newStatus !== 'future') {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$schedulingUserId = get_current_user_id();

		$this->author = get_userdata((int)$this->{ $this->post_type }->post_author);
		$this->last_editor = get_userdata((int)get_post_meta($this->{ $this->post_type }->ID, '_edit_last', true));
		$this->scheduling_user = get_userdata($schedulingUserId);

		$this->{ $this->post_type . '_creation_datetime' } = strtotime($this->{ $this->post_type }->post_date_gmt);
		$this->{ $this->post_type . '_publication_datetime' } = strtotime($this->{ $this->post_type }->post_date_gmt);
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

		$this->add_merge_tag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => sprintf('%s_publication_datetime', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s publication date and time', 'notification'), $postTypeName),
				]
			)
		);

		// Scheduling user.
		$this->add_merge_tag(
			new MergeTag\User\UserID(
				[
				'slug' => sprintf('%s_scheduling_user_ID', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user ID', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLogin(
				[
				'slug' => sprintf('%s_scheduling_user_login', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user login', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserEmail(
				[
				'slug' => sprintf('%s_scheduling_user_email', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user email', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserNicename(
				[
				'slug' => sprintf('%s_scheduling_user_nicename', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user nicename', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserDisplayName(
				[
				'slug' => sprintf('%s_scheduling_user_display_name', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user display name', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserFirstName(
				[
				'slug' => sprintf('%s_scheduling_user_firstname', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user first name', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLastName(
				[
				'slug' => sprintf('%s_scheduling_user_lastname', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user last name', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\Avatar(
				[
				'slug' => sprintf('%s_scheduling_user_avatar', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user email', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserRole(
				[
				'slug' => sprintf('%s_scheduling_user_role', $this->post_type),
				// translators: singular post name.
				'name' => sprintf(__('%s scheduling user role', 'notification'), $postTypeName),
				'property_name' => 'scheduling_user',
				'group' => __('Scheduling user', 'notification'),
				]
			)
		);
	}
}
