<?php
/**
 * Comment is reply merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment is reply merge tag class
 */
class CommentIsReply extends StringTag {

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
				'slug'        => 'comment_is_reply',
				'name'        => __( 'Is comment a reply?', 'notification' ),
				'description' => __( 'Yes or No', 'notification' ),
				'example'     => true,
				'resolver'    => function() {
					$has_parent = $this->trigger->{ $this->property_name }->comment_parent;
					return $has_parent ? __( 'Yes', 'notification' ) : __( 'No', 'notification' );
				},
				'group'       => __( ucfirst( $this->property_name ), 'notification' ),
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
		return isset( $this->trigger->{ $this->property_name } );
	}

}
