<?php
/**
 * User logout merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User logout merge tag class
 */
class UserLogoutDatetime extends StringTag {

    /**
     * Constructor
     *
     * @param string $date_time_format format for date example.
     */
    public function __construct( $date_time_format = 'Y-m-d H:i:s' ) {

    	parent::__construct( array(
			'slug'        => 'user_logout_datetime',
			'name'        => __( 'User logout time' ),
			'description' => date_i18n( $date_time_format ),
			'example'     => true,
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_time_format );
			},
        ) );

    }

}
