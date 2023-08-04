<?php

/**
 * Post added trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post added trigger class
 */
class PostAdded extends PostTrigger
{
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
				'slug' => 'post/' . $postType . '/added',
			]
		);

		$this->addAction(
			'wp_insert_post',
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
			__('%s added', 'notification'),
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
				'Fires when %1$s (%2$s) is added to database.' .
				' Useful when adding posts programmatically or for 3rd party integration',
				'notification'
			),
			WpObjectHelper::getPostTypeName($this->postType),
			$this->postType
		);
	}

	/**
	 * Sets trigger's context
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @param int $postId Post ID.
	 * @param \WP_Post $post Post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($postId, $post, $update)
	{
		// Bail if post has been already added.
		if ($update) {
			return false;
		}

		// Controls if notification should be aborted if post is added from the admin.
		// If disabled, the notification will be
		// executed every time someone click the "Add new" button in the WordPress admin.
		$bailAutoDraft = apply_filters(
			'notification/trigger/wordpress/' . $this->postType . '/added/bail_auto_draft',
			true
		);

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($bailAutoDraft && $post->post_status === 'auto-draft') {
			return false;
		}

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type !== $this->postType) {
			return false;
		}

		$this->post = $post;

		$this->author = get_userdata((int)$this->post->post_author);
		$this->lastEditor = get_userdata((int)get_post_meta($this->post->ID, '_edit_last', true));
		$this->publishingUser = get_userdata(get_current_user_id());

		$this->postCreationDatetime = strtotime($this->post->post_date_gmt);
		$this->postModificationDatetime = strtotime($this->post->post_modified_gmt);
	}
}
