<?php
/**
 * Comment author URL merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\UrlTag;


/**
 * Comment author URL merge tag class
 */
class CommentAuthorUrl extends UrlTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'comment_author_url',
			'name'        => __( 'Comment author URL' ),
			'description' => __( 'http://mywebsite.com' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_author_url;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->comment->comment_author_url );
	}

}
