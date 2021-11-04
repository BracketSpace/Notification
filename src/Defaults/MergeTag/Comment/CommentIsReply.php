<?php
/**
 * Comment is reply merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment is reply merge tag class
 */
class CommentIsReply extends StringTag {

	/**
	 * Trigger property to get the comment data from
	 *
	 * @var string
	 */
	protected $comment_type = 'comment';

	/**
	 * Trigger property name to get the comment data from
	 *
	 * @var string
	 */
	protected $property_name = '';

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

		if ( isset( $params['property_name'] ) && ! empty( $params['property_name'] ) ) {
			$this->property_name = $params['property_name'];
		} else {
			$this->property_name = $this->comment_type;
		}

		$comment_type_name = WpObjectHelper::get_comment_type_name( $this->comment_type );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'comment_is_reply',
				// Translators: Comment type name.
				'name'        => sprintf( __( 'Is %s a reply?', 'notification' ), $comment_type_name ),
				'description' => __( 'Yes or No', 'notification' ),
				'example'     => true,
				'group'       => $comment_type_name,
				'resolver'    => function ( $trigger ) {
					$has_parent = $trigger->{ $this->property_name }->comment_parent;
					return $has_parent ? __( 'Yes', 'notification' ) : __( 'No', 'notification' );
				},
			]
		);

		parent::__construct( $args );

	}

}
