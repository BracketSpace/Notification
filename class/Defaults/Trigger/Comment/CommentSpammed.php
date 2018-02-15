<?php
/**
 * Comment spammed trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Comment;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * Comment spammed trigger class
 */
class CommentSpammed extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct( $comment_type = 'comment' ) {

		parent::__construct( 'wordpress/comment_' . $comment_type . '_spammed', ucfirst( $comment_type ) . ' spammed' );

		$this->add_action( 'spammed_comment', 10, 2 );
		$this->set_group( sprintf( __( '%s', 'notification' ), __( ucfirst( $comment_type ), 'notification' ) ) );
		$this->set_description( 'Fires when new ' . $comment_type . ' is spammed' );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function action() {

		$this->comment_status = $this->callback_args[0];
		$this->comment = $this->callback_args[1];




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
