<?php
/**
 * User ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;

/**
 * User ID merge tag class
 */
class UserID extends IntegerTag {

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
			'slug'        => 'user_ID',
			'name'        => __( 'User ID' ),
			'description' => __( 'Will be resolved to a user ID' ),
			'resolver'    => function() {
				return $this->trigger->user_object->ID;
			}
        ) );

    }

    /**
     * Function for checking requirements
     *
     * @return boolean
     */
    public function check_requirements( ) {

        return isset( $this->trigger->user_object->ID );

    }

}
