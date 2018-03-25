<?php
/**
 * Comment action approve URL merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;


/**
 * Comment action approve URL merge tag class
 */
class CommentActionApprove extends UrlTag {

	/**
     * Merge tag constructor
     *
     * @since 5.0.0
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

		$args = wp_parse_args( $params, array(
			'slug'        => 'comment_approve_action_url',
			'name'        => __( 'Comment approve URL', 'notification' ),
			'resolver'    => function( $trigger ) {
				return admin_url( "comment.php?action=approve&c={$trigger->comment->comment_ID}#wpbody-content" );
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
		return isset( $this->trigger->comment->comment_ID );
	}

}
