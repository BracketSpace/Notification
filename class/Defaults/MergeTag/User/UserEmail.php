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
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'user_email',
			'name'        => __( 'User email' ),
			'description' => __( 'john.doe@example.com' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->user_email;
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
        return isset( $this->trigger->user_object->user_email );
    }

}
