<?php
/**
 * Comment author user agent merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment author user agent tag class
 */
class CommentAuthorUserAgent extends StringTag {

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_author_user_agent',
			'name'        => __( 'Comment author user agent' ),
			'description' => __( 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_agent;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->comment->comment_agent );

	}

}
