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
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( array $params = [] ) {

		$this->set_trigger_prop( $params['property_name'] ?? 'user_object' );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'user_avatar_url',
				'name'        => __( 'User avatar url', 'notification' ),
				'description' => __( 'http://0.gravatar.com/avatar/320eab812ab24ef3dbaa2e6dc6e024e0?s=96&d=mm&r=g', 'notification' ),
				'example'     => true,
				'group'       => __( 'User', 'notification' ),
				'resolver'    => function ( $trigger ) {
					if ( isset( $trigger->{ $this->get_trigger_prop() }->user_email ) ) {
						return get_avatar_url( $trigger->{ $this->get_trigger_prop() }->user_email );
					}

					return '';
				},
			]
		);

		parent::__construct( $args );

	}
}
