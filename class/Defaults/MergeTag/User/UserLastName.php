<?php

namespace underDEV\Notification\Defaults\MergeTag\User;
use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserLastName extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_last_name',
			'name'        => __( 'User last name' ),
			'description' => __( 'Will be resolved to a user last name.' ),
			'resolver'    => function() {
				return $this->trigger->user_object->last_name;
			}
        ) );

    }

    public function check_requirements( ) {

        return isset( $this->trigger->user_object->last_name );

    }

}
