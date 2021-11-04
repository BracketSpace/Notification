<?php
/**
 * Avatar url merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\User;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;

/**
 * Avatar Url merge tag class
 */
class AvatarUrl extends UrlTag {
	/**
	 * Trigger property to get the user data from
	 *
	 * @var string
	 */
	protected $property_name = 'user_object';

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( array $params = [] ) {

		if ( isset( $params['property_name'] ) && ! empty( $params['property_name'] ) ) {
			$this->property_name = $params['property_name'];
		}

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'user_avatar_url',
				'name'        => __( 'User avatar url', 'notification' ),
				'description' => __( 'http://0.gravatar.com/avatar/320eab812ab24ef3dbaa2e6dc6e024e0?s=96&d=mm&r=g', 'notification' ),
				'example'     => true,
				'group'       => __( 'User', 'notification' ),
				'resolver'    => function ( $trigger ) {
					if ( isset( $trigger->{ $this->property_name }->user_email ) ) {
						return get_avatar_url( $trigger->{ $this->property_name }->user_email );
					}

					return '';
				},
			]
		);

		parent::__construct( $args );

	}
}
