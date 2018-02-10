<?php

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserNicename extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_nicename',
			'name'        => __( 'User nicename' ),
			'description' => __( 'Will be resolved to a user nicename' ),
			'resolver'    => function() {
				return $this->trigger->user_object->user_nicename;
			}
        ) );

    }

    public function check_requirements( ) {

        return isset( $this->trigger->user_object->user_nicename );

    }

}
