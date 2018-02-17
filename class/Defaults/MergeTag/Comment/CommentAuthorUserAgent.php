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
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

		$args = wp_parse_args( $params, array(
			'slug'        => 'comment_author_user_agent',
			'name'        => __( 'Comment author user agent' ),
			'description' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0',
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_agent;
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
		return isset( $this->trigger->comment->comment_agent );
	}

}
