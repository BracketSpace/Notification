<?php

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserLogin extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_login',
			'name'        => __( 'User login' ),
			'description' => __( 'Will be resolved to a user login' ),
			'resolver'    => function() {
				return $this->trigger->user_object->user_login;
			}
        ) );

    }

    public function check_requirements( ) {
            
        return isset( $this->trigger->user_object->user_email );

    }

}
