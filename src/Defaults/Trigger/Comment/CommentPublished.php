<?php

/**
 * Comment published trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment published trigger class
 */
class CommentPublished extends CommentTrigger
{

	/**
	 * Constructor
	 *
	 * @param string $commentType optional, default: comment.
	 */
	public function __construct( $commentType = 'comment' )
	{

		parent::__construct(
			[
			'slug' => 'comment/' . $commentType . '/published',
			// Translators: %s comment type.
			'name' => sprintf(__('%s published', 'notification'), WpObjectHelper::get_comment_type_name($commentType)),
			'comment_type' => $commentType,
			]
		);

		$this->add_action('notification_comment_published_proxy', 10, 1);

		// Translators: comment type.
		$this->set_description(sprintf(__('Fires when new %s is published on the website. Includes comment replies.', 'notification'), WpObjectHelper::get_comment_type_name($commentType)));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param object $comment Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $comment )
	{

		if ($comment->comment_approved !== '1') {
			return false;
		}

		if (! $this->is_correct_type($comment)) {
			return false;
		}

		$this->comment = $comment;

		parent::assign_properties();
	}
}
