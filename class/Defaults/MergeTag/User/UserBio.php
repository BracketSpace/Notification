<?php
/**
 * User Bio merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User Bio merge tag class
 */
class UserBio extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'user_bio',
			'name'        => __( 'User bio' ),
			'description' => __( 'Developer based in Ontario, Canada' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->description;
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
        return isset( $this->trigger->user_object->description );
    }

}
