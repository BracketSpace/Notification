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
	 * Receives Trigger object from Trigger class
	 *
	 * @var private object $trigger
	 */
	protected $trigger;

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct( $trigger ) {

		$this->trigger = $trigger;

		parent::__construct( array(
			'slug'        => 'user_role',
			'name'        => __( 'User role' ),
			'description' => __( 'Will be resolved to a user role (Administrator, Subscriber etc.) ' ),
			'resolver'    => function() {
				$roles = array_map( function ( $role ) {
					$role_object = get_role( $role );
					return translate_user_role( ucfirst( $role_object->name ) ) ;
				}, $this->trigger->user_object->roles );

				return implode( ', ', $roles );
			}
		) );

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
