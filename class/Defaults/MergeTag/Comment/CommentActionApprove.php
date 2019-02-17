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
	 * Trigger property to get the comment data from
	 *
	 * @var string
	 */
	protected $property_name = 'comment';

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['property_name'] ) && ! empty( $params['property_name'] ) ) {
			$this->property_name = $params['property_name'];
		}

		$args = wp_parse_args(
			$params,
			[
				'slug'     => 'comment_approve_action_url',
				'name'     => __( 'Comment approve URL', 'notification' ),
				'resolver' => function( $trigger ) {
					return admin_url( "comment.php?action=approve&c={$trigger->{ $this->property_name }->comment_ID}#wpbody-content" );
				},
				// translators: comment type actions text.
				'group'    => sprintf( __( '%s actions', 'notification' ), ucfirst( $this->property_name ) ),
			]
		);

		parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements() {
		return isset( $this->trigger->{ $this->property_name }->comment_ID );
	}

}
