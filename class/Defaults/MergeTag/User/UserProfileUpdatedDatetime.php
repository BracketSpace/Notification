<?php
/**
 * User profile updated datetime merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User profile updated datetime merge tag class
 */
class UserProfileUpdatedDatetime extends StringTag {

    /**
     * Constructor
     */
    public function __construct() {

    	parent::__construct( array(
			'slug'        => 'user_profile_updated_datetime',
			'name'        => __( 'User profile update time' ),
			'description' => __( '2018-02-14 15:36:00' ),
			'resolver'    => function() {
				return date( $this->trigger->date_format . ' ' . $this->trigger->time_format );
			},
        ) );

    }

}
