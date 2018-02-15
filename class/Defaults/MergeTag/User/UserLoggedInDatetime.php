<?php
/**
 * User logged in datetime merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User logged in datetime merge tag class
 */
class UserLoggedInDatetime extends StringTag {

    /**
     * Constructor
     */
    public function __construct() {

    	parent::__construct( array(
			'slug'        => 'user_logged_in_datetime',
			'name'        => __( 'User login time' ),
			'description' => __( 'Will be resolved to a user login time' ),
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_format . ' ' . $this->trigger->time_format );
			},
        ) );

    }

}
