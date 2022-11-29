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
	 * @param object $post Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($newStatus, $oldStatus, $post)
	{

		if ($post->postType !== $this->postType) {
			return false;
		}

		if ($oldStatus === 'pending' || $newStatus !== 'pending') {
			return false;
		}

		$this->{$this->postType} = $post;

		$this->author = get_userdata((int)$this->{$this->postType}->postAuthor);
		$this->lastEditor = get_userdata(
			(int)get_post_meta(
				$this->{$this->postType}->ID,
				'_edit_last',
				true
			)
		);

		$this->{$this->postType . '_creation_datetime'} = strtotime($this->{$this->postType}->postDateGmt);
		$this->{$this->postType . '_modification_datetime'} = strtotime($this->{$this->postType}->postModifiedGmt);
	}
}
