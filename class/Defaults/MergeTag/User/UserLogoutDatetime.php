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
	 * Receives Trigger object from Trigger class
	 *
	 * @var private object $trigger
	 */
    protected $trigger;

    /**
     * Constructor
     *
     * @param object $trigger Trigger object to access data from.
     */
    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_logout_datetime',
			'name'        => __( 'User logout time' ),
			'description' => __( 'Will be resolved to a user logout time' ),
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_format . ' ' . $this->trigger->time_format );
			},
        ) );

    }

}
