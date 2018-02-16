<?php
/**
 * Comment author IP merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment author IP merge tag class
 */
class CommentAuthorIP extends StringTag {

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_author_IP',
			'name'        => __( 'Comment author IP' ),
			'description' => __( '127.0.0.1' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_author_IP;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->comment->comment_author_IP );

	}

}
