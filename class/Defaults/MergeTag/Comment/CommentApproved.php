<?php
/**
 * Comment approved merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment approved merge tag class
 */
class CommentApproved extends StringTag {

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_approved',
			'name'        => __( 'Comment status' ),
			'description' => __( 'Approved' ),
			'example'     => true,
			'resolver'    => function() {
				if ( $this->trigger->comment->comment_approved  == 1 ) {

					return __( 'Approved' );

				} elseif ( $this->trigger->comment->comment_approved  == 0 ) {

					return __( 'Unapproved' );

				} elseif ( $this->trigger->comment->comment_approved  == 'spam' ) {

					return __( 'Marked as spam' );

				}
 			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->comment->comment_approved );

	}

}
