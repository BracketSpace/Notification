<?php
/**
 * Comment post ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Comment post ID merge tag class
 */
class CommentPostID extends IntegerTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_post_ID',
			'name'        => __( 'Comment post ID' ),
			'description' => '25',
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
