<?php
/**
 * Comment author IP merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\IPTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment author IP merge tag class
 */
class CommentAuthorIP extends IPTag {

	/**
	 * Trigger property to get the comment data from
	 *
	 * @var string
	 */
	protected $comment_type = 'comment';

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['comment_type'] ) && ! empty( $params['comment_type'] ) ) {
			$this->comment_type = $params['comment_type'];
		}

		$this->set_trigger_prop( $params['property_name'] ?? 'comment' );

		$comment_type_name = WpObjectHelper::get_comment_type_name( $this->comment_type );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'comment_author_IP',
				// Translators: Comment type name.
				'name'        => sprintf( __( '%s author IP', 'notification' ), $comment_type_name ),
				'description' => '127.0.0.1',
				'example'     => true,
				// Translators: comment type author.
				'group'       => sprintf( __( '%s author', 'notification' ), $comment_type_name ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->comment_author_IP;
				},
			]
		);

		parent::__construct( $args );

	}

}
