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
     *
     * @param string $slug merge tag slug.
     * @param string $name merge tag name.
     */
    public function __construct( $slug = 'user_email', $name = '' ) {

    	if ( empty( $name ) ) {
    		$name = __( 'User email' );
    	}

    	parent::__construct( array(
			'slug'        => $slug,
			'name'        => $name,
			'description' => __( 'john.doe@example.com' ),
			'example'     => true,
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
