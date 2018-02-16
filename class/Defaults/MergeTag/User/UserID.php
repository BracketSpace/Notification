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
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'user_ID',
			'name'        => __( 'User ID' ),
			'description' => '25',
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->ID;
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
        return isset( $this->trigger->user_object->ID );
    }

}
