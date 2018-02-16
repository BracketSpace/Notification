<?php
/**
 * Comment ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Comment ID merge tag class
 */
class CommentID extends IntegerTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_ID',
			'name'        => __( 'Comment ID' ),
			'description' => '35',
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_ID;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->comment->comment_ID );
	}

}
