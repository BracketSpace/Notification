<?php

/**
 * Comment approved trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment added trigger class
 */
class CommentApproved extends CommentTrigger
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
			'slug' => 'comment/' . $commentType . '/approved',
			// Translators: %s comment type.
			'name' => sprintf(__('%s approved', 'notification'), WpObjectHelper::get_comment_type_name($commentType)),
			'comment_type' => $commentType,
			]
		);

		$this->add_action('transition_comment_status', 10, 3);

		// translators: comment type.
		$this->set_description(sprintf(__('Fires when %s is approved', 'notification'), WpObjectHelper::get_comment_type_name($commentType)));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $commentNewStatus New comment status.
	 * @param string $commentOldStatus Old comment status.
	 * @param object $comment            Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $commentNewStatus, $commentOldStatus, $comment )
	{

		$this->comment = $comment;

		if ($this->comment->comment_approved === 'spam' && notification_get_setting('triggers/comment/akismet')) {
			return false;
		}

		if (! $this->is_correct_type($this->comment)) {
			return false;
		}

		if ($commentNewStatus === $commentOldStatus || $commentNewStatus !== 'approved') {
			return false;
		}

		parent::assign_properties();
	}
}
