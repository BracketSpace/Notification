<?php
/**
 * User Deleted Datetime merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User User Deleted Datetime merge tag class
 */
class UserDeletedDatetime extends StringTag {

    /**
     * Constructor
     *
     * @param string $date_time_format format for date example.
     */
    public function __construct( $date_time_format = 'Y-m-d H:i:s' ) {

    	parent::__construct( array(
			'slug'        => 'user_deleted_datetime',
			'name'        => __( 'User deletion time' ),
			'description' => date_i18n( $date_time_format ),
			'example'     => true,
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_format . ' ' . $this->trigger->time_format );
			},
        ) );

    }

}
