<?php
/**
 * Comment type merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment type merge tag class
 */
class CommentType extends StringTag {

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
				'slug'        => 'comment_type',
				'name'        => __( 'Comment type', 'notification' ),
				'description' => __( 'Comment or Pingback or Trackback or Custom', 'notification' ),
				'group'       => WpObjectHelper::get_comment_type_name( $this->comment_type ),
				'resolver'    => function ( $trigger ) {
					return get_comment_type( $trigger->{ $this->property_name } );
				},
			]
		);

		parent::__construct( $args );

	}

}
