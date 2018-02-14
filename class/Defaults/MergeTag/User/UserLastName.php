<?php
/**
 * User last name merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User last name merge tag class
 */
class UserLastName extends StringTag {

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
			'slug'        => 'user_last_name',
			'name'        => __( 'User last name' ),
			'description' => __( 'Will be resolved to a user last name.' ),
			'resolver'    => function() {
				return $this->trigger->user_object->last_name;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->user_object->last_name );

	}

}
