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
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

		$args = wp_parse_args( $params, array(
			'slug'        => 'comment_author_url',
			'name'        => __( 'Comment author URL', 'notification' ),
			'description' => __( 'http://mywebsite.com', 'notification' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_author_url;
			},
		) );

    	parent::__construct( $args );

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
