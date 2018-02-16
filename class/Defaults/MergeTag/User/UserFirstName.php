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
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'user_first_name',
			'name'        => __( 'User first name' ),
			'description' => __( 'John' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->first_name;
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
        return isset( $this->trigger->user_object->first_name );
    }

}
