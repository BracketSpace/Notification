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
			'slug'        => 'user_registered_datetime',
			'name'        => __( 'User registration date' ),
			'description' => __( 'Will be resolved to a user registration date' ),
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
