<?php
/**
 * Comment unapproved trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Comment;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * Comment unapproved trigger class
 */
class CommentUnapproved extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct( $comment_type ) {

		parent::__construct( 'wordpress/comment_' . $comment_type . '_unapproved', ucfirst( $comment_type ) . ' unapproved' );

		$this->add_action( 'comment_unapproved_' . $action_type, 10, 2 );
		$this->set_group( sprintf( __( '%s', 'notification' ), __( ucfirst( $comment_type ), 'notification' ) ) );
		$this->set_description( 'Fires when new ' . $comment_type . ' is unapproved' );

	}

	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action() {

		$this->comment_ID = $this->callback_args[0];
		$this->comment    = $this->callback_args[1];
		print_r( $this->callback_args);

		if ( $this->comment->status == 'spam' ) {
			return false;
		}

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Comment\CommentID( $this ) );


    }

}
