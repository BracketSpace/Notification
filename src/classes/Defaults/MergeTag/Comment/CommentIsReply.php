<?php
/**
 * Comment is reply merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Traits;

/**
 * Comment is reply merge tag class
 */
class CommentIsReply extends StringTag {

	use Traits\Cache;

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

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'comment_is_reply',
				// Translators: Comment type name.
				'name'        => sprintf( __( 'Is %s a reply?', 'notification' ), self::get_current_comment_type_name() ),
				'description' => __( 'Yes or No', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					$has_parent = $trigger->comment->comment_parent;
					return $has_parent ? __( 'Yes', 'notification' ) : __( 'No', 'notification' );
				},
				'group'       => __( self::get_current_comment_type_name(), 'notification' ),
			]
		);

		parent::__construct( $args );

	}

}
