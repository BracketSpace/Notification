<?php
/**
 * User registered merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User registered merge tag class
 */
class UserRegistered extends StringTag {

	/**
	 * Constructor
     *
     * @param string $date_time_format format for date example.
	 */
	public function __construct( $date_time_format = 'Y-m-d H:i:s' ) {

		parent::__construct( array(
			'slug'        => 'user_registered_datetime',
			'name'        => __( 'User registration date' ),
			'description' => date_i18n( $date_time_format ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->user_registered;
			}
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->user_object->user_registered );
	}

}
