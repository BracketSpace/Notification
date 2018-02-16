<?php
/**
 * Comment content merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment content merge tag class
 */
class CommentContent extends StringTag {

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_content',
			'name'        => __( 'Comment content' ),
			'description' => __( 'Great post!' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_content;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->comment->comment_content );

	}

}
