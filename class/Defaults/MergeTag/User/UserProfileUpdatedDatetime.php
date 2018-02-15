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
     *
     * @param string $date_time_format format for date example.
     */
    public function __construct( $date_time_format = 'Y-m-d H:i:s' ) {

    	parent::__construct( array(
			'slug'        => 'user_profile_updated_datetime',
			'name'        => __( 'User profile update time' ),
			'description' => date_i18n( $date_time_format ),
			'example'     => true,
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_time_format );
			},
        ) );

    }

}
