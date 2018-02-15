<?php
/**
 * User nicename merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User nicename merge tag class
 */
class UserNicename extends StringTag {

	/**
     * Constructor
     *
     * @param string $slug merge tag slug.
     * @param string $name merge tag name.
     */
    public function __construct( $slug = 'user_nicename', $name = '' ) {

    	if ( empty( $name ) ) {
    		$name = __( 'User nicename' );
    	}

    	parent::__construct( array(
			'slug'        => $slug,
			'name'        => $name,
			'description' => __( 'Johhnie' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->user_nicename;
			}
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->user_object->user_nicename );
	}

}
