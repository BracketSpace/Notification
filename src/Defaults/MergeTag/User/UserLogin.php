<?php
/**
 * User login merge tag
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object, preferabely WP_User
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\User;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * User login merge tag class
 */
class UserLogin extends StringTag {
	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['property_name'] ?? 'user_object' );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'user_login',
				'name'        => __( 'User login', 'notification' ),
				'description' => __( 'johndoe', 'notification' ),
				'example'     => true,
				'group'       => __( 'User', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->user_login;
				},
			]
		);

		parent::__construct( $args );

	}

}
