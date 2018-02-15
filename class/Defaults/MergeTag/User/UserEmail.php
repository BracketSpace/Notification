<?php
/**
 * User Email merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User Email merge tag class
 */
class UserEmail extends StringTag {

    /**
     * Constructor
     */
    public function __construct() {

    	parent::__construct( array(
			'slug'        => 'user_email',
			'name'        => __( 'User email' ),
			'description' => __( 'Will be resolved to a user email' ),
			'resolver'    => function() {
				return $this->trigger->user_object->user_email;
			},
        ) );

    }

    /**
     * Function for checking requirements
     *
     * @return boolean
     */
    public function check_requirements( ) {
        return isset( $this->trigger->user_object->user_email );
    }

}
