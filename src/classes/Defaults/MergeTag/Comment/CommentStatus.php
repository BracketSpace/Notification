<?php
/**
 * Comment status merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Comment status merge tag class
 */
class CommentStatus extends StringTag {

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
				'slug'        => 'comment_status',
				'name'        => __( 'Comment status', 'notification' ),
				'description' => __( 'Approved', 'notification' ),
				'example'     => true,
				'resolver'    => function() {
					if ( '1' === $this->trigger->{ $this->property_name }->comment_approved ) {
						return __( 'Approved', 'notification' );
					} elseif ( '0' === $this->trigger->{ $this->property_name }->comment_approved ) {
						return __( 'Unapproved', 'notification' );
					} elseif ( 'spam' === $this->trigger->{ $this->property_name }->comment_approved ) {
						return __( 'Marked as spam', 'notification' );
					} elseif ( 'trash' === $this->trigger->{ $this->property_name }->comment_approved ) {
						return __( 'Trashed', 'notification' );
					}
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
		return isset( $this->trigger->{ $this->property_name }->comment_approved );
	}

}
