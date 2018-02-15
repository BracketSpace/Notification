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
     * Constructor
     *
     * @param string $slug merge tag slug.
     * @param string $name merge tag name.
     */
    public function __construct( $slug = 'user_ID', $name = '' ) {

    	if ( empty( $name ) ) {
    		$name = __( 'User ID' );
    	}

    	parent::__construct( array(
			'slug'        => $slug,
			'name'        => $name,
			'description' => '25',
			'example'     => true,
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
