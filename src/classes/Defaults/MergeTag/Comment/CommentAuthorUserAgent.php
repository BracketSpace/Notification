<?php
/**
 * Comment author user agent merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Traits;

/**
 * Comment author user agent tag class
 */
class CommentAuthorUserAgent extends StringTag {

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
				'slug'        => 'comment_author_user_agent',
				// Translators: Comment type name.
				'name'        => sprintf( __( '%s author user browser agent', 'notification' ), self::get_current_comment_type_name() ),
				'description' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0',
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return $trigger->comment->comment_agent;
				},
				// translators: comment type author.
				'group'       => sprintf( __( '%s author', 'notification' ), self::get_current_comment_type_name() ),
			]
		);

		parent::__construct( $args );

	}

}
