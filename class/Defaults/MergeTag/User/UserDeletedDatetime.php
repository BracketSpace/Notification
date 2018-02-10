<?php

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

class UserDeletedDatetime extends StringTag {

    private $trigger;

    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_deleted_datetime',
			'name'        => __( 'User deletion time' ),
			'description' => __( 'Will be resolved to a user deletion time' ),
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_format . ' ' . $this->trigger->time_format );
			}
        ) );

    }

}
