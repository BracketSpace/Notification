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
	public function __construct( $params = [] ) {

		if ( isset( $params['property_name'] ) && ! empty( $params['property_name'] ) ) {
			$this->property_name = $params['property_name'];
		}

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
						$this->trigger->{ $this->property_name }->roles
					);

					return implode( ', ', $roles );
				},
			]
		);

		parent::__construct( $args );

	}

}
