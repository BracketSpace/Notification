<?php
/**
 * User role merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User role merge tag class
 */
class UserRole extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'user_role',
			'name'        => __( 'User role' ),
			'description' => __( 'Subscriber' ),
			'example'     => true,
			'resolver'    => function() {
				$roles = array_map( function ( $role ) {
					$role_object = get_role( $role );
					return translate_user_role( ucfirst( $role_object->name ) ) ;
				}, $this->trigger->user_object->roles );

				return implode( ', ', $roles );
			},
		) );

    	parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->user_object->roles );
	}

}
