<?php
/**
 * Comment post permalink merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment post permalink merge tag class
 */
class CommentPostPermalink extends StringTag {

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_post_permalink',
			'name'        => __( 'Comment post_permalink' ),
			'description' => __( 'https://example.com/hello-world/' ),
			'example'     => true,
			'resolver'    => function() {
				return get_permalink( $this->trigger->comment->comment_post_ID );
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
