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
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'user_nicename',
			'name'        => __( 'User nicename' ),
			'description' => __( 'Johhnie' ),
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
