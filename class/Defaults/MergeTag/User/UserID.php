<?php

namespace underDEV\Notification\Defaults\MergeTag\User;
use underDEV\Notification\Defaults\MergeTag\IntegerTag;

class UserID extends IntegerTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_ID',
			'name'        => __( 'User ID' ),
			'description' => __( 'Will be resolved to a user ID' ),
			'resolver'    => function() {
				return $this->trigger->user_object->ID;
			}
        ) );

    }

    public function check_requirements( ) {

        return isset( $this->trigger->user_object->ID );

    }

}
