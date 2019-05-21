<?php
/**
 * Comment added trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Comment trashed trigger class
 */
class CommentTrashed extends CommentTrigger {

	/**
	 * Constructor
	 *
	 * @param string $comment_type optional, default: comment.
	 */
	public function __construct( $comment_type = 'comment' ) {

		parent::__construct( [
			'slug'         => 'wordpress/comment_' . $comment_type . '_trashed',
			// Translators: %s comment type.
			'name'         => sprintf( __( '%s trashed', 'notification' ), ucfirst( $comment_type ) ),
			'comment_type' => $comment_type,
		] );

		$this->add_action( 'trashed_comment', 10, 2 );

		// translators: comment type.
		$this->set_description( sprintf( __( 'Fires when %s is trashed', 'notification' ), __( ucfirst( $comment_type ), 'notification' ) ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param integer $comment_id Comment ID.
	 * @param object  $comment    Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $comment_id, $comment ) {

		$this->comment = $comment;

		if ( 'spam' === $this->comment->comment_approved && notification_get_setting( 'triggers/comment/akismet' ) ) {
			return false;
		}

		if ( ! $this->is_correct_type( $this->comment ) ) {
			return false;
		}

		// fix for action being called too early, before WP marks the comment as trashed.
		$this->comment->comment_approved = 'trash';

		parent::assign_properties();

	}

}
