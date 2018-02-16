<?php
/**
 * Comment post ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment post ID merge tag class
 */
class CommentPostID extends StringTag {

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_post_ID',
			'name'        => __( 'Comment post ID' ),
			'description' => __( '25' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_post_ID;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->comment->comment_post_ID );

	}

}
