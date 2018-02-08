<?php

namespace underDEV\Notification\Defaults\MergeTag\User;
use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserRole extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_role',
			'name'        => __( 'User role' ),
			'description' => __( 'Will be resolved to a user role (Administrator, Subscriber etc.) ' ),
			'resolver'    => function() {
				return ucfirst( $this->trigger->user_object->roles[0] );
			}
        ) );

    }

    public function check_requirements( ) {

        return isset( $this->trigger->user_object->roles );

    }

}
