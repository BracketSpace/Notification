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
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

		$args = wp_parse_args( $params, array(
			'slug'        => 'comment_post_permalink',
			'name'        => __( 'Comment post permalink', 'notification' ),
			'description' => __( 'https://example.com/hello-world/', 'notification' ),
			'example'     => true,
			'resolver'    => function() {
				return get_permalink( $this->trigger->comment->comment_post_ID );
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
		return isset( $this->trigger->comment->comment_post_ID );
	}

}
