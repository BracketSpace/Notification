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
     *
     * @param string $slug merge tag slug.
     * @param string $name merge tag name.
     */
    public function __construct( $slug = 'user_first_name', $name = '' ) {

    	if ( empty( $name ) ) {
    		$name = __( 'User first name' );
    	}

    	parent::__construct( array(
			'slug'        => $slug,
			'name'        => $name,
			'description' => __( 'John' ),
			'example'     => true,
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
