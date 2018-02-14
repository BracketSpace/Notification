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
     */
    public function __construct() {

    	parent::__construct( array(
			'slug'        => 'user_deleted_datetime',
			'name'        => __( 'User deletion time' ),
			'description' => __( 'Will be resolved to a user deletion time' ),
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_format . ' ' . $this->trigger->time_format );
			},
        ) );

    }

}
