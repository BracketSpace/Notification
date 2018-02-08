<?php

namespace underDEV\Notification\Defaults\MergeTag\User;
use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserFirstName extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_first_name',
			'name'        => __( 'User first name' ),
			'description' => __( 'Will be resolved to a user first name' ),
			'resolver'    => function() {
				return $this->trigger->user_object->first_name;
			}
        ) );

    }

    public function check_requirements( ) {

        return isset( $this->trigger->user_object->first_name );

    }

}
