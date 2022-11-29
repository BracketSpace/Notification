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
	public function __construct( $postType = 'post' )
	{

		parent::__construct(
			[
			'post_type' => $postType,
			'slug' => 'post/' . $postType . '/pending',
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
		return sprintf(__('%s sent for review', 'notification'), WpObjectHelper::get_post_type_name($this->post_type));
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
			__('Fires when %1$s (%2$s) is sent for review', 'notification'),
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

		if ($oldStatus === 'pending' || $newStatus !== 'pending') {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$this->author = get_userdata((int)$this->{ $this->post_type }->post_author);
		$this->last_editor = get_userdata((int)get_post_meta($this->{ $this->post_type }->ID, '_edit_last', true));

		$this->{ $this->post_type . '_creation_datetime' } = strtotime($this->{ $this->post_type }->post_date_gmt);
		$this->{ $this->post_type . '_modification_datetime' } = strtotime($this->{ $this->post_type }->post_modified_gmt);
	}
}
