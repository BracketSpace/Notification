<?php
/**
 * User Bio merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * User Bio merge tag class
 */
class UserBio extends StringTag {

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
			'slug'        => 'user_bio',
			'name'        => __( 'User bio' ),
			'description' => __( 'Will be resolved to a user profile description.' ),
			'resolver'    => function() {
				return $this->trigger->user_object->description;
			},
        ) );

    }

    /**
     * Function for checking requirements
     *
     * @return boolean
     */
    public function check_requirements( ) {

        return isset( $this->trigger->user_object->description );

    }

}
