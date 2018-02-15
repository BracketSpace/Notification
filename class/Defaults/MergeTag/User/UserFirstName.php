<?php
/**
 * User first name merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User first name merge tag class
 */
class UserFirstName extends StringTag {

    /**
     * Constructor
     */
    public function __construct() {

    	parent::__construct( array(
			'slug'        => 'user_first_name',
			'name'        => __( 'User first name' ),
			'description' => __( 'John' ),
			'resolver'    => function() {
				return $this->trigger->user_object->first_name;
			},
        ) );

    }

    /**
     * Function for checking requirements
     *
     * @return boolean
     */
    public function check_requirements( ) {
        return isset( $this->trigger->user_object->first_name );
    }

}
