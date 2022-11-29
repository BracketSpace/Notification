<?php

/**
 * Comment added trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment trashed trigger class
 */
class CommentTrashed extends CommentTrigger
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
			'slug' => 'comment/' . $commentType . '/trashed',
			// Translators: %s comment type.
			'name' => sprintf(__('%s trashed', 'notification'), WpObjectHelper::get_comment_type_name($commentType)),
			'comment_type' => $commentType,
			]
		);

		$this->add_action('trashed_comment', 10, 2);

		// translators: comment type.
		$this->set_description(sprintf(__('Fires when %s is trashed', 'notification'), WpObjectHelper::get_comment_type_name($commentType)));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $commentId Comment ID.
	 * @param object  $comment    Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $commentId, $comment )
	{

		$this->comment = $comment;

		if ($this->comment->comment_approved === 'spam' && notification_get_setting('triggers/comment/akismet')) {
			return false;
		}

		if (! $this->is_correct_type($this->comment)) {
			return false;
		}

		// fix for action being called too early, before WP marks the comment as trashed.
		$this->comment->comment_approved = 'trash';

		parent::assign_properties();
	}
}
