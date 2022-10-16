<?php
/**
 * User role merge tag
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
 * User role merge tag class
 */
class UserRole extends StringTag {
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
				'slug'        => 'user_role',
				'name'        => __( 'User role', 'notification' ),
				'description' => __( 'Subscriber', 'notification' ),
				'example'     => true,
				'group'       => __( 'User', 'notification' ),
				'resolver'    => function () {
					$roles = array_map(
						function ( $role ) {
							$role_object = get_role( $role );
							return translate_user_role( ucfirst( $role_object->name ) );
						},
						$this->trigger->{ $this->get_trigger_prop() }->roles
					);

					return implode( ', ', $roles );
				},
			]
		);

		parent::__construct( $args );

	}

}
