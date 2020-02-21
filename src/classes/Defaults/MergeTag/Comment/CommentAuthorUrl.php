<?php
/**
 * Comment author URL merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
use BracketSpace\Notification\Traits;

/**
 * Comment author URL merge tag class
 */
class CommentAuthorUrl extends UrlTag {

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
				'slug'        => 'comment_author_url',
				// Translators: Comment type name.
				'name'        => sprintf( __( '%s author URL', 'notification' ), self::get_current_comment_type_name() ),
				'description' => __( 'http://mywebsite.com', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return $trigger->comment->comment_author_url;
				},
				// translators: comment type author.
				'group'       => sprintf( __( '%s author', 'notification' ), self::get_current_comment_type_name() ),
			]
		);

		parent::__construct( $args );

	}

}
