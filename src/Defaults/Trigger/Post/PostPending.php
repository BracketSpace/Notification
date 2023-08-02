<?php

/**
 * Post sent for review trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post sent for review trigger class
 */
class PostPending extends PostTrigger
{
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
				'slug' => 'post/' . $postType . '/pending',
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
				'%s sent for review',
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
				'Fires when %1$s (%2$s) is sent for review',
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
	 * @param \WP_Post $post Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($newStatus, $oldStatus, $post)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type !== $this->postType) {
			return false;
		}

		if ($oldStatus === 'pending' || $newStatus !== 'pending') {
			return false;
		}

		$this->post = $post;

		$this->author = get_userdata((int)$this->post->post_author);
		$this->lastEditor = get_userdata((int)get_post_meta($this->post->ID, '_edit_last', true));

		$this->postCreationDatetime = strtotime($this->post->post_date_gmt);
		$this->postModificationDatetime = strtotime($this->post->post_modified_gmt);
	}
}
