<?php
/**
 * Comment content merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment content merge tag class
 */
class CommentContent extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

		$args = wp_parse_args( $params, array(
			'slug'        => 'comment_content',
			'name'        => __( 'Comment content', 'notification' ),
			'description' => __( 'Great post!', 'notification' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->comment->comment_content;
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
		return isset( $this->trigger->comment->comment_content );
	}

}
