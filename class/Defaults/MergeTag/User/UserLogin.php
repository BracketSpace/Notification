<?php

namespace underDEV\Notification\Defaults\MergeTag\User;
use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserLogin extends StringTag {

    public function __construct( $trigger ) {

    	parent::__construct( array(
			'slug'        => 'user_email',
			'name'        => __( 'User email' ),
			'description' => __( 'Will be resolved to a user email' ),
			'resolver'    => function() {
				return $trigger->user_object->user_email;
			}
        ) );

    }

    public function check_requirements(  ) {

        

    }

}
