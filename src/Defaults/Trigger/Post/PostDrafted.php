<?php

/**
 * Post drafted trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post drafted trigger class
 */
class PostDrafted extends PostTrigger
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
				'slug' => 'post/' . $postType . '/drafted',
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
				'%s saved as a draft',
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
				'Fires when %1$s (%2$s) is saved as a draft',
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

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return false;
		}

		if ($newStatus !== 'draft') {
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
		$this->publishingUser = get_userdata(get_current_user_id());

		$this->{$this->postType . '_creation_datetime'} = strtotime($this->{$this->postType}->postDateGmt);
		$this->{$this->postType . '_modification_datetime'} = strtotime($this->{$this->postType}->postModifiedGmt);
	}
}
