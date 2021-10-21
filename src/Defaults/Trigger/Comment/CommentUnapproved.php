<?php
/**
 * Comment unapproved trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment unapproved trigger class
 */
class CommentUnapproved extends CommentTrigger {

	/**
	 * Constructor
	 *
	 * @param string $comment_type optional, default: comment.
	 */
	public function __construct( $comment_type = 'comment' ) {

		parent::__construct( [
			'slug'         => 'comment/' . $comment_type . '/unapproved',
			// Translators: %s comment type.
			'name'         => sprintf( __( '%s unapproved', 'notification' ), WpObjectHelper::get_comment_type_name( $comment_type ) ),
			'comment_type' => $comment_type,
		] );

		$this->add_action( 'transition_comment_status', 10, 3 );

		// translators: comment type.
		$this->set_description( sprintf( __( 'Fires when %s is marked as unapproved', 'notification' ), WpObjectHelper::get_comment_type_name( $comment_type ), 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $comment_new_status New comment status.
	 * @param string $comment_old_status Old comment status.
	 * @param object $comment            Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $comment_new_status, $comment_old_status, $comment ) {

		$this->comment = $comment;

		if ( 'spam' === $this->comment->comment_approved && notification_get_setting( 'triggers/comment/akismet' ) ) {
			return false;
		}

		if ( ! $this->is_correct_type( $this->comment ) ) {
			return false;
		}

		if ( $comment_new_status === $comment_old_status || 'unapproved' !== $comment_new_status ) {
			return false;
		}

		parent::assign_properties();

	}

}
