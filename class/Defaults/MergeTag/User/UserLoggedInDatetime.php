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
			'slug'        => 'user_logged_in_datetime',
			'name'        => __( 'User login time' ),
			'description' => __( 'Will be resolved to a user login time' ),
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_format . ' ' . $this->trigger->time_format );
			},
        ) );

    }

}
