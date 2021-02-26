<?php
/**
 * Comment action delete URL merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
use BracketSpace\Notification\Traits;

/**
 * Comment action delete URL merge tag class
 */
class CommentActionDelete extends UrlTag {

	use Traits\Cache;

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

		$args = wp_parse_args(
			$params,
			[
				'slug'     => 'comment_delete_action_url',
				// Translators: Comment type name.
				'name'     => sprintf( __( '%s delete URL', 'notification' ), self::get_current_comment_type_name() ),
				'resolver' => function( $trigger ) {
					return admin_url( "comment.php?action=delete&c={$trigger->{ $this->property_name }->comment_ID}#wpbody-content" );
				},
				// translators: comment type actions text.
				'group'    => sprintf( __( '%s actions', 'notification' ), self::get_current_comment_type_name() ),
			]
		);

		parent::__construct( $args );

	}

}
