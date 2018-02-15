<?php
/**
 * Comment added trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Comment;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * Comment trashed trigger class
 */
class CommentTrashed extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct( $comment_type ) {

		parent::__construct( 'wordpress/comment_' . $comment_type . '_trashed', ucfirst( $comment_type ) . ' trashed' );

		$this->add_action( 'comment_trashed_' . $comment_type, 10, 2 );
		$this->set_group( sprintf( __( '%s', 'notification' ), __( ucfirst( $comment_type ), 'notification' ) ) );
		$this->set_description( 'Fires when new ' . $comment_type . ' is trashed' );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function action() {

		$this->comment_ID = $this->callback_args[0];
		$this->comment = $this->callback_args[1];

		if ( $this->comment->status == 'spam' ) {

			$this->stop();

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
