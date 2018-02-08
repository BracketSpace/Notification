<?php

namespace underDEV\Notification\Defaults\MergeTag\User;
use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserBio extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_bio',
			'name'        => __( 'User bio' ),
			'description' => __( 'Will be resolved to a user profile description.' ),
			'resolver'    => function() {
				return $this->trigger->user_object->description;
			}
        ) );

    }

    public function check_requirements( ) {

        return isset( $this->trigger->user_object->description );

    }

}
