<?php
/**
 * User login merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User login merge tag class
 */
class UserLogin extends StringTag {

	/**
	 * Receives Trigger object from Trigger class
	 *
	 * @var private object $trigger
	 */
    private $trigger;

    /**
     * Constructor
     *
     * @param object $trigger Trigger object to access data from.
     */
    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_login',
			'name'        => __( 'User login' ),
			'description' => __( 'Will be resolved to a user login' ),
			'resolver'    => function() {
				return $this->trigger->user_object->user_login;
			},
        ) );

    }

    /**
     * Function for checking requirements
     *
     * @return boolean
     */
    public function check_requirements( ) {

        return isset( $this->trigger->user_object->user_login );

    }

}
