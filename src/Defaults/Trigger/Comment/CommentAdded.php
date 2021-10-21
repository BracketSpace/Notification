<?php
/**
 * Comment added trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment added trigger class
 */
class CommentAdded extends CommentTrigger {

	/**
	 * Constructor
	 *
	 * @param string $comment_type optional, default: comment.
	 */
	public function __construct( $comment_type = 'comment' ) {

		parent::__construct( [
			'slug'         => 'comment/' . $comment_type . '/added',
			// Translators: %s comment type.
			'name'         => sprintf( __( '%s added', 'notification' ), WpObjectHelper::get_comment_type_name( $comment_type ) ),
			'comment_type' => $comment_type,
		] );

		$this->add_action( 'wp_insert_comment', 10, 2 );

		// Translators: comment type.
		$this->set_description( sprintf( __( 'Fires when new %s is added to database and awaits moderation or is published. Includes comment replies.', 'notification' ), WpObjectHelper::get_comment_type_name( $comment_type ) ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer $comment_id Comment ID.
	 * @param object  $comment    Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $comment_id, $comment ) {

		$this->comment = $comment;

		if ( 'spam' === $this->comment->comment_approved && notification_get_setting( 'triggers/comment/akismet' ) ) {
			return false;
		}

		if ( ! $this->is_correct_type( $this->comment ) ) {
			return false;
		}

		parent::assign_properties();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\Comment\CommentActionApprove( [
			'comment_type' => $this->comment_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentActionTrash( [
			'comment_type' => $this->comment_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentActionDelete( [
			'comment_type' => $this->comment_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentActionSpam( [
			'comment_type' => $this->comment_type,
		] ) );

	}

}
