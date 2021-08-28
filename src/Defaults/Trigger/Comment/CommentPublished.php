<?php
/**
 * Comment published trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment published trigger class
 */
class CommentPublished extends CommentTrigger {

	/**
	 * Constructor
	 *
	 * @param string $comment_type optional, default: comment.
	 */
	public function __construct( $comment_type = 'comment' ) {

		parent::__construct( [
			'slug'         => 'comment/' . $comment_type . '/published',
			// Translators: %s comment type.
			'name'         => sprintf( __( '%s published', 'notification' ), WpObjectHelper::get_comment_type_name( $comment_type ) ),
			'comment_type' => $comment_type,
		] );

		$this->add_action( 'notification_comment_published_proxy', 10, 1 );

		// Translators: comment type.
		$this->set_description( sprintf( __( 'Fires when new %s is published on the website. Includes comment replies.', 'notification' ), WpObjectHelper::get_comment_type_name( $comment_type ) ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param object $comment Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $comment ) {

		if ( '1' !== $comment->comment_approved ) {
			return false;
		}

		if ( ! $this->is_correct_type( $comment ) ) {
			return false;
		}

		$this->comment = $comment;

		parent::assign_properties();

	}

}
