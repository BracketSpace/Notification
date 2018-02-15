<?php
/**
 * User registered merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User registered merge tag class
 */
class UserRegistered extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'user_registered_datetime',
			'name'        => __( 'User registration date' ),
			'description' => __( '2018-02-14 15:36:00' ),
			'resolver'    => function() {
				return $this->trigger->user_object->user_registered;
			}
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->user_object->user_registered );
	}

}
