<?php
/**
 * User last name merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User last name merge tag class
 */
class UserLastName extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'user_last_name',
			'name'        => __( 'User last name' ),
			'description' => __( 'Doe' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->user_object->last_name;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->user_object->last_name );
	}

}
