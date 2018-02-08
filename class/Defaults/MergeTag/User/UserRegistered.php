<?php

namespace underDEV\Notification\Defaults\MergeTag\User;
use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserRegistered extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_registered_datetime',
			'name'        => __( 'User registration date' ),
			'description' => __( 'Will be resolved to a user registration date' ),
			'resolver'    => function() {
				return $this->trigger->user_object->user_registered;
			}
        ) );

    }

    public function check_requirements( ) {

        return isset( $this->trigger->user_object->user_registered );

    }

}
