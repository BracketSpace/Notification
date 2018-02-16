<?php
/**
 * Comment approved trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Comment;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * Comment added trigger class
 */
class CommentApproved extends Abstracts\Trigger {

	/**
	 * Constructor
	 *
	 * @param string $comment_type optional, default: comment.
	 */
	public function __construct( $comment_type = 'comment' ) {

		parent::__construct( 'wordpress/comment_' . $comment_type . '_approved', ucfirst( $comment_type ) . ' approved' );

		$this->add_action( 'transition_comment_status', 10, 3 );
		$this->set_group( __( ucfirst( $comment_type ), 'notification' ) );

		// translators: comment type.
		$this->set_description( sprintf( __( 'Fires when %s is approved', 'notification' ), __( ucfirst( $comment_type ), 'notification' ) ) );

	}

	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action() {

		$this->comment_new_status         = $this->callback_args[0];
		$this->comment_old_status         = $this->callback_args[1];
		$this->comment                    = $this->callback_args[2];
		$this->user_object->ID            = $this->comment->user_id;
		$this->user_object->user_nicename = $this->comment->comment_author;
		$this->user_object->user_email    = $this->comment->comment_author_email;

		if ( $this->comment->comment_approved == 'spam' && notification_get_setting( 'triggers/comment/akismet' ) ) {
			return false;
		}

		if ( $this->comment_new_status == $this->comment_old_status || $this->comment_new_status != 'approved' ) {
			return false;
		}

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Comment\CommentID() );
		$this->add_merge_tag( new MergeTag\Comment\CommentContent() );
		$this->add_merge_tag( new MergeTag\Comment\CommentApproved() );
		$this->add_merge_tag( new MergeTag\Comment\CommentType() );
		$this->add_merge_tag( new MergeTag\Comment\CommentPostID() );
		$this->add_merge_tag( new MergeTag\Comment\CommentPostPermalink() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorIP() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUserAgent() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUrl() );


		// Author.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug' => 'comment_author_user_ID',
			'name' => __( 'Comment author user ID' ),
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug' => 'comment_author_user_email',
			'name' => __( 'Comment author user email' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( array(
			'slug' => 'comment_author_user_nicename',
			'name' => __( 'Comment author user nicename' ),
		) ) );


    }

}
