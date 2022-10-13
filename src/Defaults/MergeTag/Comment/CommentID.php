<?php
/**
 * Comment ID merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment ID merge tag class
 */
class CommentID extends IntegerTag {

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

		$this->set_trigger_prop( $params['property_name'] ?? $this->comment_type );

		$comment_type_name = WpObjectHelper::get_comment_type_name( $this->comment_type );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'comment_ID',
				// Translators: Comment type name.
				'name'        => sprintf( __( '%s ID', 'notification' ), $comment_type_name ),
				'description' => '35',
				'example'     => true,
				'group'       => $comment_type_name,
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->comment_ID;
				},
			]
		);

		parent::__construct( $args );

	}

}
