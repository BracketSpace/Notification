<?php
/**
 * Comment post permalink merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\UrlTag;


/**
 * Comment post permalink merge tag class
 */
class CommentPostPermalink extends UrlTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_post_permalink',
			'name'        => __( 'Comment post permalink' ),
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
