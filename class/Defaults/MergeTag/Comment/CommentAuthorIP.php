<?php
/**
 * Comment author IP merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\IPTag;


/**
 * Comment author IP merge tag class
 */
class CommentAuthorIP extends IPTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_author_IP',
			'name'        => __( 'Comment author IP' ),
			'description' => '127.0.0.1',
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
