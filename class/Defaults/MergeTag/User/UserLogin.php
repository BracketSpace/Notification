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
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'user_login',
			'name'        => __( 'User login' ),
			'description' => __( 'johndoe' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->user_login;
			},
		) );

    	parent::__construct( $args );

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
