<?php
/**
 * Comment content html merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\HtmlTag;
use BracketSpace\Notification\Traits;

/**
 * Comment content html merge tag class
 */
class CommentContentHtml extends HtmlTag {

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
				'slug'        => 'comment_content_html',
				// Translators: Comment type name.
				'name'        => sprintf( __( '%s HTML content', 'notification' ), self::get_current_comment_type_name() ),
				'description' => __( 'Great post!', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return $trigger->comment->comment_content;
				},
				'group'       => __( self::get_current_comment_type_name(), 'notification' ),
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
		return isset( $this->trigger->{ $this->comment_type }->comment_content );
	}

}
